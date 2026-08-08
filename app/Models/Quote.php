<?php

namespace App\Models;

use App\Support\CanonicalServiceCatalog;
use App\Support\DevisPricing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model
{
    protected $fillable = [
        'first_name',
        'name',
        'email',
        'phone',
        'country',
        'city',
        'client_address',
        'project_type',
        'description',
        'budget_range',
        'timeline',
        'attachments',
        'status',
        'admin_notes',
        'quote_number',
        'valid_until',
        'devis_date',
        'rates',
        'product_notes',
        'subtotal',
        'tax_rate',
        'show_tax',
        'tax_amount',
        'discount',
        'total',
    ];

    protected $casts = [
        'attachments' => 'array',
        'rates' => 'array',
        'valid_until' => 'date',
        'devis_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'show_tax' => 'boolean',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class)->orderBy('order');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public static function generateQuoteNumber(): string
    {
        $year = date('Y');
        $prefix = sprintf('DEV-%s-', $year);
        $lastQuoteNumber = static::query()
            ->where('quote_number', 'like', $prefix.'%')
            ->orderByDesc('quote_number')
            ->value('quote_number');

        $sequence = $lastQuoteNumber
            ? (int) substr($lastQuoteNumber, -4) + 1
            : 1;

        return sprintf('DEV-%s-%04d', $year, $sequence);
    }

    public function calculateTotals(): void
    {
        $subtotal = (float) $this->items()->sum('total');
        $discount = (float) ($this->discount ?? 0);

        // Devis are quoted HTVA by default — the paper ones go straight from
        // Total to Remise to "Net a payer". TVA is opted into per devis.
        $taxRate = $this->show_tax ? (float) ($this->tax_rate ?? 19) : 0.0;

        $taxAmount = ($subtotal - $discount) * ($taxRate / 100);
        $total = $subtotal - $discount + $taxAmount;

        $this->forceFill([
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($total, 2),
        ])->save();
    }

    /**
     * The devis rates, normalized. Falls back to the workshop's usual joinery
     * rates so an older quote still prints a coherent legend.
     *
     * @return array<int, array{label: string, price: float, supplement: float, supplement_label: ?string}>
     */
    public function devisRates(): array
    {
        $rates = DevisPricing::normalizeRates($this->rates);

        return $rates !== [] ? $rates : DevisPricing::normalizeRates(DevisPricing::DEFAULT_RATES);
    }

    /**
     * Whether any line carries a shutter price. Drives the column layout of
     * both documents: with shutters the table splits into P.VOLET / P.ALU.
     */
    public function hasShutterPricing(): bool
    {
        return $this->items->contains(fn (QuoteItem $item): bool => $item->hasShutter());
    }

    /**
     * @return array<int, array{label: string, price: float}>
     */
    public function rateLegend(): array
    {
        return DevisPricing::legendLines($this->devisRates());
    }

    /**
     * The "Information sur produit" block, one bullet per non-empty line.
     *
     * @return array<int, string>
     */
    public function productNoteLines(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->product_notes))
            ->map(fn (string $line): string => trim(ltrim($line, "*\t ")))
            ->filter()
            ->values()
            ->all();
    }

    public function documentDate(): \Illuminate\Support\Carbon
    {
        return $this->devis_date ?? $this->created_at ?? now();
    }

    public function documentFilename(string $extension): string
    {
        $reference = $this->quote_number ?: 'BROUILLON-'.$this->id;

        return 'Devis-'.$reference.'.'.$extension;
    }

    public function markAsContacted(): void
    {
        $this->update(['status' => 'contacted']);
    }

    public function markAsQuoted(): void
    {
        if (! $this->quote_number) {
            $this->quote_number = static::generateQuoteNumber();
        }
        $this->status = 'quoted';
        $this->save();
    }

    public function markAsAccepted(): void
    {
        $this->update(['status' => 'accepted']);
    }

    public function markAsRejected(): void
    {
        $this->update(['status' => 'rejected']);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function getFullNameAttribute(): string
    {
        $fullName = trim(implode(' ', array_filter([$this->first_name, $this->name])));

        return $fullName !== '' ? $fullName : $this->name;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new' => '🆕 Nouveau',
            'contacted' => '📞 Contacté',
            'quoted' => '📋 Devis envoyé',
            'accepted' => '✅ Accepté',
            'rejected' => '❌ Refusé',
            'completed' => '🎉 Terminé',
            default => $this->status,
        };
    }

    public function createInvoice(): Invoice
    {
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'quote_id' => $this->id,
            'client_name' => $this->full_name,
            'client_email' => $this->email,
            'client_phone' => $this->phone,
            'client_address' => $this->client_address,
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $this->subtotal,
            // Mirror the devis: invoicing a quote issued without TVA must not
            // silently add 19% to what the client accepted.
            'tax_rate' => $this->show_tax ? ($this->tax_rate ?? 19) : 0,
            'tax_amount' => $this->tax_amount,
            'discount' => $this->discount ?? 0,
            'total' => $this->total,
            'status' => 'draft',
        ]);

        // Copy quote items to invoice items. An invoice line carries a single
        // price, so a shutter is folded into the unit price it was quoted with.
        foreach ($this->items as $item) {
            $invoice->items()->create([
                'description' => $item->description,
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'unit_price' => round((float) $item->unit_price + (float) $item->shutter_price, 2),
                'total' => $item->total,
                'order' => $item->order,
            ]);
        }

        return $invoice;
    }

    public static function projectTypeOptions(?string $locale = null): array
    {
        return CanonicalServiceCatalog::quoteOptions($locale);
    }

    public static function projectTypeLabel(string $projectType, ?string $locale = null): string
    {
        return CanonicalServiceCatalog::labelFor($projectType, $locale);
    }
}

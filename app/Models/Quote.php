<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'city',
        'project_type',
        'description',
        'budget_range',
        'timeline',
        'attachments',
        'status',
        'admin_notes',
        'quote_number',
        'valid_until',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount',
        'total',
    ];

    protected $casts = [
        'attachments' => 'array',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
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
            ->where('quote_number', 'like', $prefix . '%')
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
        $taxRate = (float) ($this->tax_rate ?? 19);

        $taxAmount = ($subtotal - $discount) * ($taxRate / 100);
        $total = $subtotal - $discount + $taxAmount;

        $this->forceFill([
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($total, 2),
        ])->save();
    }

    public function markAsContacted(): void
    {
        $this->update(['status' => 'contacted']);
    }

    public function markAsQuoted(): void
    {
        if (!$this->quote_number) {
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

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
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
            'client_name' => $this->name,
            'client_email' => $this->email,
            'client_phone' => $this->phone,
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $this->subtotal,
            'tax_rate' => $this->tax_rate ?? 19,
            'tax_amount' => $this->tax_amount,
            'discount' => $this->discount ?? 0,
            'total' => $this->total,
            'status' => 'draft',
        ]);

        // Copy quote items to invoice items
        foreach ($this->items as $item) {
            $invoice->items()->create([
                'description' => $item->description,
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
                'order' => $item->order,
            ]);
        }

        return $invoice;
    }
}

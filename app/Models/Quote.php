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
        $lastQuote = static::whereYear('created_at', $year)
            ->whereNotNull('quote_number')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastQuote && $lastQuote->quote_number 
            ? (int)substr($lastQuote->quote_number, -4) + 1 
            : 1;
        
        return sprintf('DEV-%s-%04d', $year, $sequence);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total');
        $discount = $this->discount ?? 0;
        $taxRate = $this->tax_rate ?? 19;
        $this->tax_amount = ($this->subtotal - $discount) * ($taxRate / 100);
        $this->total = $this->subtotal - $discount + $this->tax_amount;
        $this->save();
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

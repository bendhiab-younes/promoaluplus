<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'quote_id',
        'client_name',
        'client_email',
        'client_phone',
        'client_address',
        'client_tax_id',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount',
        'total',
        'status',
        'notes',
        'terms',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('order');
    }

    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return sprintf('FAC-%s-%04d', $year, $sequence);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total');
        $this->tax_amount = ($this->subtotal - $this->discount) * ($this->tax_rate / 100);
        $this->total = $this->subtotal - $this->discount + $this->tax_amount;
        $this->save();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => '📝 Brouillon',
            'sent' => '📤 Envoyée',
            'paid' => '✅ Payée',
            'overdue' => '⚠️ En retard',
            'cancelled' => '❌ Annulée',
            default => $this->status,
        };
    }
}

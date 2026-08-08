<?php

namespace App\Models;

use App\Support\DevisPricing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id',
        'description',
        'height',
        'width',
        'unit',
        'rate_label',
        'shutter_rate_label',
        'quantity',
        'unit_price',
        'shutter_price',
        'total',
        'order',
    ];

    protected $casts = [
        'height' => 'decimal:3',
        'width' => 'decimal:3',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'shutter_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function getAreaAttribute(): float
    {
        return DevisPricing::area($this->height, $this->width);
    }

    public function hasShutter(): bool
    {
        return (float) $this->shutter_price > 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (QuoteItem $item) {
            if ((float) $item->height > 0 && (float) $item->width > 0) {
                $item->unit = 'm²';
            }

            $item->total = DevisPricing::lineTotal($item->unit_price, $item->shutter_price, $item->quantity);
        });
    }
}

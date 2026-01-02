<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'question' => 'array',
        'answer' => 'array',
        'is_active' => 'boolean',
    ];

    public function getTranslatedQuestion(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->question[$locale] ?? $this->question['fr'] ?? '';
    }

    public function getTranslatedAnswer(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->answer[$locale] ?? $this->answer['fr'] ?? '';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}

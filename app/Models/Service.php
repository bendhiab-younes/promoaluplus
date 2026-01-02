<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'short_description',
        'description',
        'icon',
        'color',
        'image',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'array',
        'short_description' => 'array',
        'description' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function getTranslatedTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->title[$locale] ?? $this->title['fr'] ?? '';
    }

    public function getTranslatedShortDescription(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->short_description[$locale] ?? $this->short_description['fr'] ?? '';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'is_active' => 'boolean',
    ];

    public function getTranslatedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->name[$locale] ?? $this->name['fr'] ?? '';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}

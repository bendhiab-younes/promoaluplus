<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'client_name',
        'client_location',
        'client_photo',
        'content',
        'rating',
        'project_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
    ];

    public function getTranslatedContent(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->content[$locale] ?? $this->content['fr'] ?? '';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

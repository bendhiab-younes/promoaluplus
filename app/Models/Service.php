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
        'svg_icon',
        'color',
        'image',
        'gallery',
        'features',
        'materials',
        'specs',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'array',
        'short_description' => 'array',
        'description' => 'array',
        'features' => 'array',
        'gallery' => 'array',
        'materials' => 'array',
        'specs' => 'array',
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

    public function getTranslatedDescription(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->description[$locale] ?? $this->description['fr'] ?? '';
    }

    /**
     * Get translated features array
     */
    public function getTranslatedFeatures(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $features = $this->features ?? [];
        
        return array_map(function ($feature) use ($locale) {
            if (is_array($feature)) {
                return $feature[$locale] ?? $feature['fr'] ?? '';
            }
            return $feature;
        }, $features);
    }

    /**
     * Get translated materials array
     */
    public function getTranslatedMaterials(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $materials = $this->materials ?? [];
        
        return array_map(function ($material) use ($locale) {
            if (is_array($material)) {
                return $material[$locale] ?? $material['fr'] ?? '';
            }
            return $material;
        }, $materials);
    }

    /**
     * Get gallery images with fallback to main image
     */
    public function getGalleryImages(): array
    {
        $gallery = $this->gallery ?? [];
        
        if (empty($gallery) && $this->image) {
            return [$this->image];
        }
        
        return $gallery;
    }

    /**
     * Get the main/featured image
     */
    public function getFeaturedImage(): ?string
    {
        $gallery = $this->getGalleryImages();
        return $gallery[0] ?? $this->image ?? null;
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

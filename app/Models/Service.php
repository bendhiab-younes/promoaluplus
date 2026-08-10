<?php

namespace App\Models;

use App\Models\Concerns\HasImageSource;
use App\Support\MediaPath;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasImageSource;

    public const DEFAULT_ICON_BY_SLUG = [
        'kitchen' => 'chef-hat',
        'doors' => 'door-open',
        'windows' => 'app-window',
        'rolling_shutters' => 'blinds',
        'railings' => 'shield',
        'pergola' => 'sun-dim',
        'sun_breakers' => 'sun-medium',
        'mosquito_nets' => 'bug',
        'space_design' => 'layout-grid',
    ];

    public const DEFAULT_COLOR_BY_SLUG = [
        'kitchen' => 'rose',
        'doors' => 'orange',
        'windows' => 'blue',
        'rolling_shutters' => 'violet',
        'railings' => 'emerald',
        'pergola' => 'amber',
        'sun_breakers' => 'yellow',
        'mosquito_nets' => 'teal',
        'space_design' => 'indigo',
    ];

    protected $fillable = [
        'slug',
        'title',
        'short_description',
        'description',
        'icon',
        'svg_icon',
        'color',
        'image',
        'image_url',
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
     * Gallery image URLs, falling back to the main image when empty.
     *
     * @return array<int, string>
     */
    public function getGalleryImages(): array
    {
        $gallery = array_values(array_filter(
            array_map(
                static fn ($image) => is_string($image) ? MediaPath::url($image) : null,
                $this->gallery ?? []
            )
        ));

        if ($gallery === [] && ($main = $this->imageSrc()) !== null) {
            return [$main];
        }

        return $gallery;
    }

    public function getFeaturedImage(): ?string
    {
        return $this->getGalleryImages()[0] ?? $this->imageSrc();
    }

    public function getDisplayIcon(): string
    {
        if (is_string($this->icon) && trim($this->icon) !== '') {
            return $this->icon;
        }

        return self::DEFAULT_ICON_BY_SLUG[$this->slug] ?? 'wrench';
    }

    public function getDisplayColor(): string
    {
        if (is_string($this->color) && trim($this->color) !== '') {
            return $this->color;
        }

        return self::DEFAULT_COLOR_BY_SLUG[$this->slug] ?? 'blue';
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

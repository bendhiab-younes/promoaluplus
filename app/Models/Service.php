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

    /**
     * First-install `svg_icon` values, for services Lucide has no icon for.
     *
     * Lucide ships no mosquito (checked through 0.544) and its nearest match,
     * `bug`, draws a beetle. So the glyph is drawn here in Lucide's own grammar —
     * 24x24 box, `currentColor` stroke, 2px round caps, no intrinsic size — which
     * is what lets `SafeHtml::svgIcon()` size and colour it per render site.
     *
     * These are seeded into the row rather than used as a runtime fallback on
     * purpose: the point is that the SVG shows up in the admin panel's field,
     * where it can be edited or replaced without touching this file.
     */
    public const DEFAULT_SVG_ICON_BY_SLUG = [
        'mosquito_nets' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.8" cy="12.3" r="1.9"/><path d="M4 13 1 14.8"/><path d="M7.7 13.2c3.6 1.1 6.7 3.2 9.4 6.2"/><path d="M9.6 11.2c1.8-4 5.2-6.4 10.2-7.2-.6 4.3-3.6 7.1-8.9 8.3Z"/><path d="M10.7 12.3c3-2.6 7-3.4 11.6-2.4-2.6 3.3-6.2 4.5-10.6 3.6Z"/><path d="M8 14.6 5.6 18 2.4 18.6"/><path d="M11.5 15.4 10 19.4 6.6 20.6"/></svg>',
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

    /**
     * Accent hex for each `DEFAULT_COLOR_BY_SLUG` value.
     *
     * The public pages need real hex to build gradients through CSS custom
     * properties, and the admin panel needs the same values to preview an icon
     * against the tile it will actually sit on. Two copies drifting apart would
     * make the preview lie, so both read this one.
     */
    public const ACCENT_HEX_BY_COLOR = [
        'rose' => '#e11d48',
        'orange' => '#ea580c',
        'blue' => '#2563eb',
        'violet' => '#7c3aed',
        'emerald' => '#059669',
        'amber' => '#d97706',
        'yellow' => '#ca8a04',
        'teal' => '#0d9488',
        'indigo' => '#4f46e5',
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
     * Gallery image URLs, with the main image (thumbnail) always first.
     *
     * The main image and the gallery are stored in separate columns, so an
     * admin who sets a thumbnail and gallery images independently expects
     * both to show up together, thumbnail first — not one to silently hide
     * the other.
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

        $main = $this->imageSrc();

        if ($main === null) {
            return $gallery;
        }

        return array_values(array_unique([$main, ...$gallery]));
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

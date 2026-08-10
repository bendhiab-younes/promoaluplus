<?php

namespace App\Models;

use App\Models\Concerns\HasImageSource;
use App\Providers\ViewServiceProvider;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasImageSource;

    public const CTA_TYPES = ['quote', 'services', 'portfolio', 'contact', 'custom', 'none'];

    public const ACCENT_COLORS = ['orange', 'blue', 'cyan', 'emerald'];

    protected $fillable = [
        'image', 'image_url', 'alt_text', 'badge', 'badge_icon', 'title', 'highlight',
        'description', 'cta_type', 'cta_url', 'cta_label', 'show_whatsapp',
        'accent_color', 'image_fit', 'image_zoom', 'focal_x', 'focal_y',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'alt_text' => 'array',
        'badge' => 'array',
        'title' => 'array',
        'highlight' => 'array',
        'description' => 'array',
        'cta_label' => 'array',
        'show_whatsapp' => 'boolean',
        'is_active' => 'boolean',
        'image_zoom' => 'integer',
        'focal_x' => 'integer',
        'focal_y' => 'integer',
    ];

    public function getTranslatedTitle(?string $locale = null): string
    {
        return $this->translate('title', $locale);
    }

    public function getTranslatedHighlight(?string $locale = null): string
    {
        return $this->translate('highlight', $locale);
    }

    public function getTranslatedDescription(?string $locale = null): string
    {
        return $this->translate('description', $locale);
    }

    public function getTranslatedBadge(?string $locale = null): string
    {
        return $this->translate('badge', $locale);
    }

    public function getTranslatedCtaLabel(?string $locale = null): string
    {
        return $this->translate('cta_label', $locale);
    }

    public function getTranslatedAltText(?string $locale = null): string
    {
        return $this->translate('alt_text', $locale);
    }

    private function translate(string $attribute, ?string $locale): string
    {
        $locale = $locale ?? app()->getLocale();
        $values = $this->{$attribute} ?? [];

        return $values[$locale] ?? $values['fr'] ?? '';
    }

    /**
     * Resolves the CTA target. A slide pointing at the portfolio degrades to
     * the services page while that page is hidden, so the button is never dead.
     */
    public function ctaUrl(): ?string
    {
        return match ($this->cta_type) {
            'quote', 'contact' => route('contact'),
            'services' => route('services'),
            'portfolio' => ViewServiceProvider::portfolioEnabled() ? route('portfolio') : route('services'),
            'custom' => filled($this->cta_url) ? $this->cta_url : null,
            default => null,
        };
    }

    /** Inline style implementing the admin's zoom and focal-point settings. */
    public function imageStyle(): string
    {
        $scale = max(100, min(200, $this->image_zoom ?? 100)) / 100;

        return sprintf(
            'object-position: %d%% %d%%; transform: scale(%s);',
            $this->focal_x ?? 50,
            $this->focal_y ?? 50,
            rtrim(rtrim(number_format($scale, 2, '.', ''), '0'), '.')
        );
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

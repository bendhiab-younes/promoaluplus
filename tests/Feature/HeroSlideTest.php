<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroSlideTest extends TestCase
{
    use RefreshDatabase;

    private function slide(array $attributes = []): HeroSlide
    {
        return HeroSlide::create(array_merge([
            'title' => ['fr' => 'Titre FR', 'en' => 'Title EN', 'ar' => 'عنوان'],
            'highlight' => ['fr' => 'Accroche FR'],
            'description' => ['fr' => 'Description FR'],
            'badge' => ['fr' => 'Badge FR'],
            'cta_type' => 'contact',
            'accent_color' => 'orange',
            'image_fit' => 'cover',
            'image_zoom' => 100,
            'focal_x' => 50,
            'focal_y' => 50,
            'is_active' => true,
            'sort_order' => 1,
        ], $attributes));
    }

    public function test_translated_accessors_fall_back_to_french(): void
    {
        $slide = $this->slide();

        $this->assertSame('Titre FR', $slide->getTranslatedTitle('fr'));
        $this->assertSame('Title EN', $slide->getTranslatedTitle('en'));
        $this->assertSame('Accroche FR', $slide->getTranslatedHighlight('ar'));
    }

    public function test_the_cta_url_resolves_from_the_cta_type(): void
    {
        $this->assertSame(route('contact'), $this->slide(['cta_type' => 'contact'])->ctaUrl());
        $this->assertSame(route('services'), $this->slide(['cta_type' => 'services'])->ctaUrl());
        $this->assertSame(
            'https://example.test/x',
            $this->slide(['cta_type' => 'custom', 'cta_url' => 'https://example.test/x'])->ctaUrl()
        );
        $this->assertNull($this->slide(['cta_type' => 'none'])->ctaUrl());
    }

    public function test_the_portfolio_cta_falls_back_to_services_when_the_page_is_hidden(): void
    {
        $this->assertSame(route('services'), $this->slide(['cta_type' => 'portfolio'])->ctaUrl());
    }

    public function test_the_portfolio_cta_points_at_the_portfolio_when_the_page_is_visible(): void
    {
        \App\Models\SiteSetting::set('portfolio_enabled', '1', 'boolean', 'pages');

        $this->assertSame(route('portfolio'), $this->slide(['cta_type' => 'portfolio'])->ctaUrl());
    }

    public function test_active_and_ordered_scopes(): void
    {
        $this->slide(['sort_order' => 2]);
        $this->slide(['sort_order' => 1]);
        $this->slide(['sort_order' => 0, 'is_active' => false]);

        $this->assertSame([1, 2], HeroSlide::active()->ordered()->pluck('sort_order')->all());
    }

    public function test_the_image_style_encodes_zoom_and_focal_point(): void
    {
        $style = $this->slide(['image_zoom' => 150, 'focal_x' => 30, 'focal_y' => 70])->imageStyle();

        $this->assertStringContainsString('object-position: 30% 70%', $style);
        $this->assertStringContainsString('scale(1.5)', $style);
    }

    public function test_the_zoom_is_clamped_to_the_supported_range(): void
    {
        $this->assertStringContainsString('scale(1)', $this->slide(['image_zoom' => 50])->imageStyle());
        $this->assertStringContainsString('scale(2)', $this->slide(['image_zoom' => 400])->imageStyle());
    }

    public function test_the_image_source_prefers_the_upload_over_the_external_url(): void
    {
        $slide = $this->slide(['image' => 'hero/slide-1.jpeg', 'image_url' => 'https://cdn.test/x.jpg']);
        $this->assertSame(asset('uploads/hero/slide-1.jpeg'), $slide->imageSrc());

        $external = $this->slide(['image' => null, 'image_url' => 'https://cdn.test/x.jpg']);
        $this->assertSame('https://cdn.test/x.jpg', $external->imageSrc());
    }
}

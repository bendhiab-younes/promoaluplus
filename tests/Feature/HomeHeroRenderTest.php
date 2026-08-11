<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The homepage hero is driven entirely by hero_slides rows, so the markup has
 * to hold up at any slide count an admin can produce — including none.
 */
class HomeHeroRenderTest extends TestCase
{
    use RefreshDatabase;

    private function slide(int $sortOrder, array $attributes = []): HeroSlide
    {
        return HeroSlide::create(array_merge([
            'title' => ['fr' => 'Titre '.$sortOrder],
            'description' => ['fr' => 'Description '.$sortOrder],
            'badge' => ['fr' => 'Badge '.$sortOrder],
            'cta_type' => 'contact',
            'cta_label' => ['fr' => 'Contact'],
            'image_url' => '/images/hero/slide-'.($sortOrder + 1).'.webp',
            'accent_color' => 'orange',
            'is_active' => true,
            'sort_order' => $sortOrder,
        ], $attributes));
    }

    public function test_it_renders_one_slide_per_active_record(): void
    {
        foreach (range(0, 3) as $sortOrder) {
            $this->slide($sortOrder);
        }

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertSame(4, substr_count($html, 'carousel-slide '));
        $this->assertSame(4, substr_count($html, 'carousel-dot '));
        $this->assertStringContainsString('carousel-prev', $html);
        $this->assertStringContainsString('Titre 3', $html);
    }

    public function test_only_the_first_slide_ships_a_real_src(): void
    {
        foreach (range(0, 3) as $sortOrder) {
            $this->slide($sortOrder);
        }

        $html = $this->get(route('home'))->assertOk()->getContent();

        preg_match_all('/<img[^>]*class="absolute inset-0 w-full h-full[^>]*>/', $html, $matches);
        $heroImages = $matches[0];

        $this->assertCount(4, $heroImages);
        // Every slide sits inside the viewport, so loading="lazy" would not stop
        // the browser fetching all four up front. Slides 2+ carry data-src and
        // the carousel script swaps it in just before the slide is shown.
        $this->assertStringContainsString('src="/images/hero/slide-1.webp"', $heroImages[0]);
        foreach (array_slice($heroImages, 1) as $image) {
            $this->assertStringContainsString('data-src=', $image);
            $this->assertStringNotContainsString(' src=', $image);
        }

        $this->assertStringContainsString('rel="preload" as="image" fetchpriority="high" href="/images/hero/slide-1.webp"', $html);
    }

    public function test_a_single_slide_renders_without_arrows_or_dots(): void
    {
        $this->slide(0);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'carousel-slide '));
        $this->assertStringNotContainsString('carousel-dot ', $html);
        $this->assertStringNotContainsString('carousel-prev', $html);
    }

    public function test_deactivating_every_slide_removes_the_hero_instead_of_leaving_a_black_band(): void
    {
        $this->slide(0, ['is_active' => false]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('home-hero-section', $html);
        $this->assertStringNotContainsString('rel="preload" as="image"', $html);
    }

    public function test_inactive_slides_are_excluded_and_order_follows_sort_order(): void
    {
        $this->slide(1, ['title' => ['fr' => 'Deuxième']]);
        $this->slide(0, ['title' => ['fr' => 'Première']]);
        $this->slide(2, ['title' => ['fr' => 'Cachée'], 'is_active' => false]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertSame(2, substr_count($html, 'carousel-slide '));
        $this->assertStringNotContainsString('Cachée', $html);
        $this->assertLessThan(strpos($html, 'Deuxième'), strpos($html, 'Première'));
    }
}

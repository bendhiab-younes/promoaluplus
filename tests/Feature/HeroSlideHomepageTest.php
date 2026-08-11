<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Walkthrough item: "set a slide to 150% zoom, focal 30/70, save, reload /;
 * set one to Entier and confirm the blurred backdrop appears." Framing
 * persistence and imageStyle() math are already covered at the model/Livewire
 * level (HeroSlideTest, HeroSlideResourceTest) — this file closes the last
 * hop: that a saved slide actually renders those values into the homepage
 * HTML, full stack, via a real GET /.
 */
class HeroSlideHomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_contain_fit_slide_renders_a_blurred_backdrop_image(): void
    {
        HeroSlide::create([
            'title' => ['fr' => 'Slide Entier'],
            'image_url' => 'https://images.example.test/slide.jpg',
            'image_fit' => 'contain',
            'cta_type' => 'contact',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('blur-2xl scale-110', $html);
        $this->assertStringContainsString('object-contain', $html);
    }

    public function test_a_cover_fit_slide_renders_no_blurred_backdrop_image(): void
    {
        HeroSlide::create([
            'title' => ['fr' => 'Slide Cover'],
            'image_url' => 'https://images.example.test/slide.jpg',
            'image_fit' => 'cover',
            'cta_type' => 'contact',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('blur-2xl scale-110', $html);
    }

    public function test_saved_zoom_and_focal_point_render_into_the_homepage_style(): void
    {
        HeroSlide::create([
            'title' => ['fr' => 'Slide Cadrée'],
            'image_url' => 'https://images.example.test/slide.jpg',
            'image_fit' => 'cover',
            'image_zoom' => 150,
            'focal_x' => 30,
            'focal_y' => 70,
            'cta_type' => 'contact',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('object-position: 30% 70%', $html);
        $this->assertStringContainsString('transform: scale(1.5)', $html);
    }
}

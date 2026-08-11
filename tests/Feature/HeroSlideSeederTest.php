<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\SiteSetting;
use Database\Seeders\HeroSlideSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroSlideSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_the_four_slides_that_were_hardcoded_in_the_homepage(): void
    {
        $this->seed(HeroSlideSeeder::class);

        $slides = HeroSlide::ordered()->get();

        $this->assertCount(4, $slides);
        $this->assertSame(
            ['/images/hero/slide-1.webp', '/images/hero/slide-2.webp', '/images/hero/slide-3.webp', '/images/hero/slide-4.webp'],
            $slides->pluck('image_url')->all()
        );
        $this->assertSame(['quote', 'services', 'portfolio', 'contact'], $slides->pluck('cta_type')->all());
        $this->assertSame([true, false, false, false], $slides->pluck('show_whatsapp')->all());

        // The committed hero images are copied onto the uploads disk so the
        // admin's FileUpload field has a real file to show and replace,
        // instead of an empty dropzone with nothing to drag a new photo onto.
        $this->assertSame(
            ['hero/slide-1.webp', 'hero/slide-2.webp', 'hero/slide-3.webp', 'hero/slide-4.webp'],
            $slides->pluck('image')->all()
        );
        $this->assertSame(asset('uploads/hero/slide-1.webp'), $slides->first()->imageSrc());
    }

    public function test_reseeding_backfills_the_uploaded_image_for_a_slide_seeded_before_this_existed(): void
    {
        // Simulates a row from before the seeder copied files onto the
        // uploads disk: image_url set, image left null.
        HeroSlide::create([
            'sort_order' => 0,
            'title' => ['fr' => 'Titre'],
            'cta_type' => 'quote',
            'image_url' => '/images/hero/slide-1.webp',
            'is_active' => true,
        ]);

        $this->seed(HeroSlideSeeder::class);

        $slide = HeroSlide::where('sort_order', 0)->firstOrFail();

        $this->assertSame('hero/slide-1.webp', $slide->image);
        $this->assertTrue(is_file(public_path('uploads/hero/slide-1.webp')));
    }

    public function test_the_backfill_never_overwrites_an_admin_uploaded_image(): void
    {
        $this->seed(HeroSlideSeeder::class);

        $slide = HeroSlide::where('sort_order', 0)->firstOrFail();
        $slide->update(['image' => 'hero/admin-chosen.jpg']);

        $this->seed(HeroSlideSeeder::class);

        $this->assertSame('hero/admin-chosen.jpg', $slide->refresh()->image);
    }

    public function test_it_carries_the_copy_across_all_three_locales(): void
    {
        $this->seed(HeroSlideSeeder::class);

        $second = HeroSlide::where('sort_order', 1)->firstOrFail();

        $this->assertSame('Sécurité Renforcée', $second->getTranslatedTitle('fr'));
        $this->assertSame('Enhanced Security', $second->getTranslatedTitle('en'));
        $this->assertSame('أمان معزز', $second->getTranslatedTitle('ar'));
    }

    public function test_the_first_slide_prefers_the_editable_hero_site_settings(): void
    {
        SiteSetting::set('hero_title_fr', 'Titre choisi par l\'admin');

        $this->seed(HeroSlideSeeder::class);

        $first = HeroSlide::where('sort_order', 0)->firstOrFail();

        $this->assertSame('Titre choisi par l\'admin', $first->getTranslatedTitle('fr'));
        // Locales the admin left blank still fall back to the lang files.
        $this->assertSame('Reliable Aluminum Solutions', $first->getTranslatedTitle('en'));
    }

    public function test_the_badge_keeps_the_short_brand_spelling(): void
    {
        SiteSetting::set('hero_badge_fr', 'Promo Alu Plus, votre partenaire');

        $this->seed(HeroSlideSeeder::class);

        $this->assertSame('PromoAlu+', HeroSlide::where('sort_order', 0)->firstOrFail()->getTranslatedBadge('fr'));
    }

    public function test_reseeding_does_not_overwrite_admin_edits(): void
    {
        $this->seed(HeroSlideSeeder::class);

        $slide = HeroSlide::where('sort_order', 0)->firstOrFail();
        $slide->update([
            'title' => ['fr' => 'Titre modifié', 'en' => 'Edited', 'ar' => 'عنوان'],
            'is_active' => false,
        ]);

        $this->seed(HeroSlideSeeder::class);

        $slide->refresh();

        $this->assertSame('Titre modifié', $slide->getTranslatedTitle('fr'));
        $this->assertFalse($slide->is_active);
        $this->assertSame(4, HeroSlide::count());
    }
}

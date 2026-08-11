<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use Database\Seeders\TestimonialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_the_three_reviews_that_were_hardcoded_in_the_homepage(): void
    {
        $this->seed(TestimonialSeeder::class);

        $testimonials = Testimonial::orderBy('sort_order')->get();

        $this->assertCount(3, $testimonials);
        $this->assertSame(['Mohamed B.', 'Sonia K.', 'Ahmed T.'], $testimonials->pluck('client_name')->all());
        $this->assertSame(
            ['Paris, France', 'Montréal, Canada', 'Berlin, Allemagne'],
            $testimonials->pluck('client_location')->all()
        );
        $this->assertSame([5, 5, 5], $testimonials->pluck('rating')->all());
        $this->assertSame([true, true, true], $testimonials->pluck('is_active')->all());

        // No photo is invented for these clients: both image columns stay empty
        // so the card falls back to the initial-letter avatar and an admin
        // upload later wins.
        $this->assertSame([null, null, null], $testimonials->pluck('client_photo')->all());
        $this->assertNull($testimonials->first()->photoSrc());
    }

    public function test_it_carries_the_quote_text_across_all_three_locales(): void
    {
        $this->seed(TestimonialSeeder::class);

        $first = Testimonial::where('client_name', 'Mohamed B.')->firstOrFail();

        $this->assertSame(trans('messages.testimonial_1', [], 'fr'), $first->getTranslatedContent('fr'));
        $this->assertSame(trans('messages.testimonial_1', [], 'en'), $first->getTranslatedContent('en'));
        $this->assertSame(trans('messages.testimonial_1', [], 'ar'), $first->getTranslatedContent('ar'));
    }

    public function test_reseeding_does_not_overwrite_admin_edits(): void
    {
        $this->seed(TestimonialSeeder::class);

        $testimonial = Testimonial::where('client_name', 'Sonia K.')->firstOrFail();
        $testimonial->update([
            'content' => ['fr' => 'Avis corrigé par l\'admin', 'en' => 'Edited', 'ar' => 'رأي'],
            'client_location' => 'Tunis, Tunisie',
            'is_active' => false,
        ]);

        $this->seed(TestimonialSeeder::class);

        $testimonial->refresh();

        $this->assertSame('Avis corrigé par l\'admin', $testimonial->getTranslatedContent('fr'));
        $this->assertSame('Tunis, Tunisie', $testimonial->client_location);
        $this->assertFalse($testimonial->is_active);
        $this->assertSame(3, Testimonial::count());
    }
}

<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Seeds the three reviews that were previously hardcoded in the homepage:
 * the quote text lived in lang/*, the client names and locations were baked
 * into home.blade.php markup. Both are consolidated here so the admin panel
 * is the single source of truth.
 *
 * Rows are created with firstOrCreate keyed on client_name: re-running the
 * seeder must never overwrite an admin's edits, the same rule HeroSlideSeeder
 * and PageController@contact already respect.
 */
class TestimonialSeeder extends Seeder
{
    private const LOCALES = ['fr', 'en', 'ar'];

    public function run(): void
    {
        foreach ($this->testimonials() as $testimonial) {
            Testimonial::firstOrCreate(
                ['client_name' => $testimonial['client_name']],
                $testimonial
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function testimonials(): array
    {
        return [
            [
                'client_name' => 'Mohamed B.',
                'client_location' => 'Paris, France',
                'content' => $this->lang('testimonial_1'),
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'client_name' => 'Sonia K.',
                'client_location' => 'Montréal, Canada',
                'content' => $this->lang('testimonial_2'),
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'client_name' => 'Ahmed T.',
                'client_location' => 'Berlin, Allemagne',
                'content' => $this->lang('testimonial_3'),
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];
    }

    /**
     * The quote text for every locale, read from the lang files that used to
     * feed the markup directly.
     *
     * @return array<string, string>
     */
    private function lang(string $messageKey): array
    {
        $values = [];

        foreach (self::LOCALES as $locale) {
            $values[$locale] = (string) trans("messages.{$messageKey}", [], $locale);
        }

        return $values;
    }
}

<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Seeds the four slides that were previously hardcoded in the homepage
 * carousel, so an admin can edit them instead of a developer.
 *
 * Rows are created with firstOrCreate keyed on sort_order: re-running the
 * seeder must never overwrite an admin's edits, the same rule that
 * PageController@contact now respects for FAQs.
 */
class HeroSlideSeeder extends Seeder
{
    private const LOCALES = ['fr', 'en', 'ar'];

    public function run(): void
    {
        foreach ($this->slides() as $slide) {
            HeroSlide::firstOrCreate(
                ['sort_order' => $slide['sort_order']],
                $slide
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function slides(): array
    {
        $years = SiteSetting::get('stats_years', '15');

        return [
            [
                'sort_order' => 0,
                // Slide 1 mirrored the editable hero_* site settings, so its copy
                // is taken from those where set and from the lang files otherwise.
                'badge' => $this->normalizeBadge($this->setting('hero_badge', 'hero_values_badge')),
                'title' => $this->setting('hero_title', 'hero_title'),
                'highlight' => $this->setting('hero_subtitle', 'hero_subtitle'),
                'description' => $this->setting('hero_description', 'hero_description'),
                'alt_text' => $this->lang('hero_slide1_alt'),
                'badge_icon' => 'star',
                'accent_color' => 'orange',
                'cta_type' => 'quote',
                'cta_label' => $this->lang('request_quote'),
                'show_whatsapp' => true,
                'image_url' => '/images/hero/slide-1.webp',
            ],
            [
                'sort_order' => 1,
                'badge' => $this->lang('hero_slide2_badge'),
                'title' => $this->lang('hero_slide2_title'),
                'highlight' => $this->lang('hero_slide2_highlight'),
                'description' => $this->lang('hero_slide2_description'),
                'alt_text' => $this->lang('hero_slide2_alt'),
                'badge_icon' => 'home',
                'accent_color' => 'orange',
                'cta_type' => 'services',
                'cta_label' => $this->lang('learn_more'),
                'show_whatsapp' => false,
                'image_url' => '/images/hero/slide-2.webp',
            ],
            [
                'sort_order' => 2,
                'badge' => $this->lang('hero_slide3_badge'),
                'title' => $this->lang('hero_slide3_title'),
                'highlight' => $this->lang('hero_slide3_highlight'),
                'description' => $this->lang('hero_slide3_description'),
                'alt_text' => $this->lang('hero_slide3_alt'),
                'badge_icon' => 'layout-grid',
                'accent_color' => 'cyan',
                'cta_type' => 'portfolio',
                'cta_label' => $this->lang('view_our_work'),
                'show_whatsapp' => false,
                'image_url' => '/images/hero/slide-3.webp',
            ],
            [
                'sort_order' => 3,
                'badge' => $this->lang('hero_slide4_badge'),
                // The markup interpolated stats_years at render time. Storing the
                // resolved string freezes it, which is the point: the admin now
                // edits this title directly instead of via a numeric setting.
                'title' => $this->map(fn (string $locale): string => $years.'+ '.$this->trans('years_experience', $locale)),
                'highlight' => $this->lang('hundreds_projects_completed'),
                'description' => $this->lang('hero_slide4_description'),
                'alt_text' => $this->lang('hero_slide4_alt'),
                'badge_icon' => 'star',
                'accent_color' => 'emerald',
                'cta_type' => 'contact',
                'cta_label' => $this->lang('start_your_project'),
                'show_whatsapp' => false,
                'image_url' => '/images/hero/slide-4.webp',
            ],
        ];
    }

    /**
     * A site setting per locale, falling back to the lang file for any locale
     * the admin has not filled in — mirroring SiteSetting::getTranslated.
     *
     * @return array<string, string>
     */
    private function setting(string $settingKey, string $messageKey): array
    {
        return $this->map(function (string $locale) use ($settingKey, $messageKey): string {
            $value = SiteSetting::get("{$settingKey}_{$locale}");

            return filled($value) ? $value : $this->trans($messageKey, $locale);
        });
    }

    /** @return array<string, string> */
    private function lang(string $messageKey): array
    {
        return $this->map(fn (string $locale): string => $this->trans($messageKey, $locale));
    }

    /**
     * The homepage rewrote any "Promo Alu Plus" spelling to the short brand
     * form before printing the badge. Keep that once the DB drives the copy.
     *
     * @param  array<string, string>  $badge
     * @return array<string, string>
     */
    private function normalizeBadge(array $badge): array
    {
        return array_map(
            static fn (string $value): string => preg_match('/promo\s*alu\s*plus/i', $value) ? 'PromoAlu+' : $value,
            $badge
        );
    }

    /**
     * @param  callable(string): string  $resolve
     * @return array<string, string>
     */
    private function map(callable $resolve): array
    {
        $values = [];

        foreach (self::LOCALES as $locale) {
            $values[$locale] = $resolve($locale);
        }

        return $values;
    }

    private function trans(string $messageKey, string $locale): string
    {
        return (string) trans("messages.{$messageKey}", [], $locale);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\CanonicalServiceCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Executable coherence checks: translation parity, canonical-catalog
 * alignment, and the content invariants the marketing site relies on.
 *
 * Tests that are skipped mark a *known defect* — the assertion below the skip
 * is the invariant that should hold once it is fixed. Each one names the
 * section of docs/site-feature-and-coherence-report.md that describes it.
 */
class SiteCoherenceTest extends TestCase
{
    use RefreshDatabase;

    private const LOCALES = ['fr', 'en', 'ar'];

    /**
     * Strings that are legitimately identical in French and English —
     * proper nouns and loanwords, not missed translations.
     *
     * @var list<string>
     */
    private const FR_EN_IDENTICAL_ALLOWLIST = [
        'nav_services', 'nav_contact', 'navigation', 'pergola', 'durable',
        'value_innovation_title', 'step_contact', 'step_installation', 'photos',
    ];

    private function messages(string $locale): array
    {
        return require base_path("lang/{$locale}/messages.php");
    }

    // -------------------------------------------------------- translations

    public function test_all_locales_expose_exactly_the_same_message_keys(): void
    {
        $reference = array_keys($this->messages('fr'));

        foreach (['en', 'ar'] as $locale) {
            $keys = array_keys($this->messages($locale));

            $this->assertSame(
                [],
                array_values(array_diff($reference, $keys)),
                "lang/{$locale}/messages.php is missing keys present in French."
            );
            $this->assertSame(
                [],
                array_values(array_diff($keys, $reference)),
                "lang/{$locale}/messages.php has keys absent from French."
            );
        }
    }

    public function test_no_locale_contains_an_empty_translation(): void
    {
        foreach (self::LOCALES as $locale) {
            foreach ($this->messages($locale) as $key => $value) {
                if (! is_string($value)) {
                    continue;
                }

                $this->assertNotSame('', trim($value), "messages.{$key} is empty in {$locale}.");
            }
        }
    }

    public function test_english_strings_are_actually_translated_from_french(): void
    {
        $fr = $this->messages('fr');
        $en = $this->messages('en');

        $untranslated = [];

        foreach ($fr as $key => $value) {
            if (! is_string($value) || ! is_string($en[$key] ?? null)) {
                continue;
            }

            if ($value === $en[$key] && mb_strlen($value) > 3 && ! in_array($key, self::FR_EN_IDENTICAL_ALLOWLIST, true)) {
                $untranslated[] = $key;
            }
        }

        $this->assertSame([], $untranslated, 'English values identical to French — likely untranslated.');
    }

    public function test_arabic_strings_are_never_copies_of_the_french_source(): void
    {
        $fr = $this->messages('fr');
        $ar = $this->messages('ar');

        foreach ($fr as $key => $value) {
            if (! is_string($value) || ! is_string($ar[$key] ?? null) || mb_strlen($value) <= 3) {
                continue;
            }

            $this->assertNotSame($value, $ar[$key], "messages.{$key} is untranslated in Arabic.");
        }
    }

    public function test_every_message_key_used_in_a_blade_view_exists_in_all_locales(): void
    {
        $used = [];

        foreach (File::allFiles(resource_path('views')) as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            preg_match_all(
                "/__\(\s*['\"]messages\.([a-z0-9_]+)['\"]/i",
                $file->getContents(),
                $matches
            );

            foreach ($matches[1] as $key) {
                $used[$key] = $file->getRelativePathname();
            }
        }

        $this->assertNotEmpty($used, 'No translation keys were discovered in the Blade views.');

        foreach (self::LOCALES as $locale) {
            $available = $this->messages($locale);

            foreach ($used as $key => $view) {
                $this->assertArrayHasKey(
                    $key,
                    $available,
                    "messages.{$key} is used by {$view} but missing from lang/{$locale}."
                );
            }
        }
    }

    // ---------------------------------------------------- canonical catalog

    public function test_canonical_slugs_have_a_label_in_every_locale(): void
    {
        foreach (self::LOCALES as $locale) {
            $messages = $this->messages($locale);
            $options = CanonicalServiceCatalog::translatedOptions($locale);

            $this->assertSame(CanonicalServiceCatalog::slugs(), array_keys($options));

            foreach (CanonicalServiceCatalog::MESSAGE_KEYS_BY_SLUG as $slug => $messageKey) {
                $this->assertArrayHasKey(
                    $messageKey,
                    $messages,
                    "Service '{$slug}' has no messages.{$messageKey} entry in {$locale}."
                );
                $this->assertNotSame('', trim($options[$slug]), "Service '{$slug}' has no {$locale} label.");
            }
        }
    }

    public function test_canonical_labels_are_distinct_within_each_locale(): void
    {
        foreach (self::LOCALES as $locale) {
            $labels = array_values(CanonicalServiceCatalog::translatedOptions($locale));

            $this->assertSame(
                count($labels),
                count(array_unique($labels)),
                "Two canonical services share a label in {$locale}: ".implode(' | ', $labels)
            );
        }
    }

    public function test_service_icon_and_colour_maps_cover_exactly_the_canonical_slugs(): void
    {
        $slugs = CanonicalServiceCatalog::slugs();

        $this->assertSame($slugs, array_keys(Service::DEFAULT_ICON_BY_SLUG));
        $this->assertSame($slugs, array_keys(Service::DEFAULT_COLOR_BY_SLUG));
    }

    public function test_quote_options_are_the_canonical_slugs_plus_other(): void
    {
        $this->assertSame(
            [...CanonicalServiceCatalog::slugs(), CanonicalServiceCatalog::OTHER_SLUG],
            array_keys(CanonicalServiceCatalog::quoteOptions('fr'))
        );
    }

    public function test_portfolio_filter_categories_come_from_the_database(): void
    {
        $this->assertStringNotContainsString(
            "route('portfolio', ['category' => '",
            File::get(resource_path('views/pages/portfolio.blade.php')),
            'Portfolio categories must be driven by ProjectType records, not hardcoded in the view.'
        );
    }

    // ------------------------------------------------------- content models

    public function test_service_accessors_fall_back_to_french_for_a_missing_locale(): void
    {
        $service = Service::create([
            'slug' => 'kitchen',
            'title' => ['fr' => 'Cuisine'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'features' => [['fr' => 'Sur mesure']],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertSame('Cuisine', $service->getTranslatedTitle('ar'));
        $this->assertSame('Courte', $service->getTranslatedShortDescription('en'));
        $this->assertSame(['Sur mesure'], $service->getTranslatedFeatures('en'));
    }

    public function test_service_display_icon_and_colour_fall_back_to_the_slug_defaults(): void
    {
        $service = Service::create([
            'slug' => 'pergola',
            'title' => ['fr' => 'Pergola'],
            'icon' => null,
            'color' => '',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertSame(Service::DEFAULT_ICON_BY_SLUG['pergola'], $service->getDisplayIcon());
        $this->assertSame(Service::DEFAULT_COLOR_BY_SLUG['pergola'], $service->getDisplayColor());
    }

    public function test_site_setting_translation_falls_back_to_french(): void
    {
        SiteSetting::set('about_story_fr', 'Histoire française');

        app()->setLocale('ar');
        $this->assertSame('Histoire française', SiteSetting::getTranslated('about_story'));

        app()->setLocale('en');
        $this->assertSame('Histoire française', SiteSetting::getTranslated('about_story'));
    }

    public function test_site_setting_cache_is_invalidated_on_update(): void
    {
        SiteSetting::set('contact_phone', '+21611111111');
        $this->assertSame('+21611111111', SiteSetting::get('contact_phone'));

        SiteSetting::set('contact_phone', '+21622222222');
        $this->assertSame('+21622222222', SiteSetting::get('contact_phone'));
    }

    // ----------------------------------------------------- known incoherence

    public function test_chatbot_faq_endpoint_orders_by_a_column_that_exists(): void
    {
        $this->assertFalse(
            Schema::hasColumn('faqs', 'order'),
            'The faqs table gained an "order" column — report §F-02 may be resolved.'
        );

        $this->markTestSkipped(
            'Known defect (report §F-02): ChatbotController::getFaqs() orders by "order", '
            .'a column the faqs table does not have. SQLite silently treats it as a string '
            .'literal (no ordering); MySQL/PostgreSQL raise "unknown column" and the '
            .'endpoint returns 500.'
        );

        Faq::create(['question' => ['fr' => 'B'], 'answer' => ['fr' => 'b'], 'is_active' => true, 'sort_order' => 2]);
        Faq::create(['question' => ['fr' => 'A'], 'answer' => ['fr' => 'a'], 'is_active' => true, 'sort_order' => 1]);

        $texts = array_column($this->getJson(route('chatbot.faqs'))->json('quick_replies'), 'text');

        $this->assertSame(['A', 'B'], array_slice($texts, 0, 2));
    }

    public function test_contact_page_does_not_override_database_faqs_with_language_files(): void
    {
        $this->markTestSkipped(
            'Known defect (report §F-04): PageController@contact rewrites the FAQ whose '
            .'sort_order is 2 with messages.faq_q2/faq_a2, so admin edits to that row are '
            .'silently discarded on the public page.'
        );

        Faq::create([
            'question' => ['fr' => 'Question éditée en admin ?'],
            'answer' => ['fr' => 'Réponse éditée en admin'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->withSession(['locale' => 'fr'])
            ->get(route('contact'))
            ->assertSee('Question éditée en admin ?', false);
    }

    public function test_the_company_brand_name_is_consistent_across_every_fallback(): void
    {
        $this->markTestSkipped(
            'Known defect (report §F-03): the quote e-mail templates fall back to '
            .'"AluminiumCraft Tunisie" while PdfController, the Filament panel and the '
            .'public layout use "PromoAlu+".'
        );

        $sources = [
            'resources/views/emails/quote-received.blade.php',
            'app/Http/Controllers/PdfController.php',
            'app/Providers/Filament/AdminPanelProvider.php',
        ];

        foreach ($sources as $source) {
            $this->assertStringNotContainsStringIgnoringCase(
                'AluminiumCraft',
                File::get(base_path($source)),
                "{$source} still references the retired AluminiumCraft brand."
            );
        }
    }

    public function test_the_promised_response_time_is_stated_consistently(): void
    {
        $this->markTestSkipped(
            'Known defect (report §F-07): messages.response_time promises a reply within '
            .'24h while messages.cta_description, quote_form_intro, quote_success and faq_a1 '
            .'all promise 48h.'
        );

        foreach (self::LOCALES as $locale) {
            $messages = $this->messages($locale);
            $delays = [];

            foreach (['response_time', 'cta_description', 'quote_form_intro', 'quote_success', 'faq_a1'] as $key) {
                if (preg_match('/(\d+)\s*(h|ساعة|hours?)/iu', (string) ($messages[$key] ?? ''), $m)) {
                    $delays[$key] = $m[1];
                }
            }

            $this->assertLessThanOrEqual(1, count(array_unique($delays)), "Conflicting response-time promises in {$locale}: ".json_encode($delays));
        }
    }

    // ------------------------------------------------------- seeder sources

    public function test_content_seeders_read_from_a_path_inside_the_repository(): void
    {
        $this->markTestSkipped(
            'Known defect (report §F-08): ServiceSeeder and FaqSeeder read '
            ."base_path('../content_docs/json/...'), a directory outside the repository, "
            .'so `php artisan migrate --seed` silently produces zero services on a fresh clone.'
        );

        foreach (['ServiceSeeder', 'FaqSeeder'] as $seeder) {
            $this->assertStringNotContainsString(
                '../content_docs',
                File::get(database_path("seeders/{$seeder}.php")),
                "{$seeder} reads content from outside the repository."
            );
        }
    }

    public function test_seeder_json_files_shipped_in_the_repository_are_actually_used(): void
    {
        $this->markTestSkipped(
            'Known defect (report §F-09): database/seeders/services.json, faqs.json and '
            .'site_settings.json are committed but no seeder reads them; CLAUDE.md documents '
            .'them as the content source.'
        );

        $seeders = collect(File::files(database_path('seeders')))
            ->filter(fn ($f) => str_ends_with($f->getFilename(), '.php'))
            ->map(fn ($f) => $f->getContents())
            ->implode("\n");

        foreach (['services.json', 'faqs.json', 'site_settings.json'] as $json) {
            $this->assertStringContainsString($json, $seeders, "database/seeders/{$json} is never read by a seeder.");
        }
    }

    public function test_no_seeder_or_resource_class_file_is_empty(): void
    {
        $this->markTestSkipped(
            'Known defect (report §F-05): CategorySeeder.php, CategoryResource.php and '
            .'ProjectTypeResource.php are committed as zero-byte files.'
        );

        $paths = array_merge(
            File::files(database_path('seeders')),
            File::files(app_path('Filament/Resources'))
        );

        foreach ($paths as $file) {
            if (! str_ends_with($file->getFilename(), '.php')) {
                continue;
            }

            $this->assertGreaterThan(0, $file->getSize(), "{$file->getFilename()} is an empty file.");
        }
    }
}

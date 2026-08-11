<?php

namespace Tests\Feature;

use App\Filament\Resources\TestimonialResource\Pages\EditTestimonial;
use App\Mail\QuoteRequestNotification;
use App\Mail\QuoteRequestReceived;
use App\Models\ChatbotFlow;
use App\Models\Faq;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\User;
use App\Support\CanonicalServiceCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * End-to-end coverage of every public-facing feature: the five marketing
 * pages in all three locales, the locale switcher, SEO endpoints, the quote
 * form, the chatbot endpoints, and the auth gate on document downloads.
 *
 * Findings referenced as "see report §X" are documented in
 * docs/site-feature-and-coherence-report.md.
 */
class PublicSiteFeatureTest extends TestCase
{
    use RefreshDatabase;

    private const LOCALES = ['fr', 'en', 'ar'];

    /** Pages that are always public. */
    private const PAGE_ROUTES = ['home', 'services', 'about', 'contact'];

    /** Pages gated behind a SiteSetting toggle. */
    private const TOGGLEABLE_PAGE_ROUTES = ['portfolio'];

    private function enablePortfolio(): void
    {
        SiteSetting::set('portfolio_enabled', '1', 'boolean', 'pages');
    }

    private function seedContent(): void
    {
        Service::create([
            'slug' => 'windows',
            'title' => ['fr' => 'Fenêtres', 'en' => 'Windows', 'ar' => 'نوافذ'],
            'short_description' => ['fr' => 'Court FR.', 'en' => 'Short EN.', 'ar' => 'قصير.'],
            'description' => ['fr' => 'Long FR.', 'en' => 'Long EN.', 'ar' => 'طويل.'],
            'features' => [['fr' => 'Double vitrage', 'en' => 'Double glazing', 'ar' => 'زجاج مزدوج']],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Project::create([
            'title' => ['fr' => 'Villa Test', 'en' => 'Test Villa', 'ar' => 'فيلا'],
            'description' => ['fr' => 'Desc FR', 'en' => 'Desc EN', 'ar' => 'وصف'],
            'category' => 'windows',
            'location' => 'Sousse',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Testimonial::create([
            'client_name' => 'Client Test',
            'client_location' => 'Sousse, Tunisie',
            'content' => ['fr' => 'Avis FR', 'en' => 'Review EN', 'ar' => 'رأي'],
            'rating' => 5,
            'project_type' => 'windows',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Faq::create([
            'question' => ['fr' => 'Question FR ?', 'en' => 'Question EN?', 'ar' => 'سؤال؟'],
            'answer' => ['fr' => 'Réponse FR', 'en' => 'Answer EN', 'ar' => 'جواب'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    private function validQuotePayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Younes',
            'name' => 'Ben Dhiab',
            'email' => 'client@example.test',
            'phone' => '+21620000000',
            'country' => 'Tunisie',
            'city' => 'Sousse',
            'project_types' => ['windows'],
            'description' => 'Deux fenêtres coulissantes pour un salon.',
            'timeline' => '1-3 mois',
        ], $overrides);
    }

    // ---------------------------------------------------------------- pages

    public function test_every_public_page_renders_in_every_locale(): void
    {
        $this->seedContent();
        $this->enablePortfolio();

        foreach (self::LOCALES as $locale) {
            foreach ([...self::PAGE_ROUTES, ...self::TOGGLEABLE_PAGE_ROUTES] as $page) {
                $this->withSession(['locale' => $locale])
                    ->get(route($page))
                    ->assertOk();
            }
        }
    }

    public function test_public_pages_render_with_a_completely_empty_database(): void
    {
        $this->enablePortfolio();

        foreach ([...self::PAGE_ROUTES, ...self::TOGGLEABLE_PAGE_ROUTES] as $page) {
            $this->get(route($page))->assertOk();
        }
    }

    public function test_arabic_locale_switches_the_document_to_rtl(): void
    {
        $this->withSession(['locale' => 'ar'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('dir="rtl"', false);

        $this->withSession(['locale' => 'fr'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('dir="ltr"', false);
    }

    public function test_home_page_shows_database_content_when_present(): void
    {
        $this->seedContent();

        $this->withSession(['locale' => 'fr'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Fenêtres', false)
            ->assertSee('Client Test');
    }

    /**
     * Walkthrough item: "edit a testimonial and confirm the homepage
     * updates" — proves the admin edit path and the public read path share
     * the same row, full stack.
     */
    public function test_an_admin_edit_to_a_testimonial_is_shown_on_the_homepage(): void
    {
        $testimonial = Testimonial::create([
            'client_name' => 'Client Original',
            'client_location' => 'Sousse, Tunisie',
            'content' => ['fr' => 'Avis original'],
            'rating' => 5,
            'project_type' => 'windows',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs(User::factory()->create());

        Livewire::test(EditTestimonial::class, ['record' => $testimonial->getKey()])
            ->set('data.client_name', 'Client Modifié')
            ->set('data.content.fr', 'Avis modifié par admin')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->withSession(['locale' => 'fr'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Client Modifié')
            ->assertSee('Avis modifié par admin')
            ->assertDontSee('Client Original');
    }

    /**
     * Regression: the services page built its gallery from the raw 'gallery'
     * column only, so a thumbnail set independently of the gallery vanished
     * entirely whenever the gallery was non-empty.
     */
    public function test_the_services_page_shows_both_the_thumbnail_and_the_gallery(): void
    {
        Service::create([
            'slug' => 'windows',
            'title' => ['fr' => 'Fenêtres', 'en' => 'Windows', 'ar' => 'نوافذ'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'image' => 'services/windows/thumb.jpeg',
            'gallery' => ['services/windows/g1.jpeg'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $html = $this->withSession(['locale' => 'fr'])
            ->get(route('services'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(asset('uploads/services/windows/thumb.jpeg'), $html);
        $this->assertStringContainsString(asset('uploads/services/windows/g1.jpeg'), $html);
    }

    public function test_portfolio_filters_projects_by_category(): void
    {
        $this->seedContent();
        $this->enablePortfolio();

        Project::create([
            'title' => ['fr' => 'Cuisine Test', 'en' => 'Cuisine Test', 'ar' => 'مطبخ'],
            'description' => ['fr' => 'Desc', 'en' => 'Desc', 'ar' => 'وصف'],
            'category' => 'kitchen',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->withSession(['locale' => 'fr'])
            ->get(route('portfolio', ['category' => 'kitchen']))
            ->assertOk()
            ->assertSee('Cuisine Test')
            ->assertDontSee('Villa Test');

        $this->withSession(['locale' => 'fr'])
            ->get(route('portfolio'))
            ->assertOk()
            ->assertSee('Villa Test')
            ->assertSee('Cuisine Test');
    }

    public function test_contact_page_lists_active_faqs_only(): void
    {
        Faq::create([
            'question' => ['fr' => 'Question visible ?'],
            'answer' => ['fr' => 'Réponse visible'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Faq::create([
            'question' => ['fr' => 'Question cachee ?'],
            'answer' => ['fr' => 'Reponse cachee'],
            'is_active' => false,
            'sort_order' => 3,
        ]);

        $this->withSession(['locale' => 'fr'])
            ->get(route('contact'))
            ->assertOk()
            ->assertSee('Question visible ?', false)
            ->assertDontSee('Question cachee ?', false);
    }

    public function test_contact_page_renders_site_settings_contact_details(): void
    {
        SiteSetting::set('contact_phone', '+21671000111');
        SiteSetting::set('contact_email', 'contact@promoaluplus.test');

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('contact@promoaluplus.test');
    }

    public function test_an_admin_edit_to_faq_two_is_shown_on_the_contact_page(): void
    {
        Faq::create([
            'question' => ['fr' => 'Question modifiée par admin', 'en' => 'Edited', 'ar' => 'سؤال'],
            'answer' => ['fr' => 'Réponse modifiée par admin', 'en' => 'Edited', 'ar' => 'جواب'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->withSession(['locale' => 'fr'])
            ->get(route('contact'))
            ->assertOk()
            ->assertSee('Question modifiée par admin')
            ->assertSee('Réponse modifiée par admin');
    }

    // --------------------------------------------------------------- locale

    public function test_locale_switcher_persists_a_supported_locale(): void
    {
        $this->from(route('home'))
            ->get(route('locale.set', 'ar'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('locale', 'ar');
    }

    public function test_locale_switcher_ignores_an_unsupported_locale(): void
    {
        $this->from(route('home'))
            ->withSession(['locale' => 'fr'])
            ->get(route('locale.set', 'de'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('locale', 'fr');
    }

    // ------------------------------------------------------------------ seo

    public function test_sitemap_lists_every_public_page_as_valid_xml(): void
    {
        $this->enablePortfolio();

        $response = $this->get('/sitemap.xml')->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'sitemap.xml is not well-formed XML.');

        $expected = [...self::PAGE_ROUTES, ...self::TOGGLEABLE_PAGE_ROUTES];
        $this->assertCount(count($expected), $xml->url);

        foreach ($expected as $page) {
            $response->assertSee(route($page), false);
        }
    }

    public function test_the_portfolio_page_is_absent_from_the_sitemap_by_default(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'sitemap.xml is not well-formed XML.');
        $this->assertCount(count(self::PAGE_ROUTES), $xml->url);

        $response->assertDontSee(route('portfolio'), false);
    }

    public function test_robots_txt_points_at_the_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('User-agent: *')
            ->assertSee(route('sitemap'), false);
    }

    // ---------------------------------------------------------------- quote

    public function test_quote_submission_persists_the_request_and_queues_both_mails(): void
    {
        Mail::fake();

        $this->from(route('contact'))
            ->post(route('quote.store'), $this->validQuotePayload())
            ->assertRedirect(route('contact'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('quotes', [
            'email' => 'client@example.test',
            'city' => 'Sousse',
        ]);
        $this->assertSame(['windows'], Quote::where('email', 'client@example.test')->first()->project_types);

        Mail::assertQueued(QuoteRequestReceived::class);
        Mail::assertQueued(QuoteRequestNotification::class);
    }

    /**
     * The whole point of this change: a client can ask for a devis covering
     * several project types at once (doors + windows + pergola) in a single
     * request instead of filing one per type.
     */
    public function test_quote_submission_accepts_several_project_types_at_once(): void
    {
        Mail::fake();

        $this->postJson(route('quote.store'), $this->validQuotePayload([
            'project_types' => ['doors', 'windows', 'pergola'],
        ]))->assertOk();

        $quote = Quote::where('email', 'client@example.test')->firstOrFail();

        $this->assertSame(['doors', 'windows', 'pergola'], $quote->project_types);
    }

    public function test_quote_submission_answers_json_when_asked(): void
    {
        Mail::fake();

        $this->postJson(route('quote.store'), $this->validQuotePayload())
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_quote_submission_requires_the_mandatory_fields(): void
    {
        Mail::fake();

        $this->from(route('contact'))
            ->post(route('quote.store'), [])
            ->assertSessionHasErrors(['first_name', 'name', 'email', 'phone', 'project_types', 'description']);

        $this->assertDatabaseCount('quotes', 0);
    }

    public function test_quote_accepts_every_canonical_service_plus_other(): void
    {
        Mail::fake();

        $accepted = [...CanonicalServiceCatalog::slugs(), CanonicalServiceCatalog::OTHER_SLUG];

        foreach ($accepted as $slug) {
            $this->postJson(route('quote.store'), $this->validQuotePayload(['project_types' => [$slug]]))
                ->assertOk();
        }

        $this->assertDatabaseCount('quotes', count($accepted));
    }

    public function test_quote_rejects_an_empty_project_types_selection(): void
    {
        Mail::fake();

        $this->postJson(route('quote.store'), $this->validQuotePayload(['project_types' => []]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('project_types');
    }

    public function test_quote_rejects_a_project_type_outside_the_catalog(): void
    {
        Mail::fake();

        $this->postJson(route('quote.store'), $this->validQuotePayload(['project_types' => ['facades']]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('project_types.0');
    }

    public function test_quote_rejects_a_malformed_email(): void
    {
        Mail::fake();

        $this->postJson(route('quote.store'), $this->validQuotePayload(['email' => 'not-an-email']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_quote_submission_survives_a_mail_transport_failure(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));

        $this->postJson(route('quote.store'), $this->validQuotePayload())
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('quotes', 1);
    }

    // -------------------------------------------------------------- chatbot

    public function test_chatbot_welcome_falls_back_when_no_flow_is_configured(): void
    {
        $this->getJson(route('chatbot.welcome'))
            ->assertOk()
            ->assertJsonStructure(['success', 'message', 'quick_replies']);
    }

    public function test_chatbot_welcome_uses_the_configured_welcome_flow(): void
    {
        ChatbotFlow::create([
            'trigger' => 'welcome',
            'message' => ['fr' => 'Bonjour depuis la base', 'en' => 'Hello from the database', 'ar' => 'مرحبا'],
            'quick_replies' => [
                ['text' => ['fr' => 'Devis', 'en' => 'Quote', 'ar' => 'عرض'], 'action' => 'flow:quote'],
            ],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->withSession(['locale' => 'fr'])
            ->getJson(route('chatbot.welcome'))
            ->assertOk()
            ->assertJsonPath('message', 'Bonjour depuis la base');
    }

    public function test_chatbot_returns_active_faqs_as_quick_replies(): void
    {
        Faq::create([
            'question' => ['fr' => 'Delais ?', 'en' => 'Lead time?', 'ar' => 'المدة؟'],
            'answer' => ['fr' => 'Deux semaines', 'en' => 'Two weeks', 'ar' => 'أسبوعان'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->withSession(['locale' => 'en'])
            ->getJson(route('chatbot.faqs'))
            ->assertOk();

        $texts = array_column($response->json('quick_replies'), 'text');
        $this->assertContains('Lead time?', $texts);
    }

    public function test_chatbot_answers_an_faq_selected_by_id(): void
    {
        $faq = Faq::create([
            'question' => ['fr' => 'Garantie ?', 'en' => 'Warranty?', 'ar' => 'الضمان؟'],
            'answer' => ['fr' => 'Dix ans', 'en' => 'Ten years', 'ar' => 'عشر سنوات'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->withSession(['locale' => 'en'])
            ->postJson(route('chatbot.message'), ['action' => 'faq:'.$faq->id])
            ->assertOk()
            ->assertJsonPath('message', 'Ten years');
    }

    public function test_chatbot_matches_a_flow_by_keyword(): void
    {
        ChatbotFlow::create([
            'trigger' => 'quote',
            'trigger_type' => 'keyword',
            'keywords' => ['devis', 'prix'],
            'message' => ['fr' => 'Voici comment obtenir un devis', 'en' => 'Here is how to get a quote', 'ar' => 'كيفية'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->withSession(['locale' => 'fr'])
            ->postJson(route('chatbot.message'), ['message' => 'je veux un devis'])
            ->assertOk()
            ->assertJsonPath('message', 'Voici comment obtenir un devis');
    }

    public function test_chatbot_falls_back_on_an_unknown_message(): void
    {
        $this->withSession(['locale' => 'en'])
            ->postJson(route('chatbot.message'), ['message' => 'zzzzz unmatchable zzzzz'])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['message', 'quick_replies']);
    }

    // ------------------------------------------------------------ documents

    public function test_document_routes_are_closed_to_guests(): void
    {
        $quote = Quote::create($this->validQuotePayload(['status' => 'quoted']));
        $invoice = Invoice::create([
            'quote_id' => $quote->id,
            'invoice_number' => 'FAC-2026-0001',
            'client_name' => 'Ben Dhiab',
            'client_email' => 'client@example.test',
            'issue_date' => '2026-08-09',
            'total' => 1000,
        ]);

        $this->get(route('quote.pdf', $quote))->assertRedirect();
        $this->get(route('quote.excel', $quote))->assertRedirect();
        $this->get(route('invoice.pdf', $invoice))->assertRedirect();
    }

    public function test_authenticated_admin_can_download_quote_documents(): void
    {
        $quote = Quote::create($this->validQuotePayload(['status' => 'quoted', 'quote_number' => 'DEV-2026-0001']));
        $quote->items()->create([
            'description' => 'Fenêtre coulissante',
            'height' => 1.2, 'width' => 1.0, 'quantity' => 2,
            'unit_price' => 500, 'order' => 0,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('quote.pdf', $quote))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs(User::factory()->create())
            ->get(route('quote.excel', $quote))
            ->assertOk();
    }

    public function test_authenticated_admin_can_download_an_invoice_pdf(): void
    {
        $quote = Quote::create($this->validQuotePayload(['status' => 'accepted']));
        $invoice = Invoice::create([
            'quote_id' => $quote->id,
            'invoice_number' => 'FAC-2026-0002',
            'client_name' => 'Ben Dhiab',
            'client_email' => 'client@example.test',
            'issue_date' => '2026-08-09',
            'total' => 1200,
        ]);
        $invoice->items()->create([
            'description' => 'Fenêtre coulissante',
            'quantity' => 2,
            'unit_price' => 600,
            'total' => 1200,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('invoice.pdf', $invoice))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    // -------------------------------------------------------- admin surface

    public function test_admin_panel_redirects_guests_to_the_login_screen(): void
    {
        $this->get('/admin')->assertRedirect();
        $this->get('/admin/login')->assertOk();
    }

    public function test_admin_panel_is_reachable_outside_a_local_environment(): void
    {
        // Report §F-01, fixed: Filament's Authenticate middleware aborts 403
        // unless the user implements FilamentUser or app.env === 'local'.
        // App\Models\User now implements it (see User::canAccessPanel()), so
        // the panel no longer depends on app.env at all.
        $this->actingAs(User::factory()->create());

        $this->assertNotSame('local', config('app.env'));
        $this->get('/admin/quotes')->assertOk();
    }

    public function test_admin_panel_still_requires_authentication(): void
    {
        $this->get('/admin/quotes')->assertRedirect();
    }

    public function test_every_filament_resource_index_renders(): void
    {
        config(['app.env' => 'local']);
        $this->actingAs(User::factory()->create());

        // categories is deliberately absent: its resource class is an empty
        // file and registers no routes — see report §F-05. project-types was
        // the same until it was implemented (see ProjectTypeTest).
        $slugs = [
            'quotes', 'invoices', 'services', 'projects', 'testimonials',
            'faqs', 'chatbot-flows', 'project-types',
        ];

        foreach ($slugs as $slug) {
            $this->get("/admin/{$slug}")->assertOk();
        }

        $this->get('/admin')->assertOk();
        $this->get('/admin/site-settings')->assertOk();
    }

    public function test_category_resource_registers_no_admin_routes(): void
    {
        // Documents the current dead-code state (report §F-05). CategoryResource
        // is still an empty stub — out of scope for the ProjectType work. When it
        // is either implemented or deleted, this test should change.
        $this->assertNull(
            app('router')->getRoutes()->getByName('filament.admin.resources.categories.index'),
            'CategoryResource now registers routes — update report §F-05.'
        );
    }

    public function test_project_type_resource_registers_admin_routes(): void
    {
        // ProjectTypeResource was implemented (see ProjectTypeTest) — its
        // routes now register, unlike the still-stubbed CategoryResource above.
        $this->assertNotNull(
            app('router')->getRoutes()->getByName('filament.admin.resources.project-types.index')
        );
    }

    public function test_the_project_type_create_form_renders(): void
    {
        // The index-render check above only exercises table(). Hitting the
        // create page mounts form() too, covering the name.fr dot-binding,
        // markAsRequired(), alphaDash(), and extraInputAttributes() calls.
        config(['app.env' => 'local']);
        $this->actingAs(User::factory()->create());

        $this->get('/admin/project-types/create')->assertOk();
    }

    public function test_the_homepage_shows_no_invented_testimonials_when_the_table_is_empty(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Mohamed B.')
            ->assertDontSee('Sonia K.')
            ->assertDontSee('Ahmed T.');
    }

    public function test_the_homepage_shows_an_honest_empty_state_instead_of_fake_reviews(): void
    {
        $this->withSession(['locale' => 'fr'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee(__('messages.testimonials_empty', [], 'fr'));
    }

    public function test_the_testimonial_seeder_populates_the_previously_hardcoded_reviews(): void
    {
        $this->seed(\Database\Seeders\TestimonialSeeder::class);

        $this->withSession(['locale' => 'fr'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Mohamed B.')
            ->assertSee('Sonia K.')
            ->assertSee('Ahmed T.');
    }

    public function test_the_testimonial_create_form_renders(): void
    {
        // The admin-slug loop above only exercises table(); mounting the create
        // page covers form(), including the client_photo FileUpload and the
        // image_url TextInput (which must not be a type="url" input).
        config(['app.env' => 'local']);
        $this->actingAs(User::factory()->create());

        $this->get('/admin/testimonials/create')->assertOk();
    }

    public function test_the_testimonial_edit_form_hydrates_a_root_relative_image_url(): void
    {
        // The create page has no record to hydrate. Mounting the edit page with
        // a stored root-relative path is what proves image_url exists as a
        // column and that its field is not a type="url" input, which would
        // reject the value and block the whole form on save.
        config(['app.env' => 'local']);
        $this->actingAs(User::factory()->create());

        $testimonial = Testimonial::create([
            'client_name' => 'Client Test',
            'client_location' => 'Tunis, Tunisie',
            'content' => ['fr' => 'Avis.', 'en' => 'Review.', 'ar' => 'رأي.'],
            'image_url' => '/images/logo-160.webp',
            'rating' => 5,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->get("/admin/testimonials/{$testimonial->id}/edit")->assertOk();
    }
}

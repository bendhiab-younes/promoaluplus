<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\QuoteResource;
use App\Livewire\AdminNote;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\AdminNotes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The "À propos de cette page" callout: it must appear on every section an
 * admin lands on, stay off the create/edit screens, and remember each admin's
 * chosen language across logins.
 */
class AdminNotesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    public function test_every_mapped_page_has_text_in_both_languages(): void
    {
        foreach (AdminNotes::PAGES as $pageClass => $key) {
            $this->assertTrue(class_exists($pageClass), "Missing page class {$pageClass}");

            foreach (array_keys(AdminNotes::LOCALES) as $locale) {
                $heading = AdminNotes::heading($key, $locale);
                $body = AdminNotes::body($key, $locale);

                $this->assertStringNotContainsString('admin_notes.', $heading, "Untranslated heading: {$key}/{$locale}");
                $this->assertNotEmpty($body, "Empty body: {$key}/{$locale}");

                foreach ($body as $paragraph) {
                    $this->assertStringNotContainsString('admin_notes.', $paragraph);
                }
            }
        }
    }

    public function test_the_note_renders_on_every_mapped_section(): void
    {
        // Factures is hidden by default; its page only answers with the module on.
        SiteSetting::set('invoices_enabled', '1');

        foreach (AdminNotes::PAGES as $pageClass => $key) {
            $response = $this->get($pageClass::getUrl());

            $response->assertOk();
            $response->assertSee('À propos de cette page');
            $response->assertSee(AdminNotes::heading($key, 'fr'));
        }
    }

    public function test_the_note_renders_on_a_section_landing_page(): void
    {
        $this->get(QuoteResource::getUrl('index'))
            ->assertOk()
            ->assertSee('À propos de cette page')
            ->assertSee('Les devis');
    }

    public function test_the_note_stays_off_create_and_edit_screens(): void
    {
        $this->get(QuoteResource::getUrl('create'))
            ->assertOk()
            ->assertDontSee('À propos de cette page')
            // The emphasis styles ride along with the note, not with the panel.
            ->assertDontSee('.fi-admin-note', escape: false);
    }

    public function test_the_emphasis_styles_load_with_the_note(): void
    {
        $response = $this->get(QuoteResource::getUrl('index'))->assertOk();

        $response->assertSee('.fi-admin-note .fi-section', escape: false)
            ->assertSee('padding-top: 1.5rem', escape: false)
            ->assertSee('.dark .fi-admin-note', escape: false);

        $html = $response->getContent();

        $this->assertLessThan(
            strpos($html, '</head>'),
            strpos($html, '.fi-admin-note .fi-section'),
            'The callout styles must be in the document head.'
        );
    }

    public function test_the_settings_page_carries_its_own_note(): void
    {
        $this->get(SiteSettings::getUrl())
            ->assertOk()
            ->assertSee('Les paramètres du site');
    }

    public function test_both_language_buttons_are_rendered(): void
    {
        $note = Livewire::test(AdminNote::class, ['noteKey' => 'quotes']);

        foreach (AdminNotes::LOCALES as $code => $label) {
            $note->assertSee($label)
                ->assertSeeHtml('switchLocale(\''.$code.'\')');
        }
    }

    public function test_the_note_defaults_to_french(): void
    {
        Livewire::test(AdminNote::class, ['noteKey' => 'quotes'])
            ->assertSet('locale', 'fr')
            ->assertSee('Les devis');
    }

    public function test_switching_to_arabic_persists_for_that_admin(): void
    {
        Livewire::test(AdminNote::class, ['noteKey' => 'quotes'])
            ->call('switchLocale', 'ar')
            ->assertSet('locale', 'ar')
            ->assertSee('عروض الأسعار')
            ->assertDontSee('Les devis');

        $this->assertSame('ar', $this->admin->fresh()->admin_note_locale);

        // A fresh mount — the next page, or the next login — picks it back up.
        Livewire::test(AdminNote::class, ['noteKey' => 'invoices'])
            ->assertSet('locale', 'ar')
            ->assertSee('الفواتير');
    }

    public function test_the_language_choice_is_per_admin(): void
    {
        Livewire::test(AdminNote::class, ['noteKey' => 'quotes'])->call('switchLocale', 'ar');

        $this->actingAs(User::factory()->create());

        Livewire::test(AdminNote::class, ['noteKey' => 'quotes'])
            ->assertSet('locale', 'fr');
    }

    public function test_an_unsupported_locale_is_ignored(): void
    {
        Livewire::test(AdminNote::class, ['noteKey' => 'quotes'])
            ->call('switchLocale', 'de')
            ->assertSet('locale', 'fr');

        $this->assertSame('fr', $this->admin->fresh()->admin_note_locale);
    }

    public function test_arabic_notes_render_right_to_left(): void
    {
        Livewire::test(AdminNote::class, ['noteKey' => 'quotes'])
            ->call('switchLocale', 'ar')
            ->assertSeeHtml('dir="rtl"');
    }

    public function test_the_hidden_invoices_section_note_returns_with_the_module(): void
    {
        SiteSetting::set('invoices_enabled', '1');

        $this->get(\App\Filament\Resources\InvoiceResource::getUrl('index'))
            ->assertOk()
            ->assertSee('Les factures');
    }
}

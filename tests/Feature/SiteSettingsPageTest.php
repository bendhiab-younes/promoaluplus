<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Models\Quote;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\DevisDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_the_portfolio_toggle_defaults_to_off(): void
    {
        Livewire::test(SiteSettings::class)
            ->assertFormSet(['portfolio_enabled' => false]);

        $this->assertFalse(SiteSetting::enabled('portfolio_enabled'));
    }

    public function test_enabling_the_toggle_persists_and_makes_the_page_reachable(): void
    {
        Livewire::test(SiteSettings::class)
            ->set('data.portfolio_enabled', true)
            ->call('save');

        $this->assertSame('1', SiteSetting::get('portfolio_enabled'));
        $this->assertTrue(SiteSetting::enabled('portfolio_enabled'));

        $this->get(route('portfolio'))->assertOk();
    }

    public function test_disabling_the_toggle_stores_a_falsy_string_and_hides_the_page(): void
    {
        SiteSetting::set('portfolio_enabled', '1', 'boolean', 'pages');

        Livewire::test(SiteSettings::class)
            ->set('data.portfolio_enabled', false)
            ->call('save');

        $this->assertSame('0', SiteSetting::get('portfolio_enabled'));
        $this->assertFalse(SiteSetting::enabled('portfolio_enabled'));

        $this->get(route('portfolio'))->assertNotFound();
    }

    /**
     * The devis PDF's "Prestataire" block reads company_name, contact_address,
     * contact_phone, contact_email and company_tax_id straight from
     * SiteSetting — an admin editing them here must see the change on the
     * next devis they print, without touching the database directly.
     */
    public function test_editing_the_company_info_flows_into_the_devis_prestataire_block(): void
    {
        Livewire::test(SiteSettings::class)
            ->set('data.company_name', 'Nouvelle Menuiserie')
            ->set('data.contact_address', 'Nouvelle Adresse, Sfax')
            ->set('data.contact_phone', '+21611112222')
            ->set('data.contact_email', 'contact@nouvelle.tn')
            ->set('data.company_tax_id', '9998887C')
            ->call('save');

        $quote = Quote::create([
            'first_name' => 'Test', 'name' => 'Client', 'phone' => '20000000',
            'project_types' => ['other'], 'status' => 'new',
        ]);

        $company = DevisDocument::for($quote)->company();

        $this->assertSame('Nouvelle Menuiserie', $company['name']);
        $this->assertSame('Nouvelle Adresse, Sfax', $company['address']);
        $this->assertSame('+21611112222', $company['phone']);
        $this->assertSame('contact@nouvelle.tn', $company['email']);
        $this->assertSame('9998887C', $company['tax_id']);
    }

    /**
     * The whole point of the À propos fixes: what the admin types in the
     * Filament form must reach the public page. This exercises the real
     * form round-trip, not just the SiteSetting model.
     */
    public function test_the_about_fields_round_trip_from_the_form_to_the_public_page(): void
    {
        Livewire::test(SiteSettings::class)
            ->set('data.about_mission_fr', 'Mission definie par le gerant')
            ->set('data.about_vision_fr', 'Vision definie par le gerant')
            ->set('data.about_values_fr', 'Ecoute : nous prenons le temps')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Mission definie par le gerant', SiteSetting::get('about_mission_fr'));
        $this->assertSame('Vision definie par le gerant', SiteSetting::get('about_vision_fr'));

        $response = $this->get(route('about'))->assertOk();
        $response->assertSee('Mission definie par le gerant', escape: false);
        $response->assertSee('Vision definie par le gerant', escape: false);
        $response->assertSee('nous prenons le temps', escape: false);
    }

    public function test_the_story_photo_field_round_trips_to_the_public_page(): void
    {
        Livewire::test(SiteSettings::class)
            ->set('data.about_story_image_url', 'https://cdn.example.test/atelier.jpg')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('https://cdn.example.test/atelier.jpg', escape: false);
    }

    /**
     * Locks in the removal of the write-only fields: every one of these saved
     * successfully and changed nothing on the site. If someone re-adds one,
     * this fails and they have to wire it up first.
     */
    public function test_the_write_only_fields_are_gone_from_the_form(): void
    {
        $page = Livewire::test(SiteSettings::class);

        foreach ([
            'hours_weekdays', 'hours_saturday', 'hours_sunday',
            'stats_projects', 'stats_years', 'stats_satisfaction', 'stats_team',
            'contact_phone_2', 'contact_map_url',
        ] as $removed) {
            $page->assertFormFieldDoesNotExist($removed);
        }
    }
}

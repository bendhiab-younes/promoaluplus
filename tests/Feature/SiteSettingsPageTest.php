<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
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
}

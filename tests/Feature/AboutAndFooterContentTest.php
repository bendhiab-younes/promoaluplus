<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the three "admin edits must actually reach the page" fixes:
 *
 *  - À propos mission/vision/valeurs used to be shadowed by a parallel
 *    `about_history_*` key set that the admin panel never wrote to;
 *  - the "Notre histoire" photo was a hardcoded Unsplash stock image;
 *  - the footer service list came from CanonicalServiceCatalog, so services
 *    added or renamed in the admin never appeared there.
 */
class AboutAndFooterContentTest extends TestCase
{
    use RefreshDatabase;

    private function service(array $attributes = []): Service
    {
        return Service::create(array_merge([
            'slug' => 'windows',
            'title' => ['fr' => 'Fenêtres'],
            'short_description' => ['fr' => 'Courte description'],
            'description' => ['fr' => 'Description'],
            'is_active' => true,
            'sort_order' => 1,
        ], $attributes));
    }

    // ------------------------------------------------------------ À propos

    public function test_the_about_page_shows_the_mission_saved_in_the_admin(): void
    {
        SiteSetting::set('about_mission_fr', 'Mission saisie dans le panneau admin');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Mission saisie dans le panneau admin', escape: false);
    }

    public function test_the_about_page_shows_the_vision_saved_in_the_admin(): void
    {
        SiteSetting::set('about_vision_fr', 'Vision saisie dans le panneau admin');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Vision saisie dans le panneau admin', escape: false);
    }

    public function test_the_about_page_shows_the_values_saved_in_the_admin(): void
    {
        SiteSetting::set('about_values_fr', "Écoute : nous prenons le temps\nRigueur : chaque pose est vérifiée");

        $response = $this->get(route('about'))->assertOk();

        $response->assertSee('Écoute', escape: false);
        $response->assertSee('nous prenons le temps', escape: false);
        $response->assertSee('Rigueur', escape: false);
    }

    /**
     * The regression that motivated the fix: these rows used to win over the
     * admin-editable ones, so saving in Paramètres du site changed nothing.
     */
    public function test_leftover_about_history_rows_no_longer_override_the_admin(): void
    {
        SiteSetting::set('about_history_mission_fr', 'Ancien texte figé');
        SiteSetting::set('about_mission_fr', 'Mission saisie dans le panneau admin');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Mission saisie dans le panneau admin', escape: false)
            ->assertDontSee('Ancien texte figé', escape: false);
    }

    // ------------------------------------------- "Notre histoire" photo

    public function test_the_story_photo_uses_the_uploaded_image(): void
    {
        SiteSetting::set('about_story_image', 'about/atelier.webp');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('/uploads/about/atelier.webp', escape: false);
    }

    public function test_the_story_photo_falls_back_to_the_external_url(): void
    {
        SiteSetting::set('about_story_image_url', 'https://cdn.example.test/atelier.jpg');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('https://cdn.example.test/atelier.jpg', escape: false);
    }

    public function test_the_story_photo_never_falls_back_to_a_stock_image(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertDontSee('images.unsplash.com', escape: false)
            ->assertSee('images/hero/slide-1.webp', escape: false);
    }

    // -------------------------------------------------------------- Footer

    public function test_the_footer_lists_services_from_the_database(): void
    {
        $this->service(['slug' => 'verandas', 'title' => ['fr' => 'Vérandas sur mesure']]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Vérandas sur mesure', escape: false)
            ->assertSee(route('services').'#verandas', escape: false);
    }

    public function test_renaming_a_service_renames_it_in_the_footer(): void
    {
        $service = $this->service(['title' => ['fr' => 'Ancien nom']]);

        $service->update(['title' => ['fr' => 'Nouveau nom']]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Nouveau nom', escape: false)
            ->assertDontSee('Ancien nom', escape: false);
    }

    public function test_a_deactivated_service_disappears_from_the_footer(): void
    {
        $this->service(['slug' => 'pergola', 'title' => ['fr' => 'Pergolas'], 'is_active' => false]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('>Pergolas<', escape: false);
    }

    public function test_the_footer_follows_the_admin_sort_order(): void
    {
        $this->service(['slug' => 'doors', 'sort_order' => 2]);
        $this->service(['slug' => 'railings', 'sort_order' => 1]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $first = strpos($html, route('services').'#railings');
        $second = strpos($html, route('services').'#doors');

        $this->assertIsInt($first);
        $this->assertIsInt($second);
        $this->assertLessThan($second, $first, 'sort_order 1 should be linked before sort_order 2.');
    }

    // -------------------------------------------------------- Reseeding

    public function test_reseeding_does_not_revert_an_admin_edit(): void
    {
        $this->seed(\Database\Seeders\SiteSettingsSeeder::class);

        SiteSetting::set('about_mission_fr', 'Mission ajustee par le gerant');
        SiteSetting::set('about_story_fr', 'Histoire ajustee par le gerant');

        $this->seed(\Database\Seeders\SiteSettingsSeeder::class);

        $this->assertSame('Mission ajustee par le gerant', SiteSetting::get('about_mission_fr'));
        $this->assertSame('Histoire ajustee par le gerant', SiteSetting::get('about_story_fr'));
    }

    public function test_the_seeder_no_longer_writes_the_shadowing_history_keys(): void
    {
        $this->seed(\Database\Seeders\SiteSettingsSeeder::class);

        $this->assertSame(0, SiteSetting::where('key', 'like', 'about_history%')->count());
    }
}

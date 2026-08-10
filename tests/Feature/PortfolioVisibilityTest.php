<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function enablePortfolio(bool $enabled): void
    {
        SiteSetting::set('portfolio_enabled', $enabled ? '1' : '0', 'boolean', 'pages');
    }

    public function test_the_portfolio_route_is_hidden_by_default(): void
    {
        $this->get(route('portfolio'))->assertNotFound();
    }

    public function test_the_portfolio_route_is_reachable_when_enabled(): void
    {
        $this->enablePortfolio(true);

        $this->get(route('portfolio'))->assertOk();
    }

    public function test_no_page_links_to_the_portfolio_when_disabled(): void
    {
        $this->enablePortfolio(false);

        foreach (['home', 'services', 'about', 'contact'] as $page) {
            $this->get(route($page))
                ->assertOk()
                ->assertDontSee(route('portfolio'), false);
        }
    }

    public function test_the_nav_and_footer_link_to_the_portfolio_when_enabled(): void
    {
        $this->enablePortfolio(true);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('portfolio'), false);
    }

    public function test_the_sitemap_omits_the_portfolio_when_disabled(): void
    {
        $this->enablePortfolio(false);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertDontSee(route('portfolio'), false);
    }

    public function test_the_sitemap_includes_the_portfolio_when_enabled(): void
    {
        $this->enablePortfolio(true);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('portfolio'), false);
    }

    public function test_an_empty_portfolio_shows_an_empty_state_not_invented_projects(): void
    {
        $this->enablePortfolio(true);

        $this->get(route('portfolio'))
            ->assertOk()
            ->assertDontSee('Villa Moderne')
            ->assertDontSee('Résidence Carthage')
            ->assertDontSee('Immeuble Commercial')
            ->assertSee(__('messages.portfolio_empty'));
    }

    public function test_the_filter_bar_is_built_from_project_types(): void
    {
        $this->enablePortfolio(true);
        $this->seed(\Database\Seeders\ProjectTypeSeeder::class);

        \App\Models\ProjectType::create([
            'name' => ['fr' => 'Pergolas', 'en' => 'Pergolas', 'ar' => 'برجولات'],
            'slug' => 'pergola',
            'order' => 4,
            'is_active' => true,
        ]);

        $this->withSession(['locale' => 'fr'])
            ->get(route('portfolio'))
            ->assertOk()
            ->assertSee('Pergolas');
    }
}

<?php

namespace Tests\Feature;

use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Models\Service;
use App\Models\User;
use App\Support\SafeHtml;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A service's icon is admin-editable: paste an SVG into the panel and it replaces
 * the Lucide name everywhere. These cover the two things that has to get right —
 * the pasted SVG inheriting the size and colour of the slot it lands in, and the
 * mosquito-net default actually reaching the field where it can be edited.
 */
class ServiceIconTest extends TestCase
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

    public function test_a_pasted_svg_takes_the_size_and_colour_classes_of_its_slot(): void
    {
        $svg = SafeHtml::svgIcon(
            '<svg viewBox="0 0 24 24" width="512" height="512"><path d="M12 2L2 7"/></svg>',
            'w-8 h-8 text-white'
        );

        $this->assertStringContainsString('class="w-8 h-8 text-white"', $svg);
        // The intrinsic size would otherwise win and render a 512px icon in a 32px slot.
        $this->assertStringNotContainsString('width="512"', $svg);
        $this->assertStringNotContainsString('height="512"', $svg);
    }

    public function test_an_svg_without_a_viewbox_keeps_its_intrinsic_size(): void
    {
        // Nothing else carries the aspect ratio, so stripping these collapses it.
        $svg = SafeHtml::svgIcon('<svg width="24" height="24"><path d="M12 2L2 7"/></svg>', 'w-8 h-8');

        $this->assertStringContainsString('width="24"', $svg);
        $this->assertStringContainsString('height="24"', $svg);
    }

    public function test_a_service_svg_icon_reaches_the_public_page_sized(): void
    {
        $this->service(['svg_icon' => '<svg viewBox="0 0 24 24"><path d="M12 2L2 7"/></svg>']);

        $html = $this->get(route('services'))->assertOk()->getContent();

        $this->assertStringContainsString('w-8 h-8 md:w-10 md:h-10 text-white', $html);
    }

    public function test_the_seeder_gives_mosquito_nets_an_editable_svg_icon(): void
    {
        $this->seed(ServiceSeeder::class);

        $service = Service::query()->where('slug', 'mosquito_nets')->firstOrFail();

        $this->assertSame(Service::DEFAULT_SVG_ICON_BY_SLUG['mosquito_nets'], $service->svg_icon);
        // Whatever is stored has to survive the sanitiser, or the panel shows the
        // admin markup the site then silently drops.
        $this->assertNotSame('', SafeHtml::svgIcon($service->svg_icon, 'w-6 h-6'));
    }

    public function test_the_seeder_never_overwrites_an_svg_an_admin_has_set(): void
    {
        $this->seed(ServiceSeeder::class);

        $custom = '<svg viewBox="0 0 24 24"><path d="M1 1L2 2"/></svg>';
        Service::query()->where('slug', 'mosquito_nets')->update(['svg_icon' => $custom]);

        $this->seed(ServiceSeeder::class);

        $this->assertSame($custom, Service::query()->where('slug', 'mosquito_nets')->value('svg_icon'));
    }

    public function test_the_admin_can_replace_the_svg_icon_and_the_site_follows(): void
    {
        $this->actingAs(User::factory()->create());
        $service = $this->service(['svg_icon' => '<svg viewBox="0 0 24 24"><path d="M1 1L2 2"/></svg>']);

        $replacement = '<svg viewBox="0 0 24 24"><path d="M9 9L3 3"/></svg>';

        Livewire::test(EditService::class, ['record' => $service->getKey()])
            ->assertFormSet(['svg_icon' => $service->svg_icon])
            ->fillForm(['svg_icon' => $replacement])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($replacement, $service->refresh()->svg_icon);
        $this->assertStringContainsString('M9 9L3 3', $this->get(route('services'))->assertOk()->getContent());
    }

    public function test_clearing_the_svg_icon_falls_back_to_the_lucide_name(): void
    {
        $this->actingAs(User::factory()->create());
        $service = $this->service([
            'icon' => 'app-window',
            'svg_icon' => '<svg viewBox="0 0 24 24"><path d="M1 1L2 2"/></svg>',
        ]);

        Livewire::test(EditService::class, ['record' => $service->getKey()])
            ->fillForm(['svg_icon' => null])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertStringContainsString('data-lucide="app-window"', $this->get(route('services'))->getContent());
    }

    /**
     * The column was VARCHAR(255) and every real SVG overflows it. SQLite does not
     * enforce VARCHAR length, so nothing here could ever have failed on it — asserting
     * the declared *type* is the only guard that means the same thing on both engines.
     * On MySQL 8, whose default sql_mode includes STRICT_TRANS_TABLES, the old column
     * raised SQLSTATE[22001] and aborted the deploy's first db:seed.
     */
    public function test_the_svg_icon_column_is_wide_enough_for_a_real_icon(): void
    {
        $column = collect(Schema::getColumns('services'))->firstWhere('name', 'svg_icon');

        $this->assertNotNull($column, 'services.svg_icon must exist.');
        $this->assertStringContainsString(
            'text',
            strtolower((string) $column['type']),
            'svg_icon must be TEXT. As VARCHAR(255) it truncates every real SVG on MySQL.'
        );
    }

    public function test_every_default_icon_survives_a_round_trip_through_the_database(): void
    {
        foreach (Service::DEFAULT_SVG_ICON_BY_SLUG as $slug => $svg) {
            $service = $this->service(['slug' => $slug, 'svg_icon' => $svg]);

            $this->assertSame(
                $svg,
                $service->refresh()->svg_icon,
                "The default icon for {$slug} came back changed — ".strlen($svg).' characters stored.'
            );
        }
    }
}

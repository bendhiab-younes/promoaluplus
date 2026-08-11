<?php

namespace Tests\Feature;

use App\Filament\Resources\QuoteResource\Pages\CreateQuote;
use App\Filament\Resources\QuoteResource\Pages\EditQuote;
use App\Filament\Resources\QuoteResource\Pages\ListQuotes;
use App\Filament\Resources\QuoteResource\Pages\ViewQuote;
use App\Models\Quote;
use App\Models\User;
use App\Support\DevisPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Covers the admin screens around the devis builder — the pipeline list, the
 * read-only recap and the bulk re-pricing action. Rendering assertions matter
 * here: these pages are assembled from closures that only blow up at render.
 */
class DevisAdminUiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    private function devis(string $status = 'quoted'): Quote
    {
        $quote = Quote::create([
            'first_name' => 'Aymen',
            'name' => 'Hssine',
            'phone' => '26 192 898',
            'project_types' => ['other'],
            'status' => $status,
            'rates' => DevisPricing::DEFAULT_RATES,
            'show_tax' => true,
            'tax_rate' => 19,
            'discount' => 100,
            'admin_notes' => 'Rappeler après 18h',
        ]);

        $quote->items()->create([
            'description' => 'Fenêtre 2 ventaux',
            'height' => 1.41,
            'width' => 1.20,
            'quantity' => 2,
            'rate_label' => 'Aluminium',
            'shutter_rate_label' => 'Volet',
            'unit_price' => 1015.20,
            'shutter_price' => 488.40,
        ]);

        $quote->calculateTotals();

        return $quote;
    }

    public function test_the_list_is_split_into_pipeline_tabs(): void
    {
        $this->devis('new');
        $this->devis('accepted');

        Livewire::test(ListQuotes::class)
            ->assertOk()
            ->assertSee('Nouvelle demande')
            ->assertSee('Facturé');
    }

    public function test_the_default_tab_hides_nothing(): void
    {
        $pending = $this->devis('new');
        $sent = $this->devis('quoted');

        Livewire::test(ListQuotes::class)
            ->assertCanSeeTableRecords([$pending, $sent]);
    }

    public function test_duplicating_a_line_saves_a_second_line(): void
    {
        $quote = $this->devis();

        $state = Livewire::test(EditQuote::class, ['record' => $quote->getRouteKey()]);
        $itemKey = array_key_first($state->get('data')['items']);

        $state
            ->callFormComponentAction('items', 'clone', arguments: ['item' => $itemKey])
            ->call('save')
            ->assertHasNoFormErrors();

        $items = $quote->fresh()->items;

        $this->assertCount(2, $items);
        $this->assertSame(['Fenêtre 2 ventaux', 'Fenêtre 2 ventaux'], $items->pluck('description')->all());
        $this->assertSame('3007.20', $items->last()->total);
    }

    public function test_marking_a_devis_as_sent_updates_the_recap_in_place(): void
    {
        $quote = $this->devis('contacted');

        Livewire::test(ViewQuote::class, ['record' => $quote->getRouteKey()])
            ->callAction('send_quote')
            ->assertSee('Devis envoyé');

        $this->assertSame('quoted', $quote->fresh()->status);
        $this->assertNotNull($quote->fresh()->quote_number);
    }

    public function test_a_devis_can_be_read_without_opening_the_builder(): void
    {
        $quote = $this->devis();

        Livewire::test(ViewQuote::class, ['record' => $quote->getRouteKey()])
            ->assertOk()
            ->assertSee('Fenêtre 2 ventaux')
            ->assertSee('Rappeler après 18h');
    }

    public function test_an_unpriced_request_says_so_instead_of_showing_an_empty_table(): void
    {
        $quote = Quote::create([
            'first_name' => 'Sana',
            'name' => 'Ben Ali',
            'phone' => '20 000 000',
            'project_types' => ['other'],
            'status' => 'new',
        ]);

        Livewire::test(ViewQuote::class, ['record' => $quote->getRouteKey()])
            ->assertOk()
            ->assertSee('pas encore chiffré');
    }

    public function test_changing_a_rate_can_be_pushed_back_into_the_existing_lines(): void
    {
        $quote = $this->devis();
        $quote->update(['rates' => [
            ['label' => 'Aluminium', 'price' => 700, 'supplement' => 0, 'supplement_label' => null],
            ['label' => 'Volet', 'price' => 200, 'supplement' => 150, 'supplement_label' => 'Prix Moteur'],
        ]]);

        Livewire::test(EditQuote::class, ['record' => $quote->getRouteKey()])
            ->call('mountFormComponentAction', 'devis_lines', 'recalculate_all_lines')
            ->call('callMountedFormComponentAction')
            ->assertHasNoFormErrors()
            // 1.41 × 1.20 × 700 — the line follows the new rate instead of
            // keeping the price it was entered with.
            ->assertFormSet(fn (array $state): bool => (float) array_values($state['items'])[0]['unit_price'] === 1184.40);
    }

    /**
     * A client can ask for a devis covering several project types at once
     * (e.g. doors + windows + a pergola) — the admin's "Type(s) de projet"
     * field is a checkbox list, not a single select, and must persist every
     * box the admin ticks.
     */
    public function test_creating_a_devis_persists_several_project_types(): void
    {
        Livewire::test(CreateQuote::class)
            ->set('data.first_name', 'Aymen')
            ->set('data.name', 'Hssine')
            ->set('data.phone', '26 192 898')
            ->set('data.project_types', ['doors', 'windows', 'pergola'])
            ->call('create')
            ->assertHasNoFormErrors();

        $quote = Quote::firstOrFail();

        $this->assertSame(['doors', 'windows', 'pergola'], $quote->project_types);
    }

    public function test_blanking_the_project_types_selection_reports_a_form_error(): void
    {
        Livewire::test(CreateQuote::class)
            ->set('data.first_name', 'Aymen')
            ->set('data.name', 'Hssine')
            ->set('data.phone', '26 192 898')
            ->set('data.project_types', [])
            ->call('create')
            ->assertHasFormErrors(['project_types']);

        $this->assertSame(0, Quote::count());
    }

    /**
     * The list filter must match a devis that asked for ANY of the selected
     * types, not just an exact set — a request for [doors, windows] should
     * still surface a devis whose types are [windows, pergola].
     */
    public function test_the_project_types_filter_matches_any_selected_type(): void
    {
        $windowsAndPergola = Quote::create([
            'first_name' => 'A', 'name' => 'A', 'phone' => '1',
            'project_types' => ['windows', 'pergola'], 'status' => 'new',
        ]);
        $kitchenOnly = Quote::create([
            'first_name' => 'B', 'name' => 'B', 'phone' => '2',
            'project_types' => ['kitchen'], 'status' => 'new',
        ]);

        Livewire::test(ListQuotes::class)
            ->filterTable('project_types', ['doors', 'windows'])
            ->assertCanSeeTableRecords([$windowsAndPergola])
            ->assertCanNotSeeTableRecords([$kitchenOnly]);
    }
}

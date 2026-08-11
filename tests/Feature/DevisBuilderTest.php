<?php

namespace Tests\Feature;

use App\Filament\Resources\QuoteResource\Pages\CreateQuote;
use App\Filament\Resources\QuoteResource\Pages\EditQuote;
use App\Models\Quote;
use App\Models\User;
use App\Support\DevisPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Covers the admin-facing devis builder: the prices the admin sees while
 * typing must be the prices that get saved.
 */
class DevisBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_a_new_devis_starts_with_the_usual_joinery_rates(): void
    {
        Livewire::test(CreateQuote::class)
            ->assertFormFieldExists('rates')
            ->assertFormSet(fn (array $state): bool => collect($state['rates'])->pluck('label')->all() === ['Aluminium', 'Volet']);
    }

    public function test_choosing_a_rate_fills_the_line_price_from_the_dimensions(): void
    {
        Livewire::test(CreateQuote::class)
            ->fillForm([
                'first_name' => 'Aymen',
                'name' => 'Hssine',
                'phone' => '26 192 898',
                'project_types' => ['other'],
                'items' => [
                    [
                        'description' => 'Fenêtre 2 ventaux et volet',
                        'height' => 1.41,
                        'width' => 1.20,
                        'quantity' => 2,
                        'rate_label' => 'Aluminium',
                        'shutter_rate_label' => 'Volet',
                    ],
                ],
            ])
            ->assertFormSet(function (array $state): bool {
                $line = array_values($state['items'])[0];

                // 1.41 × 1.20 × 600 for the frame, and × 200 + 150 motor for
                // the shutter — filled in without the admin typing a price.
                return (float) $line['unit_price'] === 1015.20
                    && (float) $line['shutter_price'] === 488.40;
            });
    }

    public function test_a_saved_devis_keeps_the_prices_shown_in_the_form(): void
    {
        Livewire::test(CreateQuote::class)
            ->fillForm([
                'first_name' => 'Aymen',
                'name' => 'Hssine',
                'phone' => '26 192 898',
                'project_types' => ['other'],
                'discount' => 618,
                'show_tax' => false,
                'items' => [
                    [
                        'description' => 'Fenêtre 2 ventaux et volet',
                        'height' => 1.41,
                        'width' => 1.20,
                        'quantity' => 2,
                        'rate_label' => 'Aluminium',
                        'shutter_rate_label' => 'Volet',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $quote = Quote::with('items')->latest('id')->first();

        $this->assertSame('3007.20', $quote->items->first()->total);
        $this->assertSame('3007.20', $quote->subtotal);
        $this->assertSame('2389.20', $quote->total);
        $this->assertSame(['Aluminium', 'Volet'], collect($quote->rates)->pluck('label')->all());
    }

    public function test_editing_dimensions_leaves_a_hand_typed_price_alone(): void
    {
        $quote = Quote::create([
            'first_name' => 'Aymen', 'name' => 'Hssine', 'phone' => '26 192 898',
            'project_types' => ['other'], 'status' => 'quoted',
            'rates' => DevisPricing::DEFAULT_RATES,
        ]);
        $quote->items()->create([
            'description' => 'Fenêtre hors barème',
            'height' => 0.65, 'width' => 0.80, 'quantity' => 1,
            'rate_label' => null, 'unit_price' => 260, 'shutter_price' => 0,
        ]);

        Livewire::test(EditQuote::class, ['record' => $quote->getRouteKey()])
            ->assertFormSet(fn (array $state): bool => (float) array_values($state['items'])[0]['unit_price'] === 260.0)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('260.00', $quote->fresh('items')->items->first()->unit_price);
    }

    public function test_totals_are_recalculated_after_saving_an_edit(): void
    {
        $quote = Quote::create([
            'first_name' => 'Aymen', 'name' => 'Hssine', 'phone' => '26 192 898',
            'project_types' => ['other'], 'status' => 'quoted', 'show_tax' => true, 'tax_rate' => 19,
            'rates' => DevisPricing::DEFAULT_RATES,
        ]);
        $quote->items()->create([
            'description' => 'Ligne', 'quantity' => 1, 'unit_price' => 1000, 'shutter_price' => 0,
        ]);

        Livewire::test(EditQuote::class, ['record' => $quote->getRouteKey()])
            ->call('save')
            ->assertHasNoFormErrors();

        $quote->refresh();

        $this->assertSame('190.00', $quote->tax_amount);
        $this->assertSame('1190.00', $quote->total);
    }

    public function test_a_devis_can_be_created_without_an_email_or_description(): void
    {
        Livewire::test(CreateQuote::class)
            ->fillForm([
                'first_name' => 'Aymen',
                'name' => 'Hssine',
                'phone' => '26 192 898',
                'client_address' => 'Sousse-Khzema',
                'project_types' => ['other'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $quote = Quote::latest('id')->first();

        $this->assertNull($quote->email);
        $this->assertNull($quote->description);
        $this->assertSame('Aymen Hssine', $quote->full_name);
    }
}

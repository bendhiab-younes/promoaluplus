<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Support\DevisDocument;
use App\Support\DevisPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevisPricingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The rates from the workshop's joinery devis.
     */
    private const JOINERY_RATES = [
        ['label' => 'Aluminium', 'price' => 600, 'supplement' => 0, 'supplement_label' => null],
        ['label' => 'Volet', 'price' => 200, 'supplement' => 150, 'supplement_label' => 'Prix Moteur'],
    ];

    private function quote(array $attributes = []): Quote
    {
        return Quote::create(array_merge([
            'first_name' => 'Aymen',
            'name' => 'Hssine',
            'phone' => '26 192 898',
            'client_address' => 'Sousse-Khzema',
            'project_type' => 'other',
            'status' => 'quoted',
            'rates' => self::JOINERY_RATES,
        ], $attributes));
    }

    public function test_unit_price_is_the_surface_at_the_rate_price(): void
    {
        $this->assertSame(
            1015.2,
            DevisPricing::unitPrice(self::JOINERY_RATES, 'Aluminium', 1.41, 1.20)
        );
    }

    public function test_a_rate_supplement_is_charged_once_per_unit(): void
    {
        // 1.41 × 1.20 × 200 = 338.40, plus the 150 motor.
        $this->assertSame(
            488.4,
            DevisPricing::unitPrice(self::JOINERY_RATES, 'Volet', 1.41, 1.20)
        );
    }

    public function test_unit_price_is_null_when_the_line_has_no_usable_rate(): void
    {
        $this->assertNull(DevisPricing::unitPrice(self::JOINERY_RATES, null, 1.41, 1.20));
        $this->assertNull(DevisPricing::unitPrice(self::JOINERY_RATES, 'Inconnu', 1.41, 1.20));
    }

    public function test_duplicate_rate_labels_collapse_to_one_reachable_rate(): void
    {
        $rates = DevisPricing::normalizeRates([
            ['label' => 'Aluminium', 'price' => 600],
            ['label' => 'Aluminium', 'price' => 700],
            ['label' => '', 'price' => 900],
        ]);

        $this->assertCount(1, $rates);
        $this->assertSame(700.0, $rates[0]['price']);
    }

    public function test_legend_gives_a_supplement_its_own_line(): void
    {
        $this->assertSame([
            ['label' => 'Prix M² Aluminium', 'price' => 600.0],
            ['label' => 'Prix M² Volet', 'price' => 200.0],
            ['label' => 'Prix Moteur', 'price' => 150.0],
        ], DevisPricing::legendLines(self::JOINERY_RATES));
    }

    public function test_line_total_adds_the_shutter_before_multiplying_by_quantity(): void
    {
        $quote = $this->quote();

        $item = $quote->items()->create([
            'description' => 'Fenêtre 2 ventaux et volet',
            'height' => 1.41,
            'width' => 1.20,
            'quantity' => 2,
            'rate_label' => 'Aluminium',
            'shutter_rate_label' => 'Volet',
            'unit_price' => 1015.20,
            'shutter_price' => 488.40,
        ]);

        $this->assertSame('3007.20', $item->total);
        $this->assertSame('m²', $item->unit);
    }

    public function test_totals_go_straight_from_total_to_net_without_tva(): void
    {
        $quote = $this->quote(['discount' => 618, 'show_tax' => false]);
        $quote->items()->create([
            'description' => 'Ligne', 'quantity' => 1, 'unit_price' => 14374, 'shutter_price' => 0,
        ]);

        $quote->refresh()->calculateTotals();

        $this->assertSame('14374.00', $quote->subtotal);
        $this->assertSame('0.00', $quote->tax_amount);
        $this->assertSame('13756.00', $quote->total);
    }

    public function test_enabling_tva_adds_it_after_the_discount(): void
    {
        $quote = $this->quote(['discount' => 100, 'show_tax' => true, 'tax_rate' => 19]);
        $quote->items()->create([
            'description' => 'Ligne', 'quantity' => 1, 'unit_price' => 1100, 'shutter_price' => 0,
        ]);

        $quote->refresh()->calculateTotals();

        // (1100 − 100) × 19% = 190
        $this->assertSame('190.00', $quote->tax_amount);
        $this->assertSame('1190.00', $quote->total);
    }

    public function test_invoicing_an_untaxed_devis_does_not_add_tva(): void
    {
        $quote = $this->quote(['show_tax' => false, 'discount' => 0]);
        $quote->items()->create([
            'description' => 'Fenêtre', 'quantity' => 1, 'unit_price' => 900, 'shutter_price' => 100,
        ]);
        $quote->refresh()->calculateTotals();

        $invoice = $quote->createInvoice();

        $this->assertSame('0.00', $invoice->tax_rate);
        $this->assertSame($quote->total, $invoice->total);
        // The shutter is folded into the invoice line's single price column.
        $this->assertSame('1000.00', $invoice->items->first()->unit_price);
    }

    public function test_document_uses_the_shutter_layout_only_when_a_line_has_one(): void
    {
        $quote = $this->quote();
        $quote->items()->create([
            'description' => 'Garde-corps', 'height' => 1.10, 'width' => 2.50,
            'quantity' => 1, 'rate_label' => 'Aluminium', 'unit_price' => 1650, 'shutter_price' => 0,
        ]);

        $document = DevisDocument::for($quote->fresh('items'));

        $this->assertFalse($document->hasShutters());
        $this->assertSame(['Designation', 'L', 'H', 'Qté', 'PU.HTVA', 'PT.HTVA'], $document->columns());

        $quote->items()->create([
            'description' => 'Fenêtre et volet', 'height' => 1.30, 'width' => 1.20,
            'quantity' => 1, 'rate_label' => 'Aluminium', 'shutter_rate_label' => 'Volet',
            'unit_price' => 936, 'shutter_price' => 462,
        ]);

        $document = DevisDocument::for($quote->fresh('items'));

        $this->assertTrue($document->hasShutters());
        $this->assertSame(['Designation', 'H', 'L', 'Qté', 'P.VOLET', 'P.ALU', 'PT.HTVA'], $document->columns());
    }

    public function test_product_notes_become_bullets_ignoring_blank_and_starred_lines(): void
    {
        $quote = $this->quote([
            'product_notes' => "* Aluminium 1 choix\n\n  Double vitrage 1.8mm\n*Lame FLORA\n   \n",
        ]);

        $this->assertSame([
            'Aluminium 1 choix',
            'Double vitrage 1.8mm',
            'Lame FLORA',
        ], $quote->productNoteLines());
    }

    public function test_amounts_print_with_three_decimals_and_no_separator(): void
    {
        $this->assertSame('14318.000', DevisPricing::format(14318));
        $this->assertSame('488.400', DevisPricing::format(488.4));
    }
}

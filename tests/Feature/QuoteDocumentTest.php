<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\User;
use App\Support\DevisDocument;
use App\Support\DevisSpreadsheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Tests\TestCase;

class QuoteDocumentTest extends TestCase
{
    use RefreshDatabase;

    private function devis(): Quote
    {
        $quote = Quote::create([
            'first_name' => 'Aymen',
            'name' => 'Hssine',
            'phone' => '26 192 898',
            'client_address' => 'Sousse-Khzema',
            'project_type' => 'other',
            'status' => 'quoted',
            'quote_number' => 'DEV-2026-0099',
            'devis_date' => '2026-07-12',
            'discount' => 618,
            'show_tax' => false,
            'rates' => [
                ['label' => 'Aluminium', 'price' => 600, 'supplement' => 0, 'supplement_label' => null],
                ['label' => 'Volet', 'price' => 200, 'supplement' => 150, 'supplement_label' => 'Prix Moteur'],
            ],
            'product_notes' => "Aluminium 1 choix TPR\nY compris fourniture et pose",
        ]);

        $quote->items()->create([
            'description' => 'Fenetre a la française 2 ventaux et volet electrique',
            'height' => 1.41, 'width' => 1.20, 'quantity' => 2,
            'rate_label' => 'Aluminium', 'shutter_rate_label' => 'Volet',
            'unit_price' => 1015.20, 'shutter_price' => 488.40, 'order' => 0,
        ]);
        $quote->items()->create([
            'description' => 'fenetre a la française 1 ventaux',
            'height' => 0.85, 'width' => 1.20, 'quantity' => 1,
            'rate_label' => 'Aluminium', 'unit_price' => 612, 'shutter_price' => 0, 'order' => 1,
        ]);

        return $quote->refresh();
    }

    public function test_devis_downloads_require_authentication(): void
    {
        $quote = $this->devis();

        $login = route('filament.admin.auth.login');

        $this->get(route('quote.pdf', $quote))->assertRedirect($login);
        $this->get(route('quote.excel', $quote))->assertRedirect($login);
    }

    public function test_admin_downloads_the_devis_as_pdf(): void
    {
        $quote = $this->devis();

        $response = $this->actingAs(User::factory()->create())->get(route('quote.pdf', $quote));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('Devis-DEV-2026-0099.pdf', $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_admin_downloads_the_devis_as_excel(): void
    {
        $quote = $this->devis();

        $response = $this->actingAs(User::factory()->create())->get(route('quote.excel', $quote));

        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $this->assertStringContainsString('Devis-DEV-2026-0099.xlsx', $response->headers->get('content-disposition'));
        // A xlsx is a zip archive.
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    public function test_spreadsheet_prices_are_formulas_that_recalculate_from_the_rate_cells(): void
    {
        $quote = $this->devis();
        $path = tempnam(sys_get_temp_dir(), 'devis').'.xlsx';

        $spreadsheet = (new DevisSpreadsheet(DevisDocument::for($quote)))->build();
        (new XlsxWriter($spreadsheet))->save($path);

        $loaded = (new XlsxReader)->load($path);
        $sheet = $loaded->getActiveSheet();

        // Lines start on row 16; with two of them the rate legend and the
        // totals sit side by side from row 20, as on the paper devis.
        $this->assertSame('=C16*D16*$C$20', $sheet->getCell('G16')->getValue());
        $this->assertSame('=C16*D16*$C$21+$C$22', $sheet->getCell('F16')->getValue());
        $this->assertSame('=(F16+G16)*E16', $sheet->getCell('H16')->getValue());
        $this->assertSame('Prix M² Aluminium', $sheet->getCell('B20')->getValue());
        $this->assertSame(600.0, $sheet->getCell('C20')->getValue());
        $this->assertSame('Prix Moteur', $sheet->getCell('B22')->getValue());
        $this->assertSame('Net a payer', $sheet->getCell('G22')->getValue());

        // Raising the m² rate must flow through to the net payable.
        $sheet->getCell('C20')->setValue(650);
        $loaded->getCalculationEngine()->clearCalculationCache();

        $this->assertEqualsWithDelta(1.41 * 1.20 * 650, $sheet->getCell('G16')->getCalculatedValue(), 0.001);
        $this->assertEqualsWithDelta(
            $sheet->getCell('H20')->getCalculatedValue() - 618,
            $sheet->getCell('H22')->getCalculatedValue(),
            0.001
        );

        unlink($path);
    }

    public function test_an_overridden_price_is_written_as_a_value_not_a_formula(): void
    {
        $quote = $this->devis();
        // The workshop quoted this window below the standard m² rate.
        $quote->items()->first()->update(['unit_price' => 260]);

        $path = tempnam(sys_get_temp_dir(), 'devis').'.xlsx';
        (new XlsxWriter((new DevisSpreadsheet(DevisDocument::for($quote->fresh('items'))))->build()))->save($path);

        $sheet = (new XlsxReader)->load($path)->getActiveSheet();

        $this->assertSame(260.0, $sheet->getCell('G16')->getValue());

        unlink($path);
    }

    public function test_pdf_falls_back_to_the_quote_id_when_no_number_was_issued_yet(): void
    {
        $quote = $this->devis();
        $quote->update(['quote_number' => null]);

        $response = $this->actingAs(User::factory()->create())->get(route('quote.pdf', $quote));

        $response->assertOk();
        $this->assertStringContainsString(
            'Devis-BROUILLON-'.$quote->id.'.pdf',
            $response->headers->get('content-disposition')
        );
    }
}

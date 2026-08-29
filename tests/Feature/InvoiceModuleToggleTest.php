<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Facturation ships hidden. Everything that leads to an invoice — the Filament
 * resource, the "Facturer" action on a devis, the PDF route — must stay out of
 * sight until the toggle in Paramètres du site is switched on, and come back
 * intact when it is.
 */
class InvoiceModuleToggleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    private function makeInvoice(): Invoice
    {
        $invoice = Invoice::create([
            'invoice_number' => 'FAC-2026-0001',
            'client_name' => 'Ben Dhiab',
            'issue_date' => '2026-08-09',
            'total' => 1200,
        ]);

        $invoice->items()->create([
            'description' => 'Fenêtre coulissante',
            'quantity' => 2,
            'unit_price' => 600,
            'total' => 1200,
        ]);

        return $invoice;
    }

    public function test_the_invoicing_toggle_defaults_to_off(): void
    {
        Livewire::test(SiteSettings::class)
            ->assertFormSet(['invoices_enabled' => false]);

        $this->assertFalse(Invoice::moduleEnabled());
    }

    public function test_the_toggle_persists_both_ways(): void
    {
        Livewire::test(SiteSettings::class)
            ->set('data.invoices_enabled', true)
            ->call('save');

        $this->assertSame('1', SiteSetting::get('invoices_enabled'));
        $this->assertTrue(Invoice::moduleEnabled());

        Livewire::test(SiteSettings::class)
            ->set('data.invoices_enabled', false)
            ->call('save');

        $this->assertSame('0', SiteSetting::get('invoices_enabled'));
        $this->assertFalse(Invoice::moduleEnabled());
    }

    public function test_the_factures_resource_is_hidden_and_closed_while_disabled(): void
    {
        $this->assertFalse(InvoiceResource::shouldRegisterNavigation());
        $this->assertFalse(InvoiceResource::canAccess());

        $this->get(InvoiceResource::getUrl('index'))->assertForbidden();
    }

    public function test_the_factures_resource_returns_once_enabled(): void
    {
        SiteSetting::set('invoices_enabled', '1');

        $this->assertTrue(InvoiceResource::shouldRegisterNavigation());
        $this->assertTrue(InvoiceResource::canAccess());

        $this->get(InvoiceResource::getUrl('index'))->assertOk();
    }

    public function test_the_invoice_pdf_route_404s_while_disabled(): void
    {
        $invoice = $this->makeInvoice();

        $this->get(route('invoice.pdf', $invoice))->assertNotFound();

        SiteSetting::set('invoices_enabled', '1');

        $this->get(route('invoice.pdf', $invoice))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_disabling_the_module_keeps_existing_invoices(): void
    {
        SiteSetting::set('invoices_enabled', '1');
        $invoice = $this->makeInvoice();

        SiteSetting::set('invoices_enabled', '0');

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseCount('invoice_items', 1);
    }

    public function test_the_accepted_status_hint_drops_the_invoice_call_to_action(): void
    {
        $this->assertStringNotContainsString(
            'facture',
            \App\Filament\Resources\QuoteResource::statusHint('accepted')
        );

        SiteSetting::set('invoices_enabled', '1');

        $this->assertStringContainsString(
            'facture',
            \App\Filament\Resources\QuoteResource::statusHint('accepted')
        );
    }
}

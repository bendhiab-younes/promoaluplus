<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    protected function getCompanyInfo(): array
    {
        return [
            'name' => SiteSetting::get('company_name', 'Promo Alu Plus'),
            'phone' => SiteSetting::get('contact_phone', '+216 12 345 678'),
            'email' => SiteSetting::get('contact_email', 'contact@promoaluplus.tn'),
            'address' => SiteSetting::get('contact_address', 'Tunis, Tunisie'),
        ];
    }

    public function quote(Quote $quote)
    {
        $quote->load('items');
        
        $pdf = Pdf::loadView('pdf.quote', [
            'quote' => $quote,
            'company' => $this->getCompanyInfo(),
        ]);

        $filename = 'Devis-' . ($quote->quote_number ?? $quote->id) . '.pdf';
        
        return $pdf->download($filename);
    }

    public function invoice(Invoice $invoice)
    {
        $invoice->load('items');
        
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'company' => $this->getCompanyInfo(),
        ]);

        $filename = 'Facture-' . $invoice->invoice_number . '.pdf';
        
        return $pdf->download($filename);
    }
}

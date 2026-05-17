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
            'name' => SiteSetting::get('company_name', 'PromoAlu+'),
            'phone' => SiteSetting::get('contact_phone', '+21626192898'),
            'email' => SiteSetting::get('contact_email', 'promoaluplus@gmail.com'),
            'address' => SiteSetting::get('contact_address', 'Sousse, Tunisie'),
        ];
    }

    public function quote(Quote $quote)
    {
        $quote->load('items');

        $pdf = Pdf::loadView('pdf.quote', [
            'quote' => $quote,
            'company' => $this->getCompanyInfo(),
        ]);

        $filename = 'Devis-'.($quote->quote_number ?? $quote->id).'.pdf';

        return $pdf->download($filename);
    }

    public function invoice(Invoice $invoice)
    {
        $invoice->load('items');

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'company' => $this->getCompanyInfo(),
        ]);

        $filename = 'Facture-'.$invoice->invoice_number.'.pdf';

        return $pdf->download($filename);
    }
}

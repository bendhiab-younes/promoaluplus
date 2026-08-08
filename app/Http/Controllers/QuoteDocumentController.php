<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Support\DevisDocument;
use App\Support\DevisSpreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Renders a devis in whichever format the admin picked: a PDF to send to the
 * client, or a spreadsheet to keep working in.
 */
class QuoteDocumentController extends Controller
{
    public function pdf(Quote $quote): Response
    {
        $document = DevisDocument::for($quote);

        $pdf = Pdf::loadView('pdf.quote', [
            'document' => $document,
            'quote' => $quote,
        ]);

        return $pdf->download($quote->documentFilename('pdf'));
    }

    public function excel(Quote $quote): StreamedResponse
    {
        $spreadsheet = (new DevisSpreadsheet(DevisDocument::for($quote)))->build();
        $filename = $quote->documentFilename('xlsx');

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

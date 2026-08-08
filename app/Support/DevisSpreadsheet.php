<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Writes a devis as a working spreadsheet.
 *
 * Prices are written as formulas pointing at the rate cells, and the totals as
 * SUM/subtraction, so the admin can keep negotiating in Excel — change a
 * dimension or a m² price and the document recalculates itself. A line whose
 * price was typed over in the admin is written as a literal so the override
 * is not silently undone by the formula.
 */
class DevisSpreadsheet
{
    private const BORDER_COLOR = 'FF7F7F9C';

    private const ACCENT_COLOR = 'FFC0504D';

    private const MONEY_FORMAT = '0.000';

    private const DIMENSION_FORMAT = '0.00';

    private const FIRST_ITEM_ROW = 15;

    /** Tracks how far down the totals block reached, so notes land below it. */
    private int $lastUsedRow = 0;

    public function __construct(
        private readonly DevisDocument $document,
    ) {}

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Devis');

        $this->writeHeader($sheet);

        $rateRows = $this->writeRatesLegend($sheet);
        [$firstItemRow, $lastItemRow] = $this->writeItems($sheet, $rateRows);
        $this->writeTotals($sheet, $firstItemRow, $lastItemRow);
        $this->writeProductNotes($sheet, $rateRows);
        $this->applyLayout($sheet);

        $sheet->setSelectedCell('A1');

        return $spreadsheet;
    }

    private function writeHeader(Worksheet $sheet): void
    {
        $company = $this->document->company();
        $client = $this->document->client();

        $lines = [
            2 => 'Ste: '.$company['name'],
            3 => 'adresse: '.$company['address'],
            4 => 'MF: '.$company['tax_id'],
            5 => 'Tel : '.$company['phone'],
            6 => 'Clien : '.$client['name'],
            7 => 'adresse: '.($client['address'] ?? ''),
        ];

        foreach ($lines as $row => $value) {
            $sheet->setCellValue('D'.$row, $value);
        }

        $sheet->getStyle('D2:F7')->getBorders()->getOutline()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB(self::BORDER_COLOR);
        $sheet->getStyle('D5:F5')->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB(self::BORDER_COLOR);

        $sheet->setCellValue('B10', 'DEVIS');
        $sheet->mergeCells('B10:G10');
        $sheet->getStyle('B10')->getFont()->setBold(true)->setSize(16)
            ->getColor()->setARGB('FFC00000');
        $sheet->getStyle('B10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $reference = $this->document->reference();
        $sheet->setCellValue('B11', 'DATE: '.$this->document->date().($reference ? '     N° '.$reference : ''));
        $sheet->mergeCells('B11:G11');
        $sheet->getStyle('B11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $this->insertLogo($sheet);
    }

    private function insertLogo(Worksheet $sheet): void
    {
        $logoPath = $this->document->logoPath();

        if ($logoPath === null) {
            return;
        }

        $drawing = new Drawing;
        $drawing->setName($this->document->company()['name']);
        $drawing->setDescription('Logo');
        $drawing->setPath($logoPath);
        $drawing->setHeight(120);
        $drawing->setCoordinates('B2');
        $drawing->setOffsetX(4);
        $drawing->setOffsetY(4);
        $drawing->setWorksheet($sheet);
    }

    /**
     * The rate legend doubles as the spreadsheet's input cells: every line
     * price formula points back here.
     *
     * @return array<string, array{price_row: int, supplement_row: int|null}>
     */
    private function writeRatesLegend(Worksheet $sheet): array
    {
        $rows = [];
        $row = $this->legendStartRow();

        foreach ($this->document->quote->devisRates() as $rate) {
            $sheet->setCellValue('B'.$row, 'Prix M² '.$rate['label']);
            $sheet->setCellValue('C'.$row, $rate['price']);
            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode(self::MONEY_FORMAT);
            $priceRow = $row;
            $supplementRow = null;
            $row++;

            if ($rate['supplement'] > 0) {
                $sheet->setCellValue('B'.$row, $rate['supplement_label'] ?? 'Supplément '.$rate['label']);
                $sheet->setCellValue('C'.$row, $rate['supplement']);
                $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode(self::MONEY_FORMAT);
                $supplementRow = $row;
                $row++;
            }

            $rows[$rate['label']] = [
                'price_row' => $priceRow,
                'supplement_row' => $supplementRow,
            ];
        }

        if ($rows !== []) {
            $lastRow = $row - 1;
            $sheet->getStyle('B'.$this->legendStartRow().':C'.$lastRow)
                ->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB(self::BORDER_COLOR);
        }

        return $rows;
    }

    /**
     * The legend sits two rows under the last line, mirroring the paper devis.
     */
    private function legendStartRow(): int
    {
        return self::FIRST_ITEM_ROW + 1 + max(count($this->document->rows()), 1) + 2;
    }

    /**
     * @param  array<string, array{price_row: int, supplement_row: int|null}>  $rateRows
     * @return array{0: int, 1: int}
     */
    private function writeItems(Worksheet $sheet, array $rateRows): array
    {
        $hasShutters = $this->document->hasShutters();
        $columns = $this->document->columns();
        $headerRow = self::FIRST_ITEM_ROW;

        foreach ($columns as $index => $heading) {
            $sheet->setCellValue(chr(ord('B') + $index).$headerRow, $heading);
        }

        $lastColumn = chr(ord('B') + count($columns) - 1);
        $sheet->getStyle('B'.$headerRow.':'.$lastColumn.$headerRow)->getFont()->setBold(true)
            ->getColor()->setARGB(self::ACCENT_COLOR);
        $sheet->getStyle('B'.$headerRow.':'.$lastColumn.$headerRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = $headerRow + 1;
        $firstItemRow = $row;

        foreach ($this->document->rows() as $line) {
            $sheet->setCellValueExplicit('B'.$row, $line['designation'], DataType::TYPE_STRING);

            // The two devis formats disagree on which dimension comes first.
            $dimensions = $hasShutters
                ? ['C' => $line['height'], 'D' => $line['width']]
                : ['C' => $line['width'], 'D' => $line['height']];

            foreach ($dimensions as $column => $value) {
                if ($value > 0) {
                    $sheet->setCellValue($column.$row, $value);
                }
            }

            $sheet->setCellValue('E'.$row, $line['quantity']);

            $item = $line['item'];

            if ($hasShutters) {
                $this->writePriceCell($sheet, 'F', $row, $line['shutter_price'], $rateRows, $item->shutter_rate_label, $line);
                $this->writePriceCell($sheet, 'G', $row, $line['unit_price'], $rateRows, $item->rate_label, $line);
                $sheet->setCellValue('H'.$row, '=(F'.$row.'+G'.$row.')*E'.$row);
            } else {
                $this->writePriceCell($sheet, 'F', $row, $line['unit_price'], $rateRows, $item->rate_label, $line);
                $sheet->setCellValue('G'.$row, '=F'.$row.'*E'.$row);
            }

            $row++;
        }

        $lastItemRow = max($row - 1, $firstItemRow);

        $sheet->getStyle('B'.$headerRow.':'.$lastColumn.$lastItemRow)
            ->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB(self::BORDER_COLOR);
        $sheet->getStyle('C'.$firstItemRow.':D'.$lastItemRow)
            ->getNumberFormat()->setFormatCode(self::DIMENSION_FORMAT);
        $sheet->getStyle('F'.$firstItemRow.':'.$lastColumn.$lastItemRow)
            ->getNumberFormat()->setFormatCode(self::MONEY_FORMAT);
        $sheet->getStyle('B'.$firstItemRow.':B'.$lastItemRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('C'.$firstItemRow.':E'.$lastItemRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [$firstItemRow, $lastItemRow];
    }

    /**
     * Writes a unit price as a formula against its rate, or as a plain value
     * when the line has no rate or the admin overrode the computed price.
     *
     * @param  array<string, array{price_row: int, supplement_row: int|null}>  $rateRows
     * @param  array{height: float, width: float}  $line
     */
    private function writePriceCell(
        Worksheet $sheet,
        string $column,
        int $row,
        float $price,
        array $rateRows,
        ?string $rateLabel,
        array $line,
    ): void {
        $cell = $column.$row;

        if ($price <= 0.0 && $rateLabel === null) {
            return;
        }

        $rate = $rateLabel !== null ? ($rateRows[$rateLabel] ?? null) : null;

        if ($rate === null) {
            $sheet->setCellValue($cell, $price);

            return;
        }

        $computed = DevisPricing::unitPrice(
            $this->document->quote->devisRates(),
            $rateLabel,
            $line['height'],
            $line['width'],
        );

        if ($computed === null || abs($computed - $price) > 0.005) {
            $sheet->setCellValue($cell, $price);

            return;
        }

        $formula = '=C'.$row.'*D'.$row.'*$C$'.$rate['price_row'];

        if ($rate['supplement_row'] !== null) {
            $formula .= '+$C$'.$rate['supplement_row'];
        }

        $sheet->setCellValue($cell, $formula);
    }

    private function writeTotals(Worksheet $sheet, int $firstItemRow, int $lastItemRow): void
    {
        $totals = $this->document->totals();
        $hasShutters = $this->document->hasShutters();
        $totalColumn = $hasShutters ? 'H' : 'G';
        $labelColumn = $hasShutters ? 'G' : 'F';

        $row = $this->legendStartRow();

        $sheet->setCellValue($labelColumn.$row, 'Total');
        $sheet->setCellValue($totalColumn.$row, '=SUM('.$totalColumn.$firstItemRow.':'.$totalColumn.$lastItemRow.')');
        $totalRow = $row;
        $row++;

        $sheet->setCellValue($labelColumn.$row, 'Remise');
        $sheet->setCellValue($totalColumn.$row, $totals['discount']);
        $discountRow = $row;
        $row++;

        $netFormula = '='.$totalColumn.$totalRow.'-'.$totalColumn.$discountRow;

        if ($totals['show_tax']) {
            $sheet->setCellValue($labelColumn.$row, 'TVA '.rtrim(rtrim(number_format($totals['tax_rate'], 2, '.', ''), '0'), '.').'%');
            $sheet->setCellValue($totalColumn.$row, '=('.$totalColumn.$totalRow.'-'.$totalColumn.$discountRow.')*'.($totals['tax_rate'] / 100));
            $netFormula .= '+'.$totalColumn.$row;
            $row++;
        }

        $sheet->setCellValue($labelColumn.$row, 'Net a payer');
        $sheet->setCellValue($totalColumn.$row, $netFormula);
        $sheet->getStyle($labelColumn.$row.':'.$totalColumn.$row)->getFont()->setBold(true);

        $sheet->getStyle($labelColumn.$totalRow.':'.$totalColumn.$row)
            ->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB(self::BORDER_COLOR);
        $sheet->getStyle($totalColumn.$totalRow.':'.$totalColumn.$row)
            ->getNumberFormat()->setFormatCode(self::MONEY_FORMAT);

        $this->lastUsedRow = max($this->lastUsedRow, $row);
    }

    /**
     * @param  array<string, array{price_row: int, supplement_row: int|null}>  $rateRows
     */
    private function writeProductNotes(Worksheet $sheet, array $rateRows): void
    {
        $notes = $this->document->notes();

        if ($notes === []) {
            return;
        }

        $legendEnd = $this->legendStartRow() + max(count($rateRows), 1);
        $row = max($legendEnd, $this->lastUsedRow) + 2;

        $sheet->setCellValue('B'.$row, 'Information sur produit');
        $sheet->getStyle('B'.$row)->getFont()->setBold(true)->getColor()->setARGB(self::ACCENT_COLOR);
        $row++;

        foreach ($notes as $note) {
            $sheet->setCellValueExplicit('B'.$row, '* '.$note, DataType::TYPE_STRING);
            $row++;
        }
    }

    private function applyLayout(Worksheet $sheet): void
    {
        $sheet->getColumnDimension('A')->setWidth(2);
        $sheet->getColumnDimension('B')->setWidth(34);

        foreach (['C', 'D', 'E'] as $column) {
            $sheet->getColumnDimension($column)->setWidth(9);
        }

        foreach (['F', 'G', 'H'] as $column) {
            $sheet->getColumnDimension($column)->setWidth(14);
        }

        for ($row = 2; $row <= 8; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(16);
        }

        $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.4)->setRight(0.4);
    }
}

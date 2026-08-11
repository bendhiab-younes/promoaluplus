@php
    use App\Support\DevisPricing;

    $company = $document->company();
    $client = $document->client();
    $hasShutters = $document->hasShutters();
    $rows = $document->rows();
    $legend = $document->legend();
    $totals = $document->totals();
    $notes = $document->notes();
    $logo = $document->logoPath();
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis {{ $document->reference() ?? 'Promo Alu Plus' }}</title>
    <style>
        /* The bottom page margin reserves room for the fixed footer band. */
        @page { margin: 20mm 16mm 24mm 16mm; }

        /* NOTE: this must stay scoped to "body *" and never "*" — dompdf folds
           the @page margin into the <html> element's own margin, so a bare
           universal-selector reset (which also matches <html>) silently
           zeroes it back out and the page margins above stop applying. */
        body, body * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            /* Helvetica is a PDF core font: French accents and "²" all live in
               cp1252, so nothing needs embedding and a devis stays ~70 KB
               instead of ~900 KB once DejaVu is bundled in. */
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #1a1a1a;
        }

        /* ---- Header : logo on the left, party boxes and title on the right ---- */
        .masthead { width: 100%; margin-bottom: 18px; }
        .masthead td { vertical-align: top; }
        .masthead .logo-cell { width: 33%; vertical-align: middle; }
        .masthead .logo-cell img { width: 150px; }

        .party-box {
            border: 1px solid #7f7f9c;
            padding: 6px 10px;
            margin-bottom: -1px; /* the two boxes share an edge, as on the paper devis */
        }
        .party-box p { line-height: 1.5; }
        .party-box.is-company { background: #f7f7fb; }
        .party-box .party-label {
            color: #7f7f9c;
            text-transform: uppercase;
            font-size: 7.5px;
            letter-spacing: 0.5px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .doc-title {
            text-align: center;
            color: #c00000;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1.5px;
            margin-top: 18px;
        }
        .doc-meta { text-align: center; margin-top: 7px; font-size: 11px; }
        .doc-ref { text-align: center; margin-top: 2px; font-size: 9px; color: #666; }

        /* ---- Line items ---- */
        table.items { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table.items th, table.items td { border: 1px solid #7f7f9c; padding: 6px 8px; }
        table.items th {
            color: #c0504d;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
            background: #f2f0f7;
        }
        table.items td.designation { text-align: left; }
        table.items td { text-align: center; }
        table.items td.amount { text-align: right; }
        table.items tbody tr:nth-child(even) td { background: #fafafa; }
        .empty-row td { height: 18px; }

        /* ---- Legend (left) and totals (right) ---- */
        table.summary { width: 100%; margin-top: 18px; border-collapse: collapse; }
        table.summary > tr > td, table.summary td.pane { vertical-align: top; border: none; padding: 0; }
        .pane-left { width: 55%; padding-right: 16px !important; }

        table.boxed { border-collapse: collapse; width: 100%; }
        table.boxed td { border: 1px solid #7f7f9c; padding: 5px 10px; }
        table.legend td.value { text-align: right; white-space: nowrap; }
        table.totals td.label { font-weight: normal; }
        table.totals td.value { text-align: right; white-space: nowrap; }
        table.totals tr.net td {
            font-weight: bold;
            background: #f2f0f7;
            font-size: 11px;
        }

        /* ---- Product information ---- */
        .product-info {
            margin-top: 22px;
            padding-top: 12px;
            border-top: 1px solid #ddd;
        }
        .product-info h2 {
            color: #c0504d;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .product-info p { line-height: 1.6; }

        .currency-note { margin-top: 12px; font-size: 8px; color: #666; }
        .validity { margin-top: 8px; font-size: 9px; color: #7a4b00; }

        /* ---- Repeating footer, pinned into the reserved bottom margin ---- */
        .page-footer {
            position: fixed;
            left: 0; right: 0; bottom: -18mm;
            text-align: center;
            font-size: 8px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 7px;
        }
        /* counter(pages) (the page TOTAL) renders as a literal 0 in this
           dompdf version — only counter(page), the current page, is
           reliable — so the footer shows "Page N", not "N / total". */
        .page-footer .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="page-footer">
        {{ $company['name'] }} &nbsp;•&nbsp; {{ $company['phone'] }} &nbsp;•&nbsp; {{ $company['email'] }}
        &nbsp;•&nbsp; Page <span class="page-number"></span>
    </div>

    <table class="masthead">
        <tr>
            <td class="logo-cell">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $company['name'] }}">
                @else
                    <strong style="font-size: 15px;">{{ $company['name'] }}</strong>
                @endif
            </td>
            <td>
                <div class="party-box is-company">
                    <p class="party-label">Prestataire</p>
                    <p>Ste: {{ $company['name'] }}</p>
                    <p>adresse: {{ $company['address'] }}</p>
                    @if($company['tax_id'])
                        <p>MF: {{ $company['tax_id'] }}</p>
                    @endif
                    <p>Tel : {{ $company['phone'] }}</p>
                </div>
                <div class="party-box">
                    <p class="party-label">Client</p>
                    <p>Clien : {{ $client['name'] }}</p>
                    <p>adresse: {{ $client['address'] ?? '—' }}</p>
                    @if($client['phone'])
                        <p>Tel : {{ $client['phone'] }}</p>
                    @endif
                </div>

                <div class="doc-title">DEVIS</div>
                <div class="doc-meta">DATE: {{ $document->date() }}</div>
                @if($document->reference())
                    <div class="doc-ref">N° {{ $document->reference() }}</div>
                @endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                @foreach($document->columns() as $index => $column)
                    <th @class(['designation' => $index === 0]) @if($index === 0) style="width: 34%; text-align: left;" @endif>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td class="designation">{{ $row['designation'] }}</td>
                    @if($hasShutters)
                        <td>{{ DevisPricing::formatDimension($row['height']) }}</td>
                        <td>{{ DevisPricing::formatDimension($row['width']) }}</td>
                        <td>{{ rtrim(rtrim(number_format($row['quantity'], 2, '.', ''), '0'), '.') }}</td>
                        <td class="amount">{{ $row['shutter_price'] > 0 ? DevisPricing::format($row['shutter_price']) : '' }}</td>
                        <td class="amount">{{ DevisPricing::format($row['unit_price']) }}</td>
                    @else
                        <td>{{ DevisPricing::formatDimension($row['width']) }}</td>
                        <td>{{ DevisPricing::formatDimension($row['height']) }}</td>
                        <td>{{ rtrim(rtrim(number_format($row['quantity'], 2, '.', ''), '0'), '.') }}</td>
                        <td class="amount">{{ DevisPricing::format($row['unit_price']) }}</td>
                    @endif
                    <td class="amount">{{ DevisPricing::format($row['total']) }}</td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="{{ count($document->columns()) }}" style="text-align: center; color: #999;">Aucune ligne</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td class="pane pane-left">
                @if($legend !== [])
                    <table class="boxed legend">
                        @foreach($legend as $line)
                            <tr>
                                <td>{{ $line['label'] }}</td>
                                <td class="value">{{ rtrim(rtrim(DevisPricing::format($line['price']), '0'), '.') }} dt</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </td>
            <td class="pane">
                <table class="boxed totals">
                    <tr>
                        <td class="label">Total</td>
                        <td class="value">{{ DevisPricing::format($totals['subtotal']) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Remise</td>
                        <td class="value">{{ DevisPricing::format($totals['discount']) }}</td>
                    </tr>
                    @if($totals['show_tax'])
                        <tr>
                            <td class="label">TVA ({{ rtrim(rtrim(number_format($totals['tax_rate'], 2, '.', ''), '0'), '.') }}%)</td>
                            <td class="value">{{ DevisPricing::format($totals['tax']) }}</td>
                        </tr>
                    @endif
                    <tr class="net">
                        <td class="label">Net a payer</td>
                        <td class="value">{{ DevisPricing::format($totals['total']) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if($notes !== [])
        <div class="product-info">
            <h2>Information sur produit</h2>
            @foreach($notes as $note)
                <p>* {{ $note }}</p>
            @endforeach
        </div>
    @endif

    @if($quote->valid_until)
        <p class="validity">Ce devis est valable jusqu'au {{ $quote->valid_until->format('d/m/Y') }}.</p>
    @endif

    <p class="currency-note">Montants exprimés en dinars tunisiens (TND){{ $totals['show_tax'] ? '' : ' — hors TVA' }}.</p>
</body>
</html>

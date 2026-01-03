<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis {{ $quote->quote_number ?? 'N/A' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            background: #fff;
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 20px;
        }
        .company-info h1 {
            font-size: 28px;
            color: #1e3a8a;
            margin-bottom: 5px;
        }
        .company-info p {
            color: #666;
            font-size: 13px;
        }
        .document-info {
            text-align: right;
        }
        .document-info h2 {
            font-size: 24px;
            color: #1e3a8a;
            margin-bottom: 10px;
        }
        .document-info p {
            font-size: 13px;
            color: #666;
        }
        .document-info .number {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .client-section {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .client-section h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .client-section .name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .client-section p {
            color: #666;
            font-size: 13px;
        }
        .project-info {
            background: #eff6ff;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #1e3a8a;
        }
        .project-info h3 {
            font-size: 14px;
            color: #1e3a8a;
            margin-bottom: 5px;
        }
        .project-info p {
            color: #333;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background: #1e3a8a;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        th:first-child {
            border-radius: 8px 0 0 0;
        }
        th:last-child {
            border-radius: 0 8px 0 0;
            text-align: right;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            width: 300px;
            margin-left: auto;
            margin-bottom: 40px;
        }
        .totals table {
            margin-bottom: 0;
        }
        .totals td {
            padding: 8px 15px;
            border: none;
        }
        .totals tr:last-child {
            background: #1e3a8a;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        .totals tr:last-child td {
            padding: 12px 15px;
            border-radius: 0 0 8px 8px;
        }
        .validity {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .validity p {
            color: #92400e;
            font-size: 13px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-box p {
            font-size: 12px;
            color: #666;
            margin-bottom: 60px;
        }
        .signature-box .line {
            border-top: 1px solid #333;
            padding-top: 10px;
            font-size: 12px;
        }
        @media print {
            body {
                padding: 20px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h1>{{ $company['name'] }}</h1>
            <p>{{ $company['address'] }}</p>
            <p>📞 {{ $company['phone'] }}</p>
            <p>✉️ {{ $company['email'] }}</p>
        </div>
        <div class="document-info">
            <h2>DEVIS</h2>
            <p class="number">{{ $quote->quote_number ?? 'BROUILLON' }}</p>
            <p>Date: {{ $quote->created_at->format('d/m/Y') }}</p>
            @if($quote->valid_until)
            <p>Validité: {{ $quote->valid_until->format('d/m/Y') }}</p>
            @endif
        </div>
    </div>

    <div class="client-section">
        <h3>Client</h3>
        <p class="name">{{ $quote->name }}</p>
        <p>📧 {{ $quote->email }}</p>
        <p>📞 {{ $quote->phone }}</p>
        @if($quote->city || $quote->country)
        <p>📍 {{ $quote->city }}{{ $quote->city && $quote->country ? ', ' : '' }}{{ $quote->country }}</p>
        @endif
    </div>

    @if($quote->description)
    <div class="project-info">
        <h3>Description du projet</h3>
        <p>{{ $quote->description }}</p>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Description</th>
                <th class="text-center">Unité</th>
                <th class="text-center">Quantité</th>
                <th class="text-right">Prix unit.</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quote->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-center">{{ $item->unit }}</td>
                <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }} TND</td>
                <td class="text-right">{{ number_format($item->total, 2) }} TND</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="color: #999; padding: 30px;">Aucun article</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Sous-total HT</td>
                <td class="text-right">{{ number_format($quote->subtotal ?? 0, 2) }} TND</td>
            </tr>
            @if($quote->discount > 0)
            <tr>
                <td>Remise</td>
                <td class="text-right">-{{ number_format($quote->discount, 2) }} TND</td>
            </tr>
            @endif
            <tr>
                <td>TVA ({{ $quote->tax_rate ?? 19 }}%)</td>
                <td class="text-right">{{ number_format($quote->tax_amount ?? 0, 2) }} TND</td>
            </tr>
            <tr>
                <td>Total TTC</td>
                <td class="text-right">{{ number_format($quote->total ?? 0, 2) }} TND</td>
            </tr>
        </table>
    </div>

    @if($quote->valid_until)
    <div class="validity">
        <p>⚠️ Ce devis est valable jusqu'au <strong>{{ $quote->valid_until->format('d/m/Y') }}</strong>. Passé ce délai, les prix peuvent être révisés.</p>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p>Pour l'entreprise</p>
            <div class="line">{{ $company['name'] }}</div>
        </div>
        <div class="signature-box">
            <p>Bon pour accord (signature client)</p>
            <div class="line">{{ $quote->name }}</div>
        </div>
    </div>

    <div class="footer">
        <p>{{ $company['name'] }} - {{ $company['address'] }} - {{ $company['phone'] }} - {{ $company['email'] }}</p>
    </div>
</body>
</html>

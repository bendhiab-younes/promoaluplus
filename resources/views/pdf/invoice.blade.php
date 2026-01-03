<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $invoice->invoice_number }}</title>
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
            border-bottom: 3px solid #059669;
            padding-bottom: 20px;
        }
        .company-info h1 {
            font-size: 28px;
            color: #059669;
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
            color: #059669;
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
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .status-draft { background: #e5e7eb; color: #374151; }
        .status-sent { background: #dbeafe; color: #1d4ed8; }
        .status-paid { background: #d1fae5; color: #059669; }
        .status-overdue { background: #fee2e2; color: #dc2626; }
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background: #059669;
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
            background: #059669;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        .totals tr:last-child td {
            padding: 12px 15px;
            border-radius: 0 0 8px 8px;
        }
        .payment-info {
            background: #f0fdf4;
            border: 1px solid #059669;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .payment-info h3 {
            font-size: 14px;
            color: #059669;
            margin-bottom: 10px;
        }
        .payment-info p {
            color: #333;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .due-date-warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .due-date-warning p {
            color: #92400e;
            font-size: 13px;
        }
        .terms {
            background: #f8fafc;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .terms h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 10px;
        }
        .terms p {
            color: #666;
            font-size: 12px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
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
            <h2>FACTURE</h2>
            <p class="number">{{ $invoice->invoice_number }}</p>
            <p>Date: {{ $invoice->issue_date->format('d/m/Y') }}</p>
            @if($invoice->due_date)
            <p>Échéance: {{ $invoice->due_date->format('d/m/Y') }}</p>
            @endif
            <span class="status-badge status-{{ $invoice->status }}">
                @switch($invoice->status)
                    @case('draft') Brouillon @break
                    @case('sent') Envoyée @break
                    @case('paid') Payée @break
                    @case('overdue') En retard @break
                    @default {{ $invoice->status }}
                @endswitch
            </span>
        </div>
    </div>

    <div class="client-section">
        <h3>Facturé à</h3>
        <p class="name">{{ $invoice->client_name }}</p>
        @if($invoice->client_email)
        <p>📧 {{ $invoice->client_email }}</p>
        @endif
        @if($invoice->client_phone)
        <p>📞 {{ $invoice->client_phone }}</p>
        @endif
        @if($invoice->client_address)
        <p>📍 {{ $invoice->client_address }}</p>
        @endif
        @if($invoice->client_tax_id)
        <p>🏢 MF: {{ $invoice->client_tax_id }}</p>
        @endif
    </div>

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
            @forelse($invoice->items as $item)
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
                <td class="text-right">{{ number_format($invoice->subtotal, 2) }} TND</td>
            </tr>
            @if($invoice->discount > 0)
            <tr>
                <td>Remise</td>
                <td class="text-right">-{{ number_format($invoice->discount, 2) }} TND</td>
            </tr>
            @endif
            <tr>
                <td>TVA ({{ $invoice->tax_rate }}%)</td>
                <td class="text-right">{{ number_format($invoice->tax_amount, 2) }} TND</td>
            </tr>
            <tr>
                <td>Total TTC</td>
                <td class="text-right">{{ number_format($invoice->total, 2) }} TND</td>
            </tr>
        </table>
    </div>

    @if($invoice->due_date && $invoice->status !== 'paid')
    <div class="due-date-warning">
        <p>⚠️ Cette facture est à régler avant le <strong>{{ $invoice->due_date->format('d/m/Y') }}</strong>.</p>
    </div>
    @endif

    <div class="payment-info">
        <h3>💳 Informations de paiement</h3>
        <p><strong>Banque:</strong> Banque Nationale</p>
        <p><strong>IBAN:</strong> TN59 XXXX XXXX XXXX XXXX XXXX</p>
        <p><strong>BIC:</strong> BNTNTNTT</p>
        <p><strong>Référence:</strong> {{ $invoice->invoice_number }}</p>
    </div>

    @if($invoice->terms)
    <div class="terms">
        <h3>Conditions</h3>
        <p>{{ $invoice->terms }}</p>
    </div>
    @endif

    @if($invoice->notes)
    <div class="terms">
        <h3>Notes</h3>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>{{ $company['name'] }} - {{ $company['address'] }} - {{ $company['phone'] }} - {{ $company['email'] }}</p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e3a8a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .footer { background: #1f2937; color: #9ca3af; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .info-table td:first-child { font-weight: bold; width: 40%; background: #f3f4f6; }
        .urgent { background: #fef2f2; border: 1px solid #fecaca; padding: 10px; border-radius: 6px; color: #dc2626; }
        .description { background: white; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb; margin-top: 20px; }
        h1 { margin: 0; font-size: 20px; }
        .badge { display: inline-block; background: #f97316; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Nouvelle Demande de Devis</h1>
        </div>
        
        <div class="content">
            <p>Une nouvelle demande de devis a été reçue.</p>
            
            <table class="info-table">
                <tr>
                    <td>Nom</td>
                    <td>{{ $quote->name }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><a href="mailto:{{ $quote->email }}">{{ $quote->email }}</a></td>
                </tr>
                <tr>
                    <td>Téléphone</td>
                    <td><a href="tel:{{ $quote->phone }}">{{ $quote->phone }}</a></td>
                </tr>
                @if($quote->country)
                <tr>
                    <td>Pays de résidence</td>
                    <td>{{ $quote->country }}</td>
                </tr>
                @endif
                @if($quote->city)
                <tr>
                    <td>Ville du projet</td>
                    <td>{{ $quote->city }}</td>
                </tr>
                @endif
                <tr>
                    <td>Type de projet</td>
                    <td><span class="badge">{{ ucfirst($quote->project_type) }}</span></td>
                </tr>
                @if($quote->budget_range)
                <tr>
                    <td>Budget estimé</td>
                    <td>{{ $quote->budget_range }}</td>
                </tr>
                @endif
                @if($quote->timeline)
                <tr>
                    <td>Délai souhaité</td>
                    <td>{{ $quote->timeline }}</td>
                </tr>
                @endif
                <tr>
                    <td>Date de demande</td>
                    <td>{{ $quote->created_at->format('d/m/Y à H:i') }}</td>
                </tr>
            </table>
            
            <div class="description">
                <strong>Description du projet :</strong>
                <p>{{ $quote->description }}</p>
            </div>
            
            @if($quote->timeline === 'urgent')
            <div class="urgent">
                ⚠️ <strong>URGENT</strong> - Le client souhaite une réponse rapide (délai < 1 mois)
            </div>
            @endif
        </div>
        
        <div class="footer">
            <p>Répondez à cette demande depuis le panneau d'administration</p>
        </div>
    </div>
</body>
</html>

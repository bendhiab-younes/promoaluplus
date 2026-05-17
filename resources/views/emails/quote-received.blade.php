<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .footer { background: #1f2937; color: #9ca3af; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
        .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f97316; }
        h1 { margin: 0; }
        .btn { display: inline-block; background: #f97316; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ \\App\\Models\\SiteSetting::get('company_name', 'AluminiumCraft Tunisie') }}</h1>
            <p>Menuiserie Aluminium de Qualité Européenne</p>
        </div>
        
        <div class="content">
            <h2>Bonjour {{ $quote->name }},</h2>
            
            <p>Nous avons bien reçu votre demande de devis et nous vous en remercions !</p>
            
            <div class="info-box">
                <h3 style="margin-top: 0;">Récapitulatif de votre demande :</h3>
                <p><strong>Type de projet :</strong> {{ ucfirst($quote->project_type) }}</p>
                @if($quote->city)
                <p><strong>Ville du projet :</strong> {{ $quote->city }}</p>
                @endif
                
                @if($quote->timeline)
                <p><strong>Délai souhaité :</strong> {{ $quote->timeline }}</p>
                @endif
            </div>
            
            <p>Notre équipe va étudier votre demande avec attention et vous contactera sous <strong>48 heures</strong> avec un devis détaillé.</p>
            
            <p>En attendant, n'hésitez pas à nous contacter par :</p>
            <ul>
                <li>📞 Téléphone : {{ \App\Models\SiteSetting::get('contact_phone', '+216 12 345 678') }}</li>
                <li>💬 WhatsApp : {{ \App\Models\SiteSetting::get('contact_whatsapp', \App\Models\SiteSetting::get('contact_phone', '+216 12 345 678')) }}</li>
                <li>📧 Email : {{ \\App\\Models\\SiteSetting::get('contact_email', 'contact@aluminiumcraft.tn') }}</li>
            </ul>
            
            <p>Cordialement,<br>
            <strong>L'équipe {{ \\App\\Models\\SiteSetting::get('company_name','AluminiumCraft Tunisie') }}</strong></p>
        </div>
        
        <div class="footer">
            <p>{{ \\App\\Models\\SiteSetting::get('company_name','AluminiumCraft Tunisie') }} - {{ \\App\\Models\\SiteSetting::get('contact_address','Zone Industrielle, Sousse') }}</p>
            <p>© {{ date('Y') }} AluminiumCraft. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>

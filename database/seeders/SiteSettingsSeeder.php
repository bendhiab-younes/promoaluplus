<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Company settings
        $settings = [
            // Entreprise
            ['key' => 'company_name', 'value' => 'Promo Alu Plus', 'group' => 'general'],
            ['key' => 'company_slogan', 'value' => 'Menuiserie Aluminium & Inox de Qualité', 'group' => 'general'],
            ['key' => 'company_description', 'value' => 'Spécialiste en menuiserie aluminium et inox en Tunisie. Portes, fenêtres, rideaux, garde-corps, pergolas, cuisines et volets électriques.', 'group' => 'general'],
            
            // Contact
            ['key' => 'contact_phone', 'value' => '+216 12 345 678', 'group' => 'contact'],
            ['key' => 'contact_whatsapp', 'value' => '+216 12 345 678', 'group' => 'contact'],
            ['key' => 'contact_email', 'value' => 'contact@promoaluplus.tn', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => 'Tunis, Tunisie', 'group' => 'contact'],
            
            // Horaires
            ['key' => 'hours_weekdays', 'value' => '8h00 - 18h00', 'group' => 'hours'],
            ['key' => 'hours_saturday', 'value' => '9h00 - 13h00', 'group' => 'hours'],
            ['key' => 'hours_sunday', 'value' => 'Fermé', 'group' => 'hours'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // FAQs
        $faqs = [
            [
                'question' => [
                    'fr' => 'Comment obtenir un devis ?',
                    'en' => 'How to get a quote?',
                    'ar' => 'كيف أحصل على عرض سعر؟',
                ],
                'answer' => [
                    'fr' => 'Remplissez simplement notre formulaire en ligne ou contactez-nous par téléphone/WhatsApp. Nous vous répondrons sous 48h avec un devis détaillé.',
                    'en' => 'Simply fill out our online form or contact us by phone/WhatsApp. We will respond within 48h with a detailed quote.',
                    'ar' => 'املأ نموذجنا عبر الإنترنت أو اتصل بنا عبر الهاتف/واتساب. سنرد عليك خلال 48 ساعة بعرض سعر مفصل.',
                ],
                'sort_order' => 1,
            ],
            [
                'question' => [
                    'fr' => 'Quels types de produits proposez-vous ?',
                    'en' => 'What types of products do you offer?',
                    'ar' => 'ما أنواع المنتجات التي تقدمونها؟',
                ],
                'answer' => [
                    'fr' => 'Nous proposons : portes, fenêtres, rideaux métalliques, garde-corps, articles en inox, pergolas, cuisines en aluminium, abris et volets électriques.',
                    'en' => 'We offer: doors, windows, metal curtains, railings, stainless steel items, pergolas, aluminum kitchens, shelters and electric shutters.',
                    'ar' => 'نقدم: أبواب، نوافذ، ستائر معدنية، حواجز، منتجات ستانليس، برجولات، مطابخ ألمنيوم، مظلات وشتر كهربائي.',
                ],
                'sort_order' => 2,
            ],
            [
                'question' => [
                    'fr' => 'Quelle est la garantie sur vos produits ?',
                    'en' => 'What is the warranty on your products?',
                    'ar' => 'ما هي مدة الضمان على منتجاتكم؟',
                ],
                'answer' => [
                    'fr' => 'Tous nos produits sont garantis 10 ans. Nous utilisons uniquement des matériaux de qualité supérieure.',
                    'en' => 'All our products come with a 10-year warranty. We only use premium quality materials.',
                    'ar' => 'جميع منتجاتنا مضمونة لمدة 10 سنوات. نستخدم فقط مواد عالية الجودة.',
                ],
                'sort_order' => 3,
            ],
            [
                'question' => [
                    'fr' => 'Travaillez-vous avec les expatriés ?',
                    'en' => 'Do you work with expatriates?',
                    'ar' => 'هل تعملون مع المغتربين؟',
                ],
                'answer' => [
                    'fr' => 'Oui ! Nous sommes spécialisés dans l\'accompagnement des expatriés tunisiens. Suivi à distance, visioconférences, et communication en français.',
                    'en' => 'Yes! We specialize in supporting Tunisian expatriates. Remote follow-up, video conferences, and communication in French.',
                    'ar' => 'نعم! نحن متخصصون في مرافقة المغتربين التونسيين. متابعة عن بعد، مؤتمرات فيديو، وتواصل بالفرنسية.',
                ],
                'sort_order' => 4,
            ],
            [
                'question' => [
                    'fr' => 'Quels sont vos délais de livraison ?',
                    'en' => 'What are your delivery times?',
                    'ar' => 'ما هي مدة التسليم؟',
                ],
                'answer' => [
                    'fr' => 'Les délais varient selon le projet. En général, comptez 2 à 4 semaines pour la fabrication et l\'installation. Nous respectons scrupuleusement nos engagements.',
                    'en' => 'Delivery times vary by project. Generally, allow 2-4 weeks for manufacturing and installation. We strictly honor our commitments.',
                    'ar' => 'تختلف المدة حسب المشروع. عادة، احسب 2-4 أسابيع للتصنيع والتركيب. نحترم التزاماتنا بدقة.',
                ],
                'sort_order' => 5,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question->fr' => $faq['question']['fr']],
                array_merge($faq, ['is_active' => true])
            );
        }
    }
}

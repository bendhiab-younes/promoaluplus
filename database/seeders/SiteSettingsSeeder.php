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
            
            // Hero - FR
            ['key' => 'hero_badge_fr', 'value' => 'Qualité Premium Garantie', 'group' => 'hero'],
            ['key' => 'hero_title_fr', 'value' => 'Menuiserie Aluminium', 'group' => 'hero'],
            ['key' => 'hero_subtitle_fr', 'value' => 'Sur Mesure', 'group' => 'hero'],
            ['key' => 'hero_description_fr', 'value' => 'Donnez vie à vos projets avec nos portes, fenêtres, pergolas, cuisines et volets électriques. Un savoir-faire artisanal au service de votre confort.', 'group' => 'hero'],
            // Hero - EN
            ['key' => 'hero_badge_en', 'value' => 'Premium Quality Guaranteed', 'group' => 'hero'],
            ['key' => 'hero_title_en', 'value' => 'Aluminum Joinery', 'group' => 'hero'],
            ['key' => 'hero_subtitle_en', 'value' => 'Custom Made', 'group' => 'hero'],
            ['key' => 'hero_description_en', 'value' => 'Bring your projects to life with our doors, windows, pergolas, kitchens and electric shutters. Craftsmanship at the service of your comfort.', 'group' => 'hero'],
            // Hero - AR
            ['key' => 'hero_badge_ar', 'value' => 'جودة ممتازة مضمونة', 'group' => 'hero'],
            ['key' => 'hero_title_ar', 'value' => 'نجارة ألمنيوم', 'group' => 'hero'],
            ['key' => 'hero_subtitle_ar', 'value' => 'حسب الطلب', 'group' => 'hero'],
            ['key' => 'hero_description_ar', 'value' => 'حقق أحلامك مع أبوابنا، نوافذنا، برجولاتنا، مطابخنا وشترنا الكهربائي. حرفية في خدمة راحتك.', 'group' => 'hero'],
            
            // Stats
            ['key' => 'stats_projects', 'value' => '500', 'group' => 'stats'],
            ['key' => 'stats_years', 'value' => '15', 'group' => 'stats'],
            ['key' => 'stats_satisfaction', 'value' => '98', 'group' => 'stats'],
            ['key' => 'stats_team', 'value' => '12', 'group' => 'stats'],
            
            // CTA - FR
            ['key' => 'cta_title_fr', 'value' => 'Prêt à Démarrer Votre Projet?', 'group' => 'cta'],
            ['key' => 'cta_description_fr', 'value' => 'Obtenez un devis gratuit et détaillé sous 48h. Notre équipe est à votre écoute.', 'group' => 'cta'],
            // CTA - EN
            ['key' => 'cta_title_en', 'value' => 'Ready to Start Your Project?', 'group' => 'cta'],
            ['key' => 'cta_description_en', 'value' => 'Get a free detailed quote within 48h. Our team is at your service.', 'group' => 'cta'],
            // CTA - AR
            ['key' => 'cta_title_ar', 'value' => 'مستعد لبدء مشروعك؟', 'group' => 'cta'],
            ['key' => 'cta_description_ar', 'value' => 'احصل على عرض سعر مجاني ومفصل خلال 48 ساعة. فريقنا في خدمتك.', 'group' => 'cta'],
            
            // About - FR
            ['key' => 'about_story_fr', 'value' => 'Promo Alu Plus est votre partenaire de confiance pour tous vos projets en aluminium en Tunisie. Nous comprenons les défis uniques auxquels font face les Tunisiens vivant à l\'étranger lorsqu\'ils construisent ou rénovent leur maison en Tunisie.', 'group' => 'about'],
            ['key' => 'about_mission_fr', 'value' => 'Offrir aux expatriés tunisiens une expérience de construction sereine grâce à des produits de qualité européenne et un service client irréprochable.', 'group' => 'about'],
            ['key' => 'about_values_fr', 'value' => 'Qualité, transparence, ponctualité et engagement client sont les piliers de notre entreprise.', 'group' => 'about'],
            // About - EN
            ['key' => 'about_story_en', 'value' => 'Promo Alu Plus is your trusted partner for all your aluminum projects in Tunisia. We understand the unique challenges faced by Tunisians living abroad when building or renovating their home in Tunisia.', 'group' => 'about'],
            ['key' => 'about_mission_en', 'value' => 'Offer Tunisian expatriates a peaceful construction experience through European quality products and impeccable customer service.', 'group' => 'about'],
            ['key' => 'about_values_en', 'value' => 'Quality, transparency, punctuality and customer commitment are the pillars of our company.', 'group' => 'about'],
            // About - AR
            ['key' => 'about_story_ar', 'value' => 'Promo Alu Plus شريكك الموثوق لجميع مشاريعك في الألمنيوم في تونس. نحن نفهم التحديات الفريدة التي يواجهها التونسيون المقيمون في الخارج عند بناء أو تجديد منازلهم في تونس.', 'group' => 'about'],
            ['key' => 'about_mission_ar', 'value' => 'تقديم تجربة بناء هادئة للتونسيين المغتربين من خلال منتجات بجودة أوروبية وخدمة عملاء لا تشوبها شائبة.', 'group' => 'about'],
            ['key' => 'about_values_ar', 'value' => 'الجودة والشفافية والالتزام بالمواعيد والتزامنا تجاه العميل هي ركائز شركتنا.', 'group' => 'about'],
            
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

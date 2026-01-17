<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => [
                    'fr' => 'Quels types de produits en aluminium proposez-vous ?',
                    'en' => 'What types of aluminum products do you offer?',
                    'ar' => 'ما هي أنواع المنتجات الألومنيوم التي تقدمونها؟',
                ],
                'answer' => [
                    'fr' => 'Nous proposons une large gamme de produits en aluminium, notamment des fenêtres, portes, vérandas, volets, portails, et cloisons. Tous nos produits sont conçus pour allier esthétisme, robustesse et performance énergétique.',
                    'en' => 'We offer a wide range of aluminum products, including windows, doors, verandas, shutters, gates, and partitions. All our products are designed to combine aesthetics, robustness, and energy performance.',
                    'ar' => 'نقدم مجموعة واسعة من منتجات الألومنيوم، بما في ذلك النوافذ والأبواب والشرفات والمصاريع والبوابات والفواصل. جميع منتجاتنا مصممة للجمع بين الجماليات والمتانة والأداء الطاقي.',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'question' => [
                    'fr' => 'Quels sont les avantages de la menuiserie aluminium ?',
                    'en' => 'What are the advantages of aluminum joinery?',
                    'ar' => 'ما هي مزايا النجارة الألومنيوم؟',
                ],
                'answer' => [
                    'fr' => 'L\'aluminium est un matériau léger, résistant à la corrosion, durable et recyclable. Il offre une excellente isolation thermique et phonique, ce qui permet de réduire les dépenses énergétiques. De plus, il permet des designs modernes avec des profils fins.',
                    'en' => 'Aluminum is a lightweight, corrosion-resistant, durable, and recyclable material. It offers excellent thermal and acoustic insulation, which helps reduce energy costs. Additionally, it allows for modern designs with slim profiles.',
                    'ar' => 'الألومنيوم مادة خفيفة ومقاومة للتآكل ومتينة وقابلة لإعادة التدوير. يوفر عزلًا حراريًا وصوتيًا ممتازًا، مما يساعد على تقليل تكاليف الطاقة. بالإضافة إلى ذلك، يسمح بتصميمات عصرية بملفات رفيعة.',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'question' => [
                    'fr' => 'Est-il possible de personnaliser les menuiseries aluminium ?',
                    'en' => 'Is it possible to customize aluminum joinery?',
                    'ar' => 'هل من الممكن تخصيص النجارة الألومنيوم؟',
                ],
                'answer' => [
                    'fr' => 'Oui, nos menuiseries aluminium peuvent être entièrement personnalisées selon vos besoins : dimensions, coloris, finitions, type d\'ouverture, accessoires, et options de sécurité.',
                    'en' => 'Yes, our aluminum joinery can be fully customized according to your needs: dimensions, colors, finishes, opening types, accessories, and security options.',
                    'ar' => 'نعم، يمكن تخصيص نجارتنا الألومنيوم بالكامل وفقًا لاحتياجاتك: الأبعاد والألوان والتشطيبات وأنواع الفتح والإكسسوارات وخيارات الأمان.',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'question' => [
                    'fr' => 'Faites-vous la pose des menuiseries aluminium ?',
                    'en' => 'Do you install aluminum joinery?',
                    'ar' => 'هل تقومون بتركيب النجارة الألومنيوم؟',
                ],
                'answer' => [
                    'fr' => 'Oui, nous proposons un service complet comprenant la fabrication, la livraison et l\'installation par nos équipes qualifiées, assurant une pose conforme et durable.',
                    'en' => 'Yes, we offer a complete service including manufacturing, delivery, and installation by our qualified teams, ensuring proper and durable installation.',
                    'ar' => 'نعم، نقدم خدمة كاملة تشمل التصنيع والتسليم والتركيب من قبل فرقنا المؤهلة، مما يضمن تركيبًا صحيحًا ومتينًا.',
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'question' => [
                    'fr' => 'Quels sont les délais de fabrication et de pose ?',
                    'en' => 'What are the manufacturing and installation timelines?',
                    'ar' => 'ما هي مواعيد التصنيع والتركيب؟',
                ],
                'answer' => [
                    'fr' => 'Les délais varient selon la complexité du projet, mais en général, la fabrication prend environ 3 à 6 semaines. La pose s\'organise ensuite dans un délai convenu avec le client.',
                    'en' => 'Timelines vary depending on project complexity, but generally, manufacturing takes about 3 to 6 weeks. Installation is then scheduled within a timeframe agreed upon with the client.',
                    'ar' => 'تختلف المواعيد حسب تعقيد المشروع، ولكن بشكل عام، يستغرق التصنيع حوالي 3 إلى 6 أسابيع. ثم يتم تنظيم التركيب في إطار زمني متفق عليه مع العميل.',
                ],
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'question' => [
                    'fr' => 'Proposez-vous des solutions adaptées aux bâtiments anciens ou classés ?',
                    'en' => 'Do you offer solutions suitable for old or listed buildings?',
                    'ar' => 'هل تقدمون حلولاً مناسبة للمباني القديمة أو المصنفة؟',
                ],
                'answer' => [
                    'fr' => 'Oui, nous avons des solutions spécifiques respectant les contraintes architecturales et réglementaires des bâtiments anciens ou protégés.',
                    'en' => 'Yes, we have specific solutions that respect the architectural and regulatory constraints of old or protected buildings.',
                    'ar' => 'نعم، لدينا حلول محددة تحترم القيود المعمارية والتنظيمية للمباني القديمة أو المحمية.',
                ],
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'question' => [
                    'fr' => 'Vos menuiseries aluminium sont-elles garanties ?',
                    'en' => 'Are your aluminum joinery products guaranteed?',
                    'ar' => 'هل منتجات النجارة الألومنيوم مضمونة؟',
                ],
                'answer' => [
                    'fr' => 'Oui, toutes nos menuiseries bénéficient d\'une garantie fabricant. La durée varie entre 5 et 10 ans selon le produit et l\'installation.',
                    'en' => 'Yes, all our joinery products come with a manufacturer\'s warranty. The duration varies between 5 and 10 years depending on the product and installation.',
                    'ar' => 'نعم، جميع منتجاتنا تأتي مع ضمان الشركة المصنعة. تتراوح المدة بين 5 و 10 سنوات حسب المنتج والتركيب.',
                ],
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'question' => [
                    'fr' => 'Comment entretenir les menuiseries en aluminium ?',
                    'en' => 'How to maintain aluminum joinery?',
                    'ar' => 'كيف يتم صيانة النجارة الألومنيوم؟',
                ],
                'answer' => [
                    'fr' => 'L\'entretien est simple : un nettoyage régulier avec de l\'eau savonneuse suffit pour conserver leur éclat et assurer leur longévité. Évitez les produits abrasifs.',
                    'en' => 'Maintenance is simple: regular cleaning with soapy water is enough to maintain their shine and ensure longevity. Avoid abrasive products.',
                    'ar' => 'الصيانة بسيطة: التنظيف المنتظم بالماء والصابون يكفي للحفاظ على لمعانها وضمان طول عمرها. تجنب المنتجات الكاشطة.',
                ],
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'question' => [
                    'fr' => 'Proposez-vous des menuiseries aluminium à haute performance énergétique ?',
                    'en' => 'Do you offer high energy performance aluminum joinery?',
                    'ar' => 'هل تقدمون نجارة ألومنيوم بأداء طاقي عالي؟',
                ],
                'answer' => [
                    'fr' => 'Oui, nous proposons des menuiseries équipées de rupture de pont thermique, double ou triple vitrage, pour optimiser l\'isolation thermique et réduire vos factures d\'énergie.',
                    'en' => 'Yes, we offer joinery equipped with thermal breaks, double or triple glazing, to optimize thermal insulation and reduce your energy bills.',
                    'ar' => 'نعم، نقدم نجارة مجهزة بكسر الجسر الحراري، وزجاج مزدوج أو ثلاثي، لتحسين العزل الحراري وتقليل فواتير الطاقة.',
                ],
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'question' => [
                    'fr' => 'Comment obtenir un devis pour mon projet ?',
                    'en' => 'How to get a quote for my project?',
                    'ar' => 'كيف أحصل على عرض أسعار لمشروعي؟',
                ],
                'answer' => [
                    'fr' => 'Vous pouvez nous contacter via notre formulaire en ligne ou par téléphone. Un conseiller vous accompagnera pour définir vos besoins et établir un devis personnalisé et gratuit.',
                    'en' => 'You can contact us via our online form or by phone. An advisor will assist you in defining your needs and preparing a personalized and free quote.',
                    'ar' => 'يمكنك الاتصال بنا عبر نموذجنا عبر الإنترنت أو عبر الهاتف. سيساعدك مستشار في تحديد احتياجاتك وإعداد عرض أسعار مخصص ومجاني.',
                ],
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['sort_order' => $faq['sort_order']],
                $faq
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'slug' => 'kitchen',
                'title' => [
                    'fr' => 'Cuisines en Aluminium',
                    'en' => 'Aluminum Kitchens',
                    'ar' => 'مطابخ الألومنيوم',
                ],
                'short_description' => [
                    'fr' => 'Des cuisines modernes et durables en aluminium de haute qualité.',
                    'en' => 'Modern and durable kitchens made from high-quality aluminum.',
                    'ar' => 'مطابخ حديثة ومتينة من الألومنيوم عالي الجودة.',
                ],
                'description' => [
                    'fr' => 'Nos cuisines en aluminium allient modernité et durabilité. Conçues sur mesure, elles offrent une résistance exceptionnelle à l\'humidité et à la chaleur, tout en étant faciles à entretenir.',
                    'en' => 'Our aluminum kitchens combine modernity and durability. Custom-designed, they offer exceptional resistance to humidity and heat while being easy to maintain.',
                    'ar' => 'تجمع مطابخنا المصنوعة من الألومنيوم بين الحداثة والمتانة. مصممة حسب الطلب، توفر مقاومة استثنائية للرطوبة والحرارة مع سهولة الصيانة.',
                ],
                'icon' => 'utensils',
                'svg_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="M3 9V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4"/><path d="M12 3v6"/><path d="M8 21v-4"/><path d="M16 21v-4"/></svg>',
                'color' => 'rose',
                'image' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600585152220-90363fe7e115?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1565538810643-b5bdb714032a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600566752355-35792bedcfea?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                ],
                'features' => [
                    ['fr' => 'Robustesse et résistance à l\'humidité', 'en' => 'Robustness and moisture resistance', 'ar' => 'متانة ومقاومة للرطوبة'],
                    ['fr' => 'Design moderne et épuré', 'en' => 'Modern and sleek design', 'ar' => 'تصميم عصري وأنيق'],
                    ['fr' => 'Entretien facile', 'en' => 'Easy maintenance', 'ar' => 'سهولة الصيانة'],
                    ['fr' => 'Personnalisation complète', 'en' => 'Full customization', 'ar' => 'تخصيص كامل'],
                    ['fr' => 'Confort optimal', 'en' => 'Optimal comfort', 'ar' => 'راحة مثالية'],
                ],
                'materials' => [
                    ['fr' => 'Profilés aluminium', 'en' => 'Aluminum profiles', 'ar' => 'ملفات الألومنيوم'],
                    ['fr' => 'Verre trempé', 'en' => 'Tempered glass', 'ar' => 'زجاج مقسى'],
                    ['fr' => 'Quincaillerie inox', 'en' => 'Stainless hardware', 'ar' => 'أدوات من الفولاذ المقاوم للصدأ'],
                    ['fr' => 'Laquage poudre', 'en' => 'Powder coating', 'ar' => 'طلاء بودرة'],
                ],
                'specs' => [
                    ['label' => 'Épaisseur', 'value' => '1.2 - 2.0 mm'],
                    ['label' => 'Finition', 'value' => 'Mat / Brillant'],
                    ['label' => 'Couleurs', 'value' => '200+'],
                ],
                'sort_order' => 1,
            ],
            [
                'slug' => 'doors',
                'title' => [
                    'fr' => 'Portes en Aluminium',
                    'en' => 'Aluminum Doors',
                    'ar' => 'أبواب الألومنيوم',
                ],
                'short_description' => [
                    'fr' => 'Portes d\'entrée et intérieures en aluminium pour une sécurité et un style incomparables.',
                    'en' => 'Entrance and interior aluminum doors for unmatched security and style.',
                    'ar' => 'أبواب مدخل وداخلية من الألومنيوم لأمان وأناقة لا مثيل لهما.',
                ],
                'description' => [
                    'fr' => 'Nos portes en aluminium offrent une combinaison parfaite de sécurité, d\'isolation et d\'esthétique. Disponibles en plusieurs configurations et finitions.',
                    'en' => 'Our aluminum doors offer a perfect combination of security, insulation, and aesthetics. Available in multiple configurations and finishes.',
                    'ar' => 'توفر أبوابنا المصنوعة من الألومنيوم مزيجًا مثاليًا من الأمان والعزل والجمال. متوفرة بتكوينات وتشطيبات متعددة.',
                ],
                'icon' => 'door-open',
                'svg_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
                'color' => 'orange',
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1506629082955-511b1aa562c8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                ],
                'features' => [
                    ['fr' => 'Puissance et résistance', 'en' => 'Power and strength', 'ar' => 'القوة والمتانة'],
                    ['fr' => 'Excellente étanchéité', 'en' => 'Excellent sealing', 'ar' => 'إحكام ممتاز'],
                    ['fr' => 'Style et élégance', 'en' => 'Style and elegance', 'ar' => 'الأناقة والرقي'],
                    ['fr' => 'Design personnalisable', 'en' => 'Customizable design', 'ar' => 'تصميم قابل للتخصيص'],
                ],
                'materials' => [
                    ['fr' => 'Profilés aluminium', 'en' => 'Aluminum profiles', 'ar' => 'ملفات الألومنيوم'],
                    ['fr' => 'Verre trempé', 'en' => 'Tempered glass', 'ar' => 'زجاج مقسى'],
                    ['fr' => 'Quincaillerie inox', 'en' => 'Stainless hardware', 'ar' => 'أدوات من الفولاذ المقاوم للصدأ'],
                    ['fr' => 'Rupture de pont thermique', 'en' => 'Thermal break', 'ar' => 'كسر الجسر الحراري'],
                ],
                'specs' => [
                    ['label' => 'Épaisseur', 'value' => '1.4 - 2.0 mm'],
                    ['label' => 'Options verre', 'value' => '6 - 24 mm'],
                    ['label' => 'Couleurs', 'value' => '200+'],
                ],
                'sort_order' => 2,
            ],
            [
                'slug' => 'windows',
                'title' => [
                    'fr' => 'Fenêtres en Aluminium',
                    'en' => 'Aluminum Windows',
                    'ar' => 'نوافذ الألومنيوم',
                ],
                'short_description' => [
                    'fr' => 'Fenêtres performantes en aluminium pour une isolation optimale et une luminosité maximale.',
                    'en' => 'High-performance aluminum windows for optimal insulation and maximum brightness.',
                    'ar' => 'نوافذ ألومنيوم عالية الأداء لعزل مثالي وإضاءة قصوى.',
                ],
                'description' => [
                    'fr' => 'Nos fenêtres en aluminium garantissent une excellente isolation thermique et acoustique tout en maximisant l\'entrée de lumière naturelle.',
                    'en' => 'Our aluminum windows guarantee excellent thermal and acoustic insulation while maximizing natural light entry.',
                    'ar' => 'تضمن نوافذنا المصنوعة من الألومنيوم عزلًا حراريًا وصوتيًا ممتازًا مع تعظيم دخول الضوء الطبيعي.',
                ],
                'icon' => 'square',
                'svg_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="3" x2="12" y2="21"></line><line x1="3" y1="12" x2="21" y2="12"></line></svg>',
                'color' => 'blue',
                'features' => [
                    ['fr' => 'Sécurité et durabilité', 'en' => 'Security and durability', 'ar' => 'الأمان والمتانة'],
                    ['fr' => 'Performance énergétique', 'en' => 'Energy performance', 'ar' => 'كفاءة الطاقة'],
                    ['fr' => 'Design esthétique', 'en' => 'Aesthetic design', 'ar' => 'تصميم جمالي'],
                ],
                'materials' => [
                    ['fr' => 'Profilés aluminium', 'en' => 'Aluminum profiles', 'ar' => 'ملفات الألومنيوم'],
                    ['fr' => 'Double vitrage', 'en' => 'Double glass', 'ar' => 'زجاج مزدوج'],
                    ['fr' => 'Joints EPDM', 'en' => 'EPDM seals', 'ar' => 'حشوات EPDM'],
                    ['fr' => 'Rupture de pont thermique', 'en' => 'Thermal break', 'ar' => 'كسر الجسر الحراري'],
                ],
                'specs' => [
                    ['label' => 'Épaisseur', 'value' => '1.4 - 1.8 mm'],
                    ['label' => 'Options verre', 'value' => '4+12+4 mm'],
                    ['label' => 'Couleurs', 'value' => '200+'],
                ],
                'sort_order' => 3,
            ],
            [
                'slug' => 'rolling_shutters',
                'title' => [
                    'fr' => 'Volets Roulants',
                    'en' => 'Rolling Shutters',
                    'ar' => 'شتر (ستائر دوارة)',
                ],
                'short_description' => [
                    'fr' => 'Volets roulants motorisés pour une protection et un confort au quotidien.',
                    'en' => 'Motorized rolling shutters for daily protection and comfort.',
                    'ar' => 'ستائر دوارة آلية للحماية والراحة اليومية.',
                ],
                'description' => [
                    'fr' => 'Nos volets roulants en aluminium offrent sécurité, isolation thermique et confort d\'utilisation avec options motorisées.',
                    'en' => 'Our aluminum rolling shutters offer security, thermal insulation and ease of use with motorized options.',
                    'ar' => 'توفر ستائرنا الدوارة المصنوعة من الألومنيوم الأمان والعزل الحراري وسهولة الاستخدام مع خيارات آلية.',
                ],
                'icon' => 'blinds',
                'svg_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="7" x2="21" y2="7"/><line x1="3" y1="11" x2="21" y2="11"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="3" y1="19" x2="21" y2="19"/></svg>',
                'color' => 'purple',
                'features' => [
                    ['fr' => 'Sécurité renforcée', 'en' => 'Enhanced security', 'ar' => 'أمان معزز'],
                    ['fr' => 'Confort thermique', 'en' => 'Thermal comfort', 'ar' => 'راحة حرارية'],
                    ['fr' => 'Protection météo', 'en' => 'Weather protection', 'ar' => 'حماية من الطقس'],
                    ['fr' => 'Design esthétique', 'en' => 'Aesthetic design', 'ar' => 'تصميم جمالي'],
                    ['fr' => 'Tranquillité d\'esprit', 'en' => 'Peace of mind', 'ar' => 'راحة البال'],
                ],
                'materials' => [
                    ['fr' => 'Lames aluminium', 'en' => 'Aluminum slats', 'ar' => 'شرائح الألومنيوم'],
                    ['fr' => 'Isolation mousse', 'en' => 'Foam insulation', 'ar' => 'عزل رغوي'],
                    ['fr' => 'Système moteur', 'en' => 'Motor system', 'ar' => 'نظام المحرك'],
                    ['fr' => 'Rails de guidage', 'en' => 'Guide rails', 'ar' => 'قضبان التوجيه'],
                ],
                'specs' => [
                    ['label' => 'Épaisseur lames', 'value' => '0.5 - 0.8 mm'],
                    ['label' => 'Largeur lames', 'value' => '37 - 55 mm'],
                    ['label' => 'Couleurs', 'value' => '50+'],
                ],
                'sort_order' => 4,
            ],
            [
                'slug' => 'railings',
                'title' => [
                    'fr' => 'Garde-corps',
                    'en' => 'Railings',
                    'ar' => 'الدرابزين',
                ],
                'short_description' => [
                    'fr' => 'Garde-corps et balustrades en aluminium pour sécurité et esthétique.',
                    'en' => 'Aluminum railings and balustrades for safety and aesthetics.',
                    'ar' => 'درابزين وحواجز من الألومنيوم للأمان والجمال.',
                ],
                'description' => [
                    'fr' => 'Nos garde-corps en aluminium et verre combinent sécurité maximale et design contemporain pour balcons, terrasses et escaliers.',
                    'en' => 'Our aluminum and glass railings combine maximum safety and contemporary design for balconies, terraces and stairs.',
                    'ar' => 'تجمع درابزيناتنا المصنوعة من الألومنيوم والزجاج بين الأمان الأقصى والتصميم المعاصر للشرفات والتراسات والسلالم.',
                ],
                'icon' => 'fence',
                'svg_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22V8"/><path d="M5 12H2a10 10 0 0 0 20 0h-3"/><path d="M22 8l-2 2-2-2"/><path d="M6 8l-2 2-2-2"/><path d="M6 22v-4"/><path d="M18 22v-4"/></svg>',
                'color' => 'green',
                'features' => [
                    ['fr' => 'Solidité garantie', 'en' => 'Guaranteed strength', 'ar' => 'متانة مضمونة'],
                    ['fr' => 'Élégance moderne', 'en' => 'Modern elegance', 'ar' => 'أناقة عصرية'],
                    ['fr' => 'Installation facile', 'en' => 'Easy installation', 'ar' => 'تركيب سهل'],
                    ['fr' => 'Sécurité optimale', 'en' => 'Optimal security', 'ar' => 'أمان مثالي'],
                    ['fr' => 'Polyvalence', 'en' => 'Versatility', 'ar' => 'تعدد الاستخدامات'],
                    ['fr' => 'Intégration harmonieuse', 'en' => 'Harmonious integration', 'ar' => 'تكامل متناغم'],
                ],
                'materials' => [
                    ['fr' => 'Profilés aluminium', 'en' => 'Aluminum profiles', 'ar' => 'ملفات الألومنيوم'],
                    ['fr' => 'Verre trempé', 'en' => 'Tempered glass', 'ar' => 'زجاج مقسى'],
                    ['fr' => 'Fixations inox', 'en' => 'Stainless fittings', 'ar' => 'تثبيتات من الفولاذ المقاوم للصدأ'],
                    ['fr' => 'Laquage poudre', 'en' => 'Powder coating', 'ar' => 'طلاء بودرة'],
                ],
                'specs' => [
                    ['label' => 'Épaisseur', 'value' => '1.5 - 2.5 mm'],
                    ['label' => 'Options verre', 'value' => '8 - 12 mm'],
                    ['label' => 'Couleurs', 'value' => '200+'],
                ],
                'sort_order' => 5,
            ],
            [
                'slug' => 'pergola',
                'title' => [
                    'fr' => 'Pergolas',
                    'en' => 'Pergolas',
                    'ar' => 'البرجولات',
                ],
                'short_description' => [
                    'fr' => 'Pergolas bioclimatiques en aluminium pour profiter de votre extérieur toute l\'année.',
                    'en' => 'Bioclimatic aluminum pergolas to enjoy your outdoor space all year round.',
                    'ar' => 'برجولات بيومناخية من الألومنيوم للاستمتاع بمساحتك الخارجية طوال العام.',
                ],
                'description' => [
                    'fr' => 'Nos pergolas en aluminium créent des espaces de vie extérieurs élégants avec contrôle de l\'ombre et de la ventilation.',
                    'en' => 'Our aluminum pergolas create elegant outdoor living spaces with shade and ventilation control.',
                    'ar' => 'تخلق برجولاتنا المصنوعة من الألومنيوم مساحات معيشة خارجية أنيقة مع التحكم في الظل والتهوية.',
                ],
                'icon' => 'tent',
                'svg_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22V6"/><path d="M20 22V6"/><path d="M2 6h20"/><path d="M2 10h20"/><path d="M12 6v16"/></svg>',
                'color' => 'amber',
                'features' => [
                    ['fr' => 'Robustesse et durabilité', 'en' => 'Robustness and durability', 'ar' => 'المتانة والديمومة'],
                    ['fr' => 'Esthétique raffinée', 'en' => 'Refined aesthetics', 'ar' => 'جمالية راقية'],
                    ['fr' => 'Sans entretien', 'en' => 'No maintenance', 'ar' => 'بدون صيانة'],
                    ['fr' => 'Allié de l\'aménagement', 'en' => 'Layout ally', 'ar' => 'حليف التصميم'],
                    ['fr' => 'Tranquillité d\'esprit', 'en' => 'Peace of mind', 'ar' => 'راحة البال'],
                ],
                'materials' => [
                    ['fr' => 'Poutres aluminium', 'en' => 'Aluminum beams', 'ar' => 'عوارض الألومنيوم'],
                    ['fr' => 'Toiture polycarbonate', 'en' => 'Polycarbonate roof', 'ar' => 'سقف بولي كربونات'],
                    ['fr' => 'Système drainage', 'en' => 'Drainage system', 'ar' => 'نظام الصرف'],
                    ['fr' => 'Éclairage LED', 'en' => 'LED lighting', 'ar' => 'إضاءة LED'],
                ],
                'specs' => [
                    ['label' => 'Épaisseur', 'value' => '2.0 - 3.0 mm'],
                    ['label' => 'Portée max', 'value' => '6 m'],
                    ['label' => 'Couleurs', 'value' => '100+'],
                ],
                'sort_order' => 6,
            ],
            [
                'slug' => 'sun_breakers',
                'title' => [
                    'fr' => 'Brise-soleil',
                    'en' => 'Sun Breakers',
                    'ar' => 'كاسرات الشمس',
                ],
                'short_description' => [
                    'fr' => 'Brise-soleil en aluminium pour contrôler la lumière et réduire la chaleur.',
                    'en' => 'Aluminum sun breakers to control light and reduce heat.',
                    'ar' => 'كاسرات شمس من الألومنيوم للتحكم في الضوء وتقليل الحرارة.',
                ],
                'description' => [
                    'fr' => 'Nos brise-soleil orientables permettent un contrôle optimal de la luminosité et contribuent à l\'efficacité énergétique du bâtiment.',
                    'en' => 'Our adjustable sun breakers allow optimal brightness control and contribute to building energy efficiency.',
                    'ar' => 'تتيح كاسرات الشمس القابلة للتوجيه لدينا التحكم الأمثل في السطوع وتساهم في كفاءة الطاقة للمبنى.',
                ],
                'icon' => 'sun',
                'svg_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>',
                'color' => 'yellow',
                'features' => [
                    ['fr' => 'Confort thermique', 'en' => 'Thermal comfort', 'ar' => 'راحة حرارية'],
                    ['fr' => 'Robustesse', 'en' => 'Robustness', 'ar' => 'المتانة'],
                    ['fr' => 'Design moderne', 'en' => 'Modern design', 'ar' => 'تصميم عصري'],
                    ['fr' => 'Sans entretien', 'en' => 'No maintenance', 'ar' => 'بدون صيانة'],
                    ['fr' => 'Intégration harmonieuse', 'en' => 'Harmonious integration', 'ar' => 'تكامل متناغم'],
                    ['fr' => 'Économies d\'énergie', 'en' => 'Energy savings', 'ar' => 'توفير الطاقة'],
                ],
                'materials' => [
                    ['fr' => 'Lames aluminium', 'en' => 'Aluminum blades', 'ar' => 'شفرات الألومنيوم'],
                    ['fr' => 'Système pivot', 'en' => 'Pivot system', 'ar' => 'نظام المحور'],
                    ['fr' => 'Mécanisme de contrôle', 'en' => 'Control mechanism', 'ar' => 'آلية التحكم'],
                    ['fr' => 'Laquage poudre', 'en' => 'Powder coating', 'ar' => 'طلاء بودرة'],
                ],
                'specs' => [
                    ['label' => 'Largeur lames', 'value' => '100 - 300 mm'],
                    ['label' => 'Angle', 'value' => '0 - 90°'],
                    ['label' => 'Couleurs', 'value' => '200+'],
                ],
                'sort_order' => 7,
            ],
            [
                'slug' => 'mosquito_nets',
                'title' => [
                    'fr' => 'Moustiquaires',
                    'en' => 'Mosquito Nets',
                    'ar' => 'شبكات البعوض',
                ],
                'short_description' => [
                    'fr' => 'Moustiquaires en aluminium pour une protection efficace contre les insectes.',
                    'en' => 'Aluminum mosquito nets for effective insect protection.',
                    'ar' => 'شبكات بعوض من الألومنيوم لحماية فعالة من الحشرات.',
                ],
                'description' => [
                    'fr' => 'Nos moustiquaires en aluminium offrent une protection discrète et efficace tout en laissant passer l\'air et la lumière.',
                    'en' => 'Our aluminum mosquito nets offer discreet and effective protection while allowing air and light to pass through.',
                    'ar' => 'توفر شبكات البعوض المصنوعة من الألومنيوم لدينا حماية سرية وفعالة مع السماح بمرور الهواء والضوء.',
                ],
                'icon' => 'grid-3x3',
                'svg_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/><path d="M15 3v18"/></svg>',
                'color' => 'teal',
                'features' => [
                    ['fr' => 'Protection efficace', 'en' => 'Effective protection', 'ar' => 'حماية فعالة'],
                    ['fr' => 'Robustesse', 'en' => 'Robustness', 'ar' => 'المتانة'],
                    ['fr' => 'Installation facile', 'en' => 'Easy installation', 'ar' => 'تركيب سهل'],
                    ['fr' => 'Confort optimal', 'en' => 'Optimal comfort', 'ar' => 'راحة مثالية'],
                    ['fr' => 'Design discret', 'en' => 'Discreet design', 'ar' => 'تصميم سري'],
                    ['fr' => 'Tranquillité d\'esprit', 'en' => 'Peace of mind', 'ar' => 'راحة البال'],
                ],
                'materials' => [
                    ['fr' => 'Cadre aluminium', 'en' => 'Aluminum frame', 'ar' => 'إطار الألومنيوم'],
                    ['fr' => 'Toile fibre de verre', 'en' => 'Fiberglass mesh', 'ar' => 'شبكة الألياف الزجاجية'],
                    ['fr' => 'Joints brosse', 'en' => 'Brush seals', 'ar' => 'حشوات فرشاة'],
                    ['fr' => 'Mécanisme enrouleur', 'en' => 'Roller mechanism', 'ar' => 'آلية التدوير'],
                ],
                'specs' => [
                    ['label' => 'Densité toile', 'value' => '18x16'],
                    ['label' => 'Profilé cadre', 'value' => '25 - 45 mm'],
                    ['label' => 'Couleurs', 'value' => '30+'],
                ],
                'sort_order' => 8,
            ],
            [
                'slug' => 'space_design',
                'title' => [
                    'fr' => 'Aménagement d\'espaces',
                    'en' => 'Space Design',
                    'ar' => 'تصميم المساحات',
                ],
                'short_description' => [
                    'fr' => 'Cloisons et séparations en aluminium pour organiser vos espaces intérieurs.',
                    'en' => 'Aluminum partitions and dividers to organize your interior spaces.',
                    'ar' => 'فواصل وحواجز من الألومنيوم لتنظيم مساحاتك الداخلية.',
                ],
                'description' => [
                    'fr' => 'Nos solutions d\'aménagement en aluminium et verre permettent de créer des espaces modulables tout en préservant la luminosité.',
                    'en' => 'Our aluminum and glass layout solutions allow you to create modular spaces while preserving brightness.',
                    'ar' => 'تتيح لك حلول التخطيط المصنوعة من الألومنيوم والزجاج إنشاء مساحات قابلة للتعديل مع الحفاظ على السطوع.',
                ],
                'icon' => 'layout-grid',
                'svg_icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
                'color' => 'indigo',
                'features' => [
                    ['fr' => 'Robustesse', 'en' => 'Robustness', 'ar' => 'المتانة'],
                    ['fr' => 'Élégance du verre', 'en' => 'Glass elegance', 'ar' => 'أناقة الزجاج'],
                    ['fr' => 'Luminosité préservée', 'en' => 'Preserved brightness', 'ar' => 'الحفاظ على السطوع'],
                    ['fr' => 'Polyvalence', 'en' => 'Versatility', 'ar' => 'تعدد الاستخدامات'],
                    ['fr' => 'Installation facile', 'en' => 'Easy installation', 'ar' => 'تركيب سهل'],
                    ['fr' => 'Sans entretien', 'en' => 'No maintenance', 'ar' => 'بدون صيانة'],
                    ['fr' => 'Design contemporain', 'en' => 'Contemporary design', 'ar' => 'تصميم معاصر'],
                ],
                'materials' => [
                    ['fr' => 'Cloisons aluminium', 'en' => 'Aluminum partitions', 'ar' => 'فواصل الألومنيوم'],
                    ['fr' => 'Panneaux verre', 'en' => 'Glass panels', 'ar' => 'ألواح زجاجية'],
                    ['fr' => 'Systèmes portes', 'en' => 'Door systems', 'ar' => 'أنظمة الأبواب'],
                    ['fr' => 'Joints acoustiques', 'en' => 'Acoustic seals', 'ar' => 'حشوات صوتية'],
                ],
                'specs' => [
                    ['label' => 'Options verre', 'value' => '6 - 12 mm'],
                    ['label' => 'Hauteur max', 'value' => '3.5 m'],
                    ['label' => 'Couleurs', 'value' => '200+'],
                ],
                'sort_order' => 9,
            ],
        ];

        foreach ($services as $serviceData) {
            Service::updateOrCreate(
                ['slug' => $serviceData['slug']],
                array_merge($serviceData, ['is_active' => true])
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\ChatbotFlow;
use Illuminate\Database\Seeder;

class ChatbotFlowSeeder extends Seeder
{
    public function run(): void
    {
        $flows = [
            // Welcome message
            [
                'trigger' => 'welcome',
                'trigger_type' => 'button',
                'message' => [
                    'fr' => "Bonjour ! 👋 Je suis l'assistant virtuel de Promo Alu Plus. Comment puis-je vous aider ?",
                    'en' => "Hello! 👋 I'm Promo Alu Plus virtual assistant. How can I help you?",
                    'ar' => 'مرحبا! 👋 أنا المساعد الافتراضي لـ Promo Alu Plus. كيف يمكنني مساعدتك؟',
                ],
                'quick_replies' => [
                    ['text' => ['fr' => '📋 Demander un devis', 'en' => '📋 Request a quote', 'ar' => '📋 طلب عرض سعر'], 'action' => 'flow:quote'],
                    ['text' => ['fr' => '🏠 Nos services', 'en' => '🏠 Our services', 'ar' => '🏠 خدماتنا'], 'action' => 'flow:services'],
                    ['text' => ['fr' => '⏱️ Délais & Livraison', 'en' => '⏱️ Delivery times', 'ar' => '⏱️ مواعيد التسليم'], 'action' => 'flow:delivery'],
                    ['text' => ['fr' => '❓ Questions fréquentes', 'en' => '❓ FAQ', 'ar' => '❓ أسئلة شائعة'], 'action' => 'flow:faq'],
                    ['text' => ['fr' => '💬 Parler à un conseiller', 'en' => '💬 Talk to an advisor', 'ar' => '💬 تحدث إلى مستشار'], 'action' => 'flow:contact'],
                ],
                'order' => 0,
            ],

            // Quote information
            [
                'trigger' => 'quote',
                'trigger_type' => 'button',
                'keywords' => ['devis', 'prix', 'tarif', 'coût', 'combien', 'quote', 'price', 'cost', 'سعر', 'تكلفة'],
                'message' => [
                    'fr' => "📋 **Demande de devis**\n\nPour obtenir un devis gratuit et personnalisé :\n\n✅ Remplissez notre formulaire en ligne\n✅ Recevez une réponse sous 48h\n✅ Devis détaillé et sans engagement\n\nVoulez-vous remplir le formulaire maintenant ?",
                    'en' => "📋 **Quote Request**\n\nTo get a free personalized quote:\n\n✅ Fill out our online form\n✅ Receive a response within 48h\n✅ Detailed quote with no commitment\n\nWould you like to fill out the form now?",
                    'ar' => "📋 **طلب عرض سعر**\n\nللحصول على عرض سعر مجاني ومخصص:\n\n✅ املأ نموذجنا عبر الإنترنت\n✅ احصل على رد خلال 48 ساعة\n✅ عرض سعر مفصل بدون التزام\n\nهل تريد ملء النموذج الآن؟",
                ],
                'quick_replies' => [
                    ['text' => ['fr' => '✅ Oui, remplir le formulaire', 'en' => '✅ Yes, fill out the form', 'ar' => '✅ نعم، املأ النموذج'], 'action' => 'url:/contact'],
                    ['text' => ['fr' => '📱 Contacter par WhatsApp', 'en' => '📱 Contact via WhatsApp', 'ar' => '📱 تواصل عبر واتساب'], 'action' => 'flow:whatsapp'],
                    ['text' => ['fr' => '← Menu principal', 'en' => '← Main menu', 'ar' => '← القائمة الرئيسية'], 'action' => 'flow:welcome'],
                ],
                'order' => 1,
            ],

            // Services
            [
                'trigger' => 'services',
                'trigger_type' => 'button',
                'keywords' => ['service', 'produit', 'fenêtre', 'porte', 'cuisine', 'pergola', 'volet', 'window', 'door', 'kitchen', 'نافذة', 'باب', 'مطبخ'],
                'message' => [
                    'fr' => "🏠 **Nos Services**\n\nNous proposons une gamme complète de menuiserie aluminium :\n\n• 🪟 Fenêtres sur mesure\n• 🚪 Portes d'entrée et intérieures\n• 🏡 Pergolas bioclimatiques\n• 🍳 Cuisines aluminium\n• 🔒 Volets électriques\n• 🛡️ Garde-corps\n• 🏪 Rideaux métalliques\n\nQuel service vous intéresse ?",
                    'en' => "🏠 **Our Services**\n\nWe offer a complete range of aluminum joinery:\n\n• 🪟 Custom windows\n• 🚪 Entry and interior doors\n• 🏡 Bioclimatic pergolas\n• 🍳 Aluminum kitchens\n• 🔒 Electric shutters\n• 🛡️ Railings\n• 🏪 Metal curtains\n\nWhich service interests you?",
                    'ar' => "🏠 **خدماتنا**\n\nنقدم مجموعة كاملة من نجارة الألمنيوم:\n\n• 🪟 نوافذ مخصصة\n• 🚪 أبواب مدخل وداخلية\n• 🏡 برجولات بيومناخية\n• 🍳 مطابخ ألمنيوم\n• 🔒 شتر كهربائي\n• 🛡️ حواجز\n• 🏪 ستائر معدنية\n\nأي خدمة تهمك؟",
                ],
                'quick_replies' => [
                    ['text' => ['fr' => '🪟 Fenêtres', 'en' => '🪟 Windows', 'ar' => '🪟 نوافذ'], 'action' => 'url:/services#windows'],
                    ['text' => ['fr' => '🚪 Portes', 'en' => '🚪 Doors', 'ar' => '🚪 أبواب'], 'action' => 'url:/services#doors'],
                    ['text' => ['fr' => '🍳 Cuisines', 'en' => '🍳 Kitchens', 'ar' => '🍳 مطابخ'], 'action' => 'url:/services#kitchen'],
                    ['text' => ['fr' => '📋 Demander un devis', 'en' => '📋 Request a quote', 'ar' => '📋 طلب عرض سعر'], 'action' => 'flow:quote'],
                    ['text' => ['fr' => '← Menu principal', 'en' => '← Main menu', 'ar' => '← القائمة الرئيسية'], 'action' => 'flow:welcome'],
                ],
                'order' => 2,
            ],

            // Delivery times
            [
                'trigger' => 'delivery',
                'trigger_type' => 'button',
                'keywords' => ['délai', 'livraison', 'temps', 'durée', 'combien de temps', 'delivery', 'time', 'how long', 'وقت', 'تسليم', 'مدة'],
                'message' => [
                    'fr' => "⏱️ **Délais de réalisation**\n\nNos délais moyens :\n\n• Fenêtres : 2-3 semaines\n• Portes : 2-3 semaines\n• Cuisines : 3-4 semaines\n• Pergolas : 3-4 semaines\n• Volets : 2 semaines\n\n📍 Installation incluse dans toute la Tunisie\n⚡ Service express disponible (+30%)",
                    'en' => "⏱️ **Production Times**\n\nOur average lead times:\n\n• Windows: 2-3 weeks\n• Doors: 2-3 weeks\n• Kitchens: 3-4 weeks\n• Pergolas: 3-4 weeks\n• Shutters: 2 weeks\n\n📍 Installation included throughout Tunisia\n⚡ Express service available (+30%)",
                    'ar' => "⏱️ **مواعيد التسليم**\n\nمواعيدنا المتوسطة:\n\n• النوافذ: 2-3 أسابيع\n• الأبواب: 2-3 أسابيع\n• المطابخ: 3-4 أسابيع\n• البرجولات: 3-4 أسابيع\n• الشتر: أسبوعين\n\n📍 التركيب متضمن في كل تونس\n⚡ خدمة سريعة متاحة (+30%)",
                ],
                'quick_replies' => [
                    ['text' => ['fr' => '📋 Demander un devis', 'en' => '📋 Request a quote', 'ar' => '📋 طلب عرض سعر'], 'action' => 'flow:quote'],
                    ['text' => ['fr' => '💬 Parler à un conseiller', 'en' => '💬 Talk to an advisor', 'ar' => '💬 تحدث إلى مستشار'], 'action' => 'flow:contact'],
                    ['text' => ['fr' => '← Menu principal', 'en' => '← Main menu', 'ar' => '← القائمة الرئيسية'], 'action' => 'flow:welcome'],
                ],
                'order' => 3,
            ],

            // FAQ
            [
                'trigger' => 'faq',
                'trigger_type' => 'button',
                'keywords' => ['question', 'faq', 'aide', 'help', 'سؤال', 'مساعدة'],
                'message' => [
                    'fr' => "❓ **Questions Fréquentes**\n\nSélectionnez une question ou tapez la vôtre :",
                    'en' => "❓ **Frequently Asked Questions**\n\nSelect a question or type your own:",
                    'ar' => "❓ **الأسئلة الشائعة**\n\nاختر سؤالاً أو اكتب سؤالك:",
                ],
                'quick_replies' => [
                    ['text' => ['fr' => '💰 Quels sont vos tarifs ?', 'en' => '💰 What are your prices?', 'ar' => '💰 ما هي أسعاركم؟'], 'action' => 'flow:pricing'],
                    ['text' => ['fr' => '🛡️ Quelle garantie ?', 'en' => '🛡️ What warranty?', 'ar' => '🛡️ ما هو الضمان؟'], 'action' => 'flow:warranty'],
                    ['text' => ['fr' => '🌍 Livrez-vous partout ?', 'en' => '🌍 Do you deliver everywhere?', 'ar' => '🌍 هل تسلمون في كل مكان؟'], 'action' => 'flow:coverage'],
                    ['text' => ['fr' => '← Menu principal', 'en' => '← Main menu', 'ar' => '← القائمة الرئيسية'], 'action' => 'flow:welcome'],
                ],
                'order' => 4,
            ],

            // Pricing FAQ
            [
                'trigger' => 'pricing',
                'trigger_type' => 'button',
                'message' => [
                    'fr' => "💰 **Nos Tarifs**\n\nNos prix varient selon :\n• Les dimensions\n• Le type de vitrage\n• Les finitions choisies\n• La complexité du projet\n\n✅ **Devis gratuit et détaillé sous 48h**\n\nChaque projet est unique, c'est pourquoi nous établissons un devis personnalisé.",
                    'en' => "💰 **Our Prices**\n\nOur prices vary according to:\n• Dimensions\n• Type of glazing\n• Chosen finishes\n• Project complexity\n\n✅ **Free detailed quote within 48h**\n\nEach project is unique, which is why we provide a personalized quote.",
                    'ar' => "💰 **أسعارنا**\n\nأسعارنا تختلف حسب:\n• الأبعاد\n• نوع الزجاج\n• التشطيبات المختارة\n• تعقيد المشروع\n\n✅ **عرض سعر مجاني ومفصل خلال 48 ساعة**\n\nكل مشروع فريد، لذلك نقدم عرض سعر مخصص.",
                ],
                'quick_replies' => [
                    ['text' => ['fr' => '📋 Obtenir un devis', 'en' => '📋 Get a quote', 'ar' => '📋 احصل على عرض سعر'], 'action' => 'flow:quote'],
                    ['text' => ['fr' => '← Retour FAQ', 'en' => '← Back to FAQ', 'ar' => '← العودة للأسئلة'], 'action' => 'flow:faq'],
                ],
                'order' => 10,
            ],

            // Warranty FAQ
            [
                'trigger' => 'warranty',
                'trigger_type' => 'button',
                'keywords' => ['garantie', 'warranty', 'ضمان'],
                'message' => [
                    'fr' => "🛡️ **Notre Garantie**\n\n✅ **10 ans** sur la structure aluminium\n✅ **5 ans** sur les accessoires et mécanismes\n✅ **2 ans** sur la pose et l'installation\n\n🔧 Service après-vente réactif\n📞 Support technique disponible",
                    'en' => "🛡️ **Our Warranty**\n\n✅ **10 years** on aluminum structure\n✅ **5 years** on accessories and mechanisms\n✅ **2 years** on installation\n\n🔧 Responsive after-sales service\n📞 Technical support available",
                    'ar' => "🛡️ **ضماننا**\n\n✅ **10 سنوات** على هيكل الألمنيوم\n✅ **5 سنوات** على الملحقات والآليات\n✅ **سنتان** على التركيب\n\n🔧 خدمة ما بعد البيع سريعة\n📞 دعم فني متاح",
                ],
                'quick_replies' => [
                    ['text' => ['fr' => '📋 Demander un devis', 'en' => '📋 Request a quote', 'ar' => '📋 طلب عرض سعر'], 'action' => 'flow:quote'],
                    ['text' => ['fr' => '← Retour FAQ', 'en' => '← Back to FAQ', 'ar' => '← العودة للأسئلة'], 'action' => 'flow:faq'],
                ],
                'order' => 11,
            ],

            // Coverage FAQ
            [
                'trigger' => 'coverage',
                'trigger_type' => 'button',
                'keywords' => ['zone', 'région', 'livraison', 'tunisie', 'coverage', 'area', 'منطقة'],
                'message' => [
                    'fr' => "🌍 **Zone de couverture**\n\n✅ Nous intervenons dans **toute la Tunisie** :\n\n• Grand Tunis\n• Cap Bon\n• Sahel\n• Sfax et Sud\n• Nord-Ouest\n\n🚚 Livraison et installation incluses\n🌐 Service adapté aux expatriés",
                    'en' => "🌍 **Coverage Area**\n\n✅ We operate throughout **all of Tunisia**:\n\n• Greater Tunis\n• Cap Bon\n• Sahel\n• Sfax and South\n• Northwest\n\n🚚 Delivery and installation included\n🌐 Service adapted for expatriates",
                    'ar' => "🌍 **منطقة التغطية**\n\n✅ نعمل في **كل تونس**:\n\n• تونس الكبرى\n• الوطن القبلي\n• الساحل\n• صفاقس والجنوب\n• الشمال الغربي\n\n🚚 التوصيل والتركيب متضمن\n🌐 خدمة مخصصة للمغتربين",
                ],
                'quick_replies' => [
                    ['text' => ['fr' => '📋 Demander un devis', 'en' => '📋 Request a quote', 'ar' => '📋 طلب عرض سعر'], 'action' => 'flow:quote'],
                    ['text' => ['fr' => '← Retour FAQ', 'en' => '← Back to FAQ', 'ar' => '← العودة للأسئلة'], 'action' => 'flow:faq'],
                ],
                'order' => 12,
            ],

            // Contact / WhatsApp
            [
                'trigger' => 'contact',
                'trigger_type' => 'button',
                'keywords' => ['contact', 'appeler', 'téléphone', 'conseiller', 'humain', 'call', 'phone', 'advisor', 'اتصال', 'هاتف'],
                'message' => [
                    'fr' => "💬 **Parler à un conseiller**\n\nNotre équipe est disponible pour vous :\n\n📞 Téléphone : +216 26 192 898\n📱 WhatsApp : +216 26 192 898\n📧 Email : contact@promoaluplus.tn\n\n⏰ Horaires :\n• Lun-Ven : 8h-18h\n• Samedi : 9h-13h",
                    'en' => "💬 **Talk to an Advisor**\n\nOur team is available for you:\n\n📞 Phone: +216 26 192 898\n📱 WhatsApp: +216 26 192 898\n📧 Email: contact@promoaluplus.tn\n\n⏰ Hours:\n• Mon-Fri: 8am-6pm\n• Saturday: 9am-1pm",
                    'ar' => "💬 **تحدث إلى مستشار**\n\nفريقنا متاح لك:\n\n📞 الهاتف: +216 26 192 898\n📱 واتساب: +216 26 192 898\n📧 البريد: contact@promoaluplus.tn\n\n⏰ أوقات العمل:\n• الإثنين-الجمعة: 8ص-6م\n• السبت: 9ص-1م",
                ],
                'quick_replies' => [
                    ['text' => ['fr' => '📱 Ouvrir WhatsApp', 'en' => '📱 Open WhatsApp', 'ar' => '📱 فتح واتساب'], 'action' => 'flow:whatsapp'],
                    ['text' => ['fr' => '📧 Envoyer un email', 'en' => '📧 Send an email', 'ar' => '📧 إرسال بريد'], 'action' => 'url:/contact'],
                    ['text' => ['fr' => '← Menu principal', 'en' => '← Main menu', 'ar' => '← القائمة الرئيسية'], 'action' => 'flow:welcome'],
                ],
                'order' => 5,
            ],

            // WhatsApp redirect
            [
                'trigger' => 'whatsapp',
                'trigger_type' => 'button',
                'keywords' => ['whatsapp', 'واتساب'],
                'message' => [
                    'fr' => "📱 **WhatsApp**\n\nVous allez être redirigé vers WhatsApp pour discuter avec notre équipe.",
                    'en' => "📱 **WhatsApp**\n\nYou will be redirected to WhatsApp to chat with our team.",
                    'ar' => "📱 **واتساب**\n\nسيتم توجيهك إلى واتساب للتحدث مع فريقنا.",
                ],
                'action' => 'whatsapp',
                'action_value' => '+21612345678',
                'quick_replies' => [
                    ['text' => ['fr' => '← Menu principal', 'en' => '← Main menu', 'ar' => '← القائمة الرئيسية'], 'action' => 'flow:welcome'],
                ],
                'order' => 20,
            ],

            // Fallback
            [
                'trigger' => 'fallback',
                'trigger_type' => 'fallback',
                'message' => [
                    'fr' => "🤔 Je n'ai pas compris votre demande.\n\nPuis-je vous aider autrement ? Vous pouvez aussi parler directement à un conseiller.",
                    'en' => "🤔 I didn't understand your request.\n\nCan I help you with something else? You can also speak directly to an advisor.",
                    'ar' => "🤔 لم أفهم طلبك.\n\nهل يمكنني مساعدتك بشيء آخر؟ يمكنك أيضاً التحدث مباشرة إلى مستشار.",
                ],
                'quick_replies' => [
                    ['text' => ['fr' => '← Menu principal', 'en' => '← Main menu', 'ar' => '← القائمة الرئيسية'], 'action' => 'flow:welcome'],
                    ['text' => ['fr' => '💬 Parler à un conseiller', 'en' => '💬 Talk to an advisor', 'ar' => '💬 تحدث إلى مستشار'], 'action' => 'flow:contact'],
                ],
                'order' => 99,
            ],
        ];

        foreach ($flows as $flow) {
            ChatbotFlow::updateOrCreate(
                ['trigger' => $flow['trigger']],
                $flow
            );
        }
    }
}

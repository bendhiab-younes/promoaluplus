<?php

namespace App\Http\Controllers;

use App\Models\ChatbotFlow;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function getResponse(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        $locale = app()->getLocale();
        $action = $request->input('action');
        $userMessage = $request->input('message');

        // Handle action-based triggers (button clicks)
        if ($action) {
            // Check if it's a flow trigger
            if (str_starts_with($action, 'flow:')) {
                $trigger = str_replace('flow:', '', $action);
                $flow = ChatbotFlow::findByTrigger($trigger);
                
                if ($flow) {
                    return $this->formatFlowResponse($flow, $locale);
                }
            }

            // Check if it's an FAQ trigger
            if (str_starts_with($action, 'faq:')) {
                $faqId = str_replace('faq:', '', $action);
                $faq = Faq::find($faqId);
                
                if ($faq) {
                    return response()->json([
                        'success' => true,
                        'message' => $faq->getTranslatedAnswer(),
                        'quick_replies' => [
                            ['text' => $this->getLocalizedText('back_to_menu', $locale), 'action' => 'flow:welcome'],
                            ['text' => $this->getLocalizedText('other_question', $locale), 'action' => 'flow:faq'],
                        ],
                    ]);
                }
            }
        }

        // Handle text message (keyword matching)
        if ($userMessage) {
            // First try keyword matching in flows
            $flow = ChatbotFlow::findByKeyword($userMessage);
            if ($flow) {
                return $this->formatFlowResponse($flow, $locale);
            }

            // Try FAQ search
            $faq = $this->searchFaq($userMessage, $locale);
            if ($faq) {
                return response()->json([
                    'success' => true,
                    'message' => $faq->getTranslatedAnswer(),
                    'quick_replies' => [
                        ['text' => $this->getLocalizedText('back_to_menu', $locale), 'action' => 'flow:welcome'],
                        ['text' => $this->getLocalizedText('contact_advisor', $locale), 'action' => 'flow:contact'],
                    ],
                ]);
            }
        }

        // Fallback response
        $fallback = ChatbotFlow::getFallbackFlow();
        if ($fallback) {
            return $this->formatFlowResponse($fallback, $locale);
        }

        // Default fallback
        return response()->json([
            'success' => true,
            'message' => $this->getLocalizedText('fallback_message', $locale),
            'quick_replies' => [
                ['text' => $this->getLocalizedText('back_to_menu', $locale), 'action' => 'flow:welcome'],
                ['text' => $this->getLocalizedText('contact_advisor', $locale), 'action' => 'flow:contact'],
            ],
        ]);
    }

    public function getWelcome(): JsonResponse
    {
        $locale = app()->getLocale();
        $welcome = ChatbotFlow::getWelcomeFlow();

        if ($welcome) {
            return $this->formatFlowResponse($welcome, $locale);
        }

        // Default welcome if none configured
        return response()->json([
            'success' => true,
            'message' => $this->getLocalizedText('welcome_message', $locale),
            'quick_replies' => [
                ['text' => $this->getLocalizedText('request_quote', $locale), 'action' => 'flow:quote'],
                ['text' => $this->getLocalizedText('our_services', $locale), 'action' => 'flow:services'],
                ['text' => $this->getLocalizedText('faq', $locale), 'action' => 'flow:faq'],
                ['text' => $this->getLocalizedText('contact_advisor', $locale), 'action' => 'flow:contact'],
            ],
        ]);
    }

    public function getFaqs(): JsonResponse
    {
        $locale = app()->getLocale();
        $faqs = Faq::where('is_active', true)->orderBy('order')->get();

        $quickReplies = $faqs->map(function ($faq) {
            return [
                'text' => $faq->getTranslatedQuestion(),
                'action' => 'faq:' . $faq->id,
            ];
        })->toArray();

        $quickReplies[] = ['text' => $this->getLocalizedText('back_to_menu', $locale), 'action' => 'flow:welcome'];

        return response()->json([
            'success' => true,
            'message' => $this->getLocalizedText('faq_intro', $locale),
            'quick_replies' => $quickReplies,
        ]);
    }

    private function formatFlowResponse(ChatbotFlow $flow, string $locale): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $flow->getMessageForLocale($locale),
            'quick_replies' => $flow->getQuickRepliesForLocale($locale),
        ];

        if ($flow->action) {
            $response['action'] = $flow->action;
            $response['action_value'] = $flow->action_value;
        }

        return response()->json($response);
    }

    private function searchFaq(string $query, string $locale): ?Faq
    {
        $query = mb_strtolower($query);
        
        return Faq::where('is_active', true)
            ->get()
            ->first(function ($faq) use ($query, $locale) {
                $question = mb_strtolower($faq->getTranslatedQuestion());
                $keywords = $faq->keywords ?? [];
                
                // Check question similarity
                if (str_contains($question, $query) || str_contains($query, $question)) {
                    return true;
                }
                
                // Check keywords
                foreach ($keywords as $keyword) {
                    if (str_contains($query, mb_strtolower($keyword))) {
                        return true;
                    }
                }
                
                return false;
            });
    }

    private function getLocalizedText(string $key, string $locale): string
    {
        $texts = [
            'welcome_message' => [
                'fr' => 'Bonjour ! 👋 Comment puis-je vous aider aujourd\'hui ?',
                'en' => 'Hello! 👋 How can I help you today?',
                'ar' => 'مرحبا! 👋 كيف يمكنني مساعدتك اليوم؟',
            ],
            'fallback_message' => [
                'fr' => 'Je n\'ai pas compris votre demande. Puis-je vous aider autrement ?',
                'en' => 'I didn\'t understand your request. Can I help you with something else?',
                'ar' => 'لم أفهم طلبك. هل يمكنني مساعدتك بشيء آخر؟',
            ],
            'back_to_menu' => [
                'fr' => '← Menu principal',
                'en' => '← Main menu',
                'ar' => '← القائمة الرئيسية',
            ],
            'other_question' => [
                'fr' => 'Autre question',
                'en' => 'Other question',
                'ar' => 'سؤال آخر',
            ],
            'contact_advisor' => [
                'fr' => '💬 Parler à un conseiller',
                'en' => '💬 Talk to an advisor',
                'ar' => '💬 تحدث إلى مستشار',
            ],
            'request_quote' => [
                'fr' => '📋 Demander un devis',
                'en' => '📋 Request a quote',
                'ar' => '📋 طلب عرض سعر',
            ],
            'our_services' => [
                'fr' => '🏠 Nos services',
                'en' => '🏠 Our services',
                'ar' => '🏠 خدماتنا',
            ],
            'faq' => [
                'fr' => '❓ Questions fréquentes',
                'en' => '❓ FAQ',
                'ar' => '❓ أسئلة شائعة',
            ],
            'faq_intro' => [
                'fr' => 'Voici les questions les plus fréquentes. Sélectionnez celle qui vous intéresse :',
                'en' => 'Here are the most frequent questions. Select the one that interests you:',
                'ar' => 'إليك الأسئلة الأكثر شيوعاً. اختر ما يهمك:',
            ],
        ];

        return $texts[$key][$locale] ?? $texts[$key]['fr'] ?? $key;
    }
}

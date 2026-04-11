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
        // Load FAQs from the new JSON file
        $jsonFile = base_path('../content_docs/json/questions_frequentes.json');
        $data = json_decode(file_get_contents($jsonFile), true);

        if (! is_array($data) || ! isset($data['fr']) || ! is_array($data['fr'])) {
            return;
        }

        foreach ($data['fr'] as $index => $faqFr) {
            $faqEn = $data['en'][$index] ?? [];
            $faqAr = $data['ar'][$index] ?? [];

            $questionFr = $faqFr['question'] ?? null;
            if (! is_string($questionFr) || trim($questionFr) === '') {
                continue;
            }

            $answerFr = $faqFr['answer'] ?? '';

            Faq::updateOrCreate(
                ['question->fr' => $questionFr],
                [
                    'question' => [
                        'fr' => $questionFr,
                        'en' => $faqEn['question'] ?? $questionFr,
                        'ar' => $faqAr['question'] ?? $questionFr,
                    ],
                    'answer' => [
                        'fr' => $answerFr,
                        'en' => $faqEn['answer'] ?? $answerFr,
                        'ar' => $faqAr['answer'] ?? $answerFr,
                    ],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}

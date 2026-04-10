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

        foreach ($data['fr'] as $faq) {
            Faq::updateOrCreate(
                ['question->fr' => $faq['question']],
                [
                    'answer' => [
                        'fr' => $faq['answer'],
                        'en' => $data['en'][array_search($faq, $data['fr'])]['answer'] ?? null,
                        'ar' => $data['ar'][array_search($faq, $data['fr'])]['answer'] ?? null
                    ],
                    'is_active' => true
                ]
            );
        }
    }
}

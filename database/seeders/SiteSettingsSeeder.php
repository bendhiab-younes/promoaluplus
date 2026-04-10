<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Load settings and FAQs from updated JSON file
        $jsonFile = database_path('seeders/site_settings.json');
        $missionFile = base_path('../content_docs/json/mission_valeurs.json');
        
        $data = json_decode(file_get_contents($jsonFile), true);
        $missionData = json_decode(file_get_contents($missionFile), true);

        // Load mission, vision, and values from the new JSON file
        SiteSetting::updateOrCreate(
            ['key' => 'mission'],
            ['value' => json_encode($missionData['fr']['mission'])]
        );

        SiteSetting::updateOrCreate(
            ['key' => 'vision'],
            ['value' => json_encode($missionData['fr']['vision'])]
        );

        SiteSetting::updateOrCreate(
            ['key' => 'valeurs'],
            ['value' => json_encode($missionData['fr']['valeurs'])]
        );

        // Insert settings
        foreach ($data['settings'] as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // Insert FAQs
        foreach ($data['faqs'] as $faq) {
            Faq::updateOrCreate(
                ['question->fr' => $faq['question']['fr']],
                array_merge($faq, ['is_active' => true])
            );
        }

        // Load mission, vision, and values from the new JSON file
        $jsonFile = base_path('content_docs/json/mission_valeurs.json');
        $data = json_decode(file_get_contents($jsonFile), true);

        SiteSetting::updateOrCreate(
            ['key' => 'mission'],
            ['value' => json_encode($data['fr']['mission'])]
        );

        SiteSetting::updateOrCreate(
            ['key' => 'vision'],
            ['value' => json_encode($data['fr']['vision'])]
        );

        SiteSetting::updateOrCreate(
            ['key' => 'valeurs'],
            ['value' => json_encode($data['fr']['valeurs'])]
        );
    }
}

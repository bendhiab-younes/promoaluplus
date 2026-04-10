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
        // Load services from the content JSON file
        $servicesFile = base_path('../content_docs/json/services.json');
        $servicesData = json_decode(file_get_contents($servicesFile), true);

        $slugMap = [
            'cuisines' => 'kitchen',
            'portes' => 'doors',
            'fenetres' => 'windows',
        ];

        foreach ($servicesData['fr'] as $key => $service) {
            $slug = $slugMap[$key] ?? $key;
            $serviceEn = $servicesData['en'][$key] ?? [];
            $serviceAr = $servicesData['ar'][$key] ?? [];

            Service::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => [
                        'fr' => $service['title'] ?? $key,
                        'en' => $serviceEn['title'] ?? null,
                        'ar' => $serviceAr['title'] ?? null,
                    ],
                    'description' => [
                        'fr' => $service['description'],
                        'en' => $serviceEn['description'] ?? null,
                        'ar' => $serviceAr['description'] ?? null,
                    ],
                    'features' => [
                        'fr' => $service['features'] ?? [],
                        'en' => $serviceEn['features'] ?? [],
                        'ar' => $serviceAr['features'] ?? [],
                    ],
                    'gallery' => $service['images'] ?? [],
                ]
            );
        }
    }
}

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

        // Load gallery overrides from the centralized image list
        $serviceImagesFile = base_path('../service_images.json');
        $serviceImagesData = file_exists($serviceImagesFile)
            ? json_decode(file_get_contents($serviceImagesFile), true)
            : [];

        if (! is_array($serviceImagesData)) {
            $serviceImagesData = [];
        }

        if (! is_array($servicesData) || ! isset($servicesData['fr']) || ! is_array($servicesData['fr'])) {
            return;
        }

        $slugMap = [
            'cuisines' => 'kitchen',
            'portes' => 'doors',
            'fenetres' => 'windows',
            'rolling_shutters' => 'rolling_shutters',
            'railings' => 'railings',
            'pergola' => 'pergola',
            'sun_breakers' => 'sun_breakers',
            'mosquito_nets' => 'mosquito_nets',
            'space_design' => 'space_design',
        ];

        $sortOrder = 1;

        foreach ($servicesData['fr'] as $key => $service) {
            $slug = $slugMap[$key] ?? $key;
            $serviceEn = $servicesData['en'][$key] ?? [];
            $serviceAr = $servicesData['ar'][$key] ?? [];
            $gallery = $serviceImagesData[$slug] ?? $serviceImagesData[$key] ?? ($service['images'] ?? []);

            if (! is_array($gallery)) {
                $gallery = [];
            }

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
                    'gallery' => array_values($gallery),
                    'sort_order' => $sortOrder,
                ]
            );

            $sortOrder++;
        }
    }
}

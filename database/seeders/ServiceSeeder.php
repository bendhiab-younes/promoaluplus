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
        $content = $this->readJson(database_path('seeders/content/services.json'));

        $sortOrder = 1;

        foreach ($this->serviceSlugMap() as $sourceKey => $slug) {
            $source = [
                'fr' => $content['fr'][$sourceKey] ?? [],
                'en' => $content['en'][$sourceKey] ?? [],
                'ar' => $content['ar'][$sourceKey] ?? [],
            ];

            if (! is_array($source['fr']) || empty($source['fr'])) {
                continue;
            }

            $descriptions = $this->buildLocalizedStrings($source, 'description');
            $gallery = $this->buildGallery($source);

            $service = Service::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $this->buildLocalizedStrings($source, 'title'),
                    'short_description' => $this->buildShortDescriptions($descriptions),
                    'description' => $descriptions,
                    'icon' => Service::DEFAULT_ICON_BY_SLUG[$slug] ?? 'wrench',
                    'color' => Service::DEFAULT_COLOR_BY_SLUG[$slug] ?? 'blue',
                    'image' => $gallery[0] ?? null,
                    'gallery' => $gallery,
                    'features' => $this->transposeLocalizedArray($source, 'features'),
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );

            // Deliberately outside the updateOrCreate payload: the fields above are
            // re-applied on every reseed, but a custom SVG is something an admin
            // pastes by hand, and stomping it would be unrecoverable. This fills the
            // field only while it is still empty.
            if (blank($service->svg_icon) && isset(Service::DEFAULT_SVG_ICON_BY_SLUG[$slug])) {
                $service->forceFill(['svg_icon' => Service::DEFAULT_SVG_ICON_BY_SLUG[$slug]])->save();
            }
        }
    }

    private function serviceSlugMap(): array
    {
        return [
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
    }

    private function readJson(string $path): array
    {
        if (! file_exists($path)) {
            return [];
        }

        $decoded = json_decode(file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function buildLocalizedStrings(array $source, string $field): array
    {
        $values = [];

        foreach (['fr', 'en', 'ar'] as $locale) {
            $value = $source[$locale][$field] ?? null;
            $values[$locale] = is_string($value) ? $value : '';
        }

        return $values;
    }

    private function buildShortDescriptions(array $descriptions): array
    {
        $shortDescriptions = [];

        foreach ($descriptions as $locale => $description) {
            $shortDescriptions[$locale] = $this->firstSentence($description);
        }

        return $shortDescriptions;
    }

    private function transposeLocalizedArray(array $source, string $field): array
    {
        $locales = ['fr', 'en', 'ar'];
        $itemsByLocale = [];

        foreach ($locales as $locale) {
            $itemsByLocale[$locale] = is_array($source[$locale][$field] ?? null) ? $source[$locale][$field] : [];
        }

        $maxCount = 0;

        foreach ($itemsByLocale as $items) {
            $maxCount = max($maxCount, count($items));
        }

        $transposed = [];

        for ($index = 0; $index < $maxCount; $index++) {
            $item = [];

            foreach ($locales as $locale) {
                $item[$locale] = $itemsByLocale[$locale][$index] ?? ($itemsByLocale['fr'][$index] ?? '');
            }

            $transposed[] = $item;
        }

        return $transposed;
    }

    private function buildGallery(array $source): array
    {
        $gallery = $source['fr']['images'] ?? [];

        if (! is_array($gallery) || empty($gallery)) {
            return [];
        }

        return array_values(array_filter($gallery, static fn ($item) => is_string($item) && trim($item) !== ''));
    }

    private function firstSentence(?string $text): string
    {
        if (! is_string($text)) {
            return '';
        }

        $trimmed = trim($text);

        if ($trimmed === '') {
            return '';
        }

        $parts = preg_split('/(?<=[.!?؟])\s+/u', $trimmed, 2);

        return trim($parts[0] ?? $trimmed);
    }
}

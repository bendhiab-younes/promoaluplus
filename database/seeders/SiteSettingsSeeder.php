<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    private const LOCALES = ['fr', 'en', 'ar'];

    /**
     * Every write here is first-write-wins: these keys are all editable in
     * Paramètres du site, so a reseed must never silently revert an admin's
     * edit back to the JSON source.
     */
    public function run(): void
    {
        $settingsData = $this->readJson(database_path('seeders/site_settings.json'));
        $historyData = $this->readJson(database_path('seeders/content/notre_histoire.json'));
        $expatServiceData = $this->readJson(database_path('seeders/content/service_tunisiens_etranger.json'));

        foreach (($settingsData['settings'] ?? []) as $setting) {
            if (! is_array($setting) || empty($setting['key'])) {
                continue;
            }

            SiteSetting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'] ?? null,
                    'type' => $setting['type'] ?? 'text',
                    'group' => $setting['group'] ?? 'general',
                ]
            );
        }

        $this->seedAboutSettings($historyData);
        $this->seedExpatServiceSettings($expatServiceData);
    }

    private function readJson(string $path): array
    {
        if (! file_exists($path)) {
            return [];
        }

        $decoded = json_decode(file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * `notre_histoire.json` is the single source for the À propos block.
     *
     * It previously wrote a parallel `about_history_*` key set while
     * `mission_valeurs.json` wrote `about_*`; the page read the former and
     * the admin edited the latter, so admin edits had no effect. Both now
     * collapse onto `about_*` — see the
     * `consolidate_about_history_settings` migration.
     */
    private function seedAboutSettings(array $historyData): void
    {
        foreach (self::LOCALES as $locale) {
            $story = $this->joinLines($historyData[$locale]['story'] ?? null, "\n\n");

            $this->put("about_story_{$locale}", $story);
            $this->put("about_mission_{$locale}", $historyData[$locale]['mission'] ?? null);
            $this->put("about_vision_{$locale}", $historyData[$locale]['vision'] ?? null);
            $this->put("about_values_{$locale}", $this->joinLines($historyData[$locale]['valeurs'] ?? null));
        }
    }

    private function seedExpatServiceSettings(array $expatServiceData): void
    {
        foreach (self::LOCALES as $locale) {
            $this->put("expat_service_title_{$locale}", $expatServiceData[$locale]['title'] ?? null, 'text');
            $this->put("expat_service_intro_{$locale}", $expatServiceData[$locale]['intro'] ?? null);
            $this->put("expat_service_features_{$locale}", $this->joinLines($expatServiceData[$locale]['features'] ?? null));
        }
    }

    /**
     * @param  array<int, mixed>|string|null  $value
     */
    private function joinLines(array|string|null $value, string $glue = "\n"): ?string
    {
        if (is_array($value)) {
            $value = implode($glue, array_values(array_filter(
                $value,
                static fn ($line) => is_string($line) && trim($line) !== ''
            )));
        }

        return is_string($value) && trim($value) !== '' ? $value : null;
    }

    private function put(string $key, ?string $value, string $type = 'textarea'): void
    {
        if ($value === null) {
            return;
        }

        SiteSetting::firstOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => 'about']
        );
    }
}

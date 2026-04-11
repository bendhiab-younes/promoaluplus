<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settingsData = $this->readJson(database_path('seeders/site_settings.json'));
        $missionData = $this->readJson(base_path('../content_docs/json/mission_valeurs.json'));
        $historyData = $this->readJson(base_path('../content_docs/json/notre_histoire.json'));
        $expatServiceData = $this->readJson(base_path('../content_docs/json/service_tunisiens_etranger.json'));

        foreach (($settingsData['settings'] ?? []) as $setting) {
            if (! is_array($setting) || empty($setting['key'])) {
                continue;
            }

            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'] ?? null,
                    'type' => $setting['type'] ?? 'text',
                    'group' => $setting['group'] ?? 'general',
                ]
            );
        }

        $this->seedHistorySettings($historyData);
        $this->seedMissionSettings($missionData);
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

    private function seedHistorySettings(array $historyData): void
    {
        foreach (['fr', 'en', 'ar'] as $locale) {
            $story = $historyData[$locale]['story'] ?? null;
            $mission = $historyData[$locale]['mission'] ?? null;
            $vision = $historyData[$locale]['vision'] ?? null;
            $values = $historyData[$locale]['valeurs'] ?? null;

            if (is_array($story)) {
                $story = implode("\n\n", array_values(array_filter($story, static fn ($line) => is_string($line) && trim($line) !== '')));
            }

            if (is_array($values)) {
                $values = implode("\n", array_values(array_filter($values, static fn ($value) => is_string($value) && trim($value) !== '')));
            }

            if (! is_string($story) || trim($story) === '') {
                $story = null;
            }

            if (is_string($story)) {
                SiteSetting::updateOrCreate(
                    ['key' => "about_story_{$locale}"],
                    ['value' => $story, 'type' => 'textarea', 'group' => 'about']
                );
            }

            if (is_string($mission) && trim($mission) !== '') {
                SiteSetting::updateOrCreate(
                    ['key' => "about_history_mission_{$locale}"],
                    ['value' => $mission, 'type' => 'textarea', 'group' => 'about']
                );
            }

            if (is_string($vision) && trim($vision) !== '') {
                SiteSetting::updateOrCreate(
                    ['key' => "about_history_vision_{$locale}"],
                    ['value' => $vision, 'type' => 'textarea', 'group' => 'about']
                );
            }

            if (is_string($values) && trim($values) !== '') {
                SiteSetting::updateOrCreate(
                    ['key' => "about_history_values_{$locale}"],
                    ['value' => $values, 'type' => 'textarea', 'group' => 'about']
                );
            }
        }
    }

    private function seedMissionSettings(array $missionData): void
    {
        foreach (['fr', 'en', 'ar'] as $locale) {
            $mission = $missionData[$locale]['mission'] ?? null;
            $vision = $missionData[$locale]['vision'] ?? null;
            $values = $missionData[$locale]['valeurs'] ?? null;

            if (is_array($values)) {
                $values = implode("\n", array_values(array_filter($values, static fn ($value) => is_string($value) && trim($value) !== '')));
            }

            if (is_string($mission) && trim($mission) !== '') {
                SiteSetting::updateOrCreate(
                    ['key' => "about_mission_{$locale}"],
                    ['value' => $mission, 'type' => 'textarea', 'group' => 'about']
                );
            }

            if (is_string($vision) && trim($vision) !== '') {
                SiteSetting::updateOrCreate(
                    ['key' => "about_vision_{$locale}"],
                    ['value' => $vision, 'type' => 'textarea', 'group' => 'about']
                );
            }

            if (is_string($values) && trim($values) !== '') {
                SiteSetting::updateOrCreate(
                    ['key' => "about_values_{$locale}"],
                    ['value' => $values, 'type' => 'textarea', 'group' => 'about']
                );
            }
        }
    }

    private function seedExpatServiceSettings(array $expatServiceData): void
    {
        foreach (['fr', 'en', 'ar'] as $locale) {
            $title = $expatServiceData[$locale]['title'] ?? null;
            $intro = $expatServiceData[$locale]['intro'] ?? null;
            $features = $expatServiceData[$locale]['features'] ?? null;

            if (is_array($features)) {
                $features = implode("\n", array_values(array_filter($features, static fn ($value) => is_string($value) && trim($value) !== '')));
            }

            if (is_string($title) && trim($title) !== '') {
                SiteSetting::updateOrCreate(
                    ['key' => "expat_service_title_{$locale}"],
                    ['value' => $title, 'type' => 'text', 'group' => 'about']
                );
            }

            if (is_string($intro) && trim($intro) !== '') {
                SiteSetting::updateOrCreate(
                    ['key' => "expat_service_intro_{$locale}"],
                    ['value' => $intro, 'type' => 'textarea', 'group' => 'about']
                );
            }

            if (is_string($features) && trim($features) !== '') {
                SiteSetting::updateOrCreate(
                    ['key' => "expat_service_features_{$locale}"],
                    ['value' => $features, 'type' => 'textarea', 'group' => 'about']
                );
            }
        }
    }
}

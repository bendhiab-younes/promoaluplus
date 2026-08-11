<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Mission / Vision / Valeurs used to live under two competing key sets:
 * `about_history_*` (seeded from content_docs/json/notre_histoire.json) and
 * `about_*` (seeded from mission_valeurs.json). The About page read the
 * history keys first, but the admin panel only edited the `about_*` ones —
 * so saving "Notre mission" in Paramètres du site changed nothing on the page.
 *
 * This collapses both onto `about_*`, keeping the text that was actually
 * visible (the history one) so the public site does not change appearance.
 */
return new class extends Migration
{
    private const FIELDS = ['mission', 'vision', 'values'];

    private const LOCALES = ['fr', 'en', 'ar'];

    public function up(): void
    {
        foreach (self::FIELDS as $field) {
            foreach (self::LOCALES as $locale) {
                $legacyKey = "about_history_{$field}_{$locale}";
                $legacy = DB::table('site_settings')->where('key', $legacyKey)->first();

                if ($legacy === null) {
                    continue;
                }

                if (filled($legacy->value)) {
                    $targetKey = "about_{$field}_{$locale}";
                    $target = DB::table('site_settings')->where('key', $targetKey);

                    if ($target->exists()) {
                        $target->update([
                            'value' => $legacy->value,
                            'type' => 'textarea',
                            'group' => 'about',
                            'updated_at' => now(),
                        ]);
                    } else {
                        DB::table('site_settings')->insert([
                            'key' => $targetKey,
                            'value' => $legacy->value,
                            'type' => 'textarea',
                            'group' => 'about',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                DB::table('site_settings')->where('key', $legacyKey)->delete();

                // SiteSetting::get() caches forever, so rewriting the row is not
                // enough — without this the About page keeps serving the
                // pre-migration text until the cache is cleared by hand.
                Cache::forget("site_setting_{$legacyKey}");
                Cache::forget("site_setting_about_{$field}_{$locale}");
            }
        }
    }

    /**
     * Irreversible: the pre-migration `about_*` values are overwritten above
     * and were never displayed anywhere, so there is nothing worth restoring.
     */
    public function down(): void
    {
        //
    }
};

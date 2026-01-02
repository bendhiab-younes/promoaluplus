<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("site_setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value, string $type = 'text', string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
        Cache::forget("site_setting_{$key}");
    }

    public static function getByGroup(string $group): array
    {
        return static::where('group', $group)->pluck('value', 'key')->toArray();
    }

    /**
     * Get a translated setting based on current locale
     * Looks for {key}_fr, {key}_en, {key}_ar based on app locale
     */
    public static function getTranslated(string $key, $default = null)
    {
        $locale = app()->getLocale();
        $translatedKey = "{$key}_{$locale}";
        
        $value = static::get($translatedKey);
        
        // Fallback to French if translation not found
        if (empty($value) && $locale !== 'fr') {
            $value = static::get("{$key}_fr");
        }
        
        return $value ?: $default;
    }

    protected static function booted(): void
    {
        static::saved(function ($setting) {
            Cache::forget("site_setting_{$setting->key}");
        });

        static::deleted(function ($setting) {
            Cache::forget("site_setting_{$setting->key}");
        });
    }
}

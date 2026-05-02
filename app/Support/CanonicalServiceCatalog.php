<?php

namespace App\Support;

class CanonicalServiceCatalog
{
    /**
     * Canonical service ordering shared across homepage/footer/contact/quotes.
     *
     * @var array<string, string>
     */
    public const MESSAGE_KEYS_BY_SLUG = [
        'kitchen' => 'kitchen',
        'doors' => 'doors',
        'windows' => 'windows',
        'rolling_shutters' => 'rolling_shutters',
        'railings' => 'railings',
        'pergola' => 'pergola',
        'sun_breakers' => 'sun_breakers',
        'mosquito_nets' => 'mosquito_nets',
        'space_design' => 'space_design',
    ];

    public static function slugs(): array
    {
        return array_keys(self::MESSAGE_KEYS_BY_SLUG);
    }

    public static function validationRule(): string
    {
        return 'required|string|in:' . implode(',', self::slugs());
    }

    public static function translatedOptions(?string $locale = null): array
    {
        $options = [];
        $translator = app('translator');

        foreach (self::MESSAGE_KEYS_BY_SLUG as $slug => $messageKey) {
            $translationKey = 'messages.' . $messageKey;
            $label = $locale === null
                ? __($translationKey)
                : $translator->get($translationKey, [], $locale);

            $options[$slug] = is_string($label) ? $label : ucwords(str_replace('_', ' ', $slug));
        }

        return $options;
    }

    public static function labelFor(string $slug, ?string $locale = null): string
    {
        $options = self::translatedOptions($locale);

        return $options[$slug] ?? ucwords(str_replace('_', ' ', $slug));
    }
}

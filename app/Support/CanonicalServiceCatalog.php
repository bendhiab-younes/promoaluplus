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

    /**
     * Catch-all slug for quote requests that don't fit any canonical service.
     * Not part of the homepage/footer service list — quote-form/admin only.
     */
    public const OTHER_SLUG = 'other';

    public static function slugs(): array
    {
        return array_keys(self::MESSAGE_KEYS_BY_SLUG);
    }

    public static function validationRule(): string
    {
        return 'required|string|in:'.implode(',', self::slugs());
    }

    public static function quoteValidationRule(): string
    {
        return 'required|string|in:'.implode(',', [...self::slugs(), self::OTHER_SLUG]);
    }

    /**
     * Per-item rule for a multi-select project_types array — the "required"
     * lives on the array itself (at least one type), not on each element.
     */
    public static function quoteItemValidationRule(): string
    {
        return 'string|in:'.implode(',', [...self::slugs(), self::OTHER_SLUG]);
    }

    public static function translatedOptions(?string $locale = null): array
    {
        $options = [];
        $translator = app('translator');

        foreach (self::MESSAGE_KEYS_BY_SLUG as $slug => $messageKey) {
            $translationKey = 'messages.'.$messageKey;
            $label = $locale === null
                ? __($translationKey)
                : $translator->get($translationKey, [], $locale);

            $options[$slug] = is_string($label) ? $label : ucwords(str_replace('_', ' ', $slug));
        }

        return $options;
    }

    /**
     * Same as translatedOptions() plus a trailing "Other" option, for the
     * quote form and admin quote UI where a project may not match any
     * canonical service.
     */
    public static function quoteOptions(?string $locale = null): array
    {
        $options = self::translatedOptions($locale);
        $options[self::OTHER_SLUG] = self::otherLabel($locale);

        return $options;
    }

    public static function labelFor(string $slug, ?string $locale = null): string
    {
        if ($slug === self::OTHER_SLUG) {
            return self::otherLabel($locale);
        }

        $options = self::translatedOptions($locale);

        return $options[$slug] ?? ucwords(str_replace('_', ' ', $slug));
    }

    private static function otherLabel(?string $locale = null): string
    {
        $translator = app('translator');
        $label = $locale === null
            ? __('messages.other')
            : $translator->get('messages.other', [], $locale);

        return is_string($label) ? $label : 'Other';
    }
}

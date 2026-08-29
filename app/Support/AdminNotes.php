<?php

namespace App\Support;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\ChatbotFlowResource;
use App\Filament\Resources\FaqResource;
use App\Filament\Resources\HeroSlideResource;
use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectTypeResource;
use App\Filament\Resources\QuoteResource;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\TestimonialResource;
use Filament\Pages\Dashboard;

/**
 * The "À propos de cette page" note shown at the top of each admin section.
 *
 * Keyed by the Filament page class rather than the resource, so only the
 * landing page of a section carries a note — a create or edit screen inherits
 * nothing and stays uncluttered. The text itself lives in
 * lang/{fr,ar}/admin_notes.php.
 */
class AdminNotes
{
    /**
     * Page class => translation key. A page absent from this map shows no note.
     *
     * @var array<class-string, string>
     */
    public const PAGES = [
        Dashboard::class => 'dashboard',
        QuoteResource\Pages\ListQuotes::class => 'quotes',
        InvoiceResource\Pages\ListInvoices::class => 'invoices',
        ServiceResource\Pages\ListServices::class => 'services',
        ProjectResource\Pages\ListProjects::class => 'projects',
        ProjectTypeResource\Pages\ListProjectTypes::class => 'project_types',
        TestimonialResource\Pages\ListTestimonials::class => 'testimonials',
        FaqResource\Pages\ListFaqs::class => 'faqs',
        HeroSlideResource\Pages\ListHeroSlides::class => 'hero_slides',
        ChatbotFlowResource\Pages\ListChatbotFlows::class => 'chatbot_flows',
        SiteSettings::class => 'settings',
    ];

    /**
     * The languages an admin can read the notes in, in toggle order.
     *
     * @var array<string, string>
     */
    public const LOCALES = [
        'fr' => 'FR',
        'ar' => 'ع',
    ];

    public const DEFAULT_LOCALE = 'fr';

    /**
     * Filament hands a page's render hooks every scope it declares — the page
     * class first, then the resource. Only the page class is matched, which is
     * what keeps Edit/Create pages noteless.
     *
     * @param  array<int, string>  $scopes
     */
    public static function keyFor(array $scopes): ?string
    {
        foreach ($scopes as $scope) {
            if (isset(self::PAGES[$scope])) {
                return self::PAGES[$scope];
            }
        }

        return null;
    }

    public static function isSupportedLocale(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, self::LOCALES);
    }

    public static function normalizeLocale(?string $locale): string
    {
        return self::isSupportedLocale($locale) ? $locale : self::DEFAULT_LOCALE;
    }

    public static function heading(string $key, string $locale): string
    {
        return __("admin_notes.{$key}.heading", [], self::normalizeLocale($locale));
    }

    /**
     * The body, as the paragraphs it should be rendered in.
     *
     * @return array<int, string>
     */
    public static function body(string $key, string $locale): array
    {
        $body = __("admin_notes.{$key}.body", [], self::normalizeLocale($locale));

        return is_array($body) ? array_values($body) : [];
    }

    public static function isRightToLeft(string $locale): bool
    {
        return $locale === 'ar';
    }
}

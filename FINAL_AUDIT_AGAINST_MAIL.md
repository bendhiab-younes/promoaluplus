# Final audit against client mail

Date: 2026-05-17

## Accueil - 1st dot

| Request | Status | Code evidence |
|---|---|---|
| Replace `PROMO ALU PLUS` with `PromoAlu+` | Fixed (UI) | Hero badge normalization exists: `resources/views/pages/home.blade.php:5-8`. Layout branding uses `PromoAlu+`: `resources/views/layouts/app.blade.php:588,647`. Updated FR/EN/AR site description and WhatsApp messages to `PromoAlu+`: `lang/fr/messages.php:5,317`, `lang/en/messages.php:5,319`, `lang/ar/messages.php:5,319`. |
| Reduce dot size | Fixed | `resources/views/pages/home.blade.php:168-171` (`w-2 h-2 md:w-2.5 md:h-2.5`). |
| Add wording after cuisines and remove `et` before cuisines | Fixed | Updated hero sentence: `lang/fr/messages.php:19` now uses `... garde-corps, cuisines et divers autres travaux en aluminium ...`. |
| White bar overlaps contact/WhatsApp buttons | Fixed | CTA buttons at `z-30`: `resources/views/pages/home.blade.php:42`; dots lowered: `:167`. |
| Replace non-Tunisian hero background image | Not fixed | Hero still uses Unsplash images: `resources/views/pages/home.blade.php:21,61,94,127`. |
| Under `NOS SERVICES`, replace `votre maison` with `vos espaces résidentiels et professionnels` | Fixed (FR) | `lang/fr/messages.php:40`, rendered in `resources/views/pages/home.blade.php:388`. |
| Footer services list must match top services order | Fixed | Canonical ordering is implemented via `CanonicalServiceCatalog`: `app/Support/CanonicalServiceCatalog.php:12-22`, used in footer `resources/views/layouts/app.blade.php:626,668`. FR labels aligned to requested forms in `lang/fr/messages.php` (`kitchen`, `windows`, `rolling_shutters`, `railings`, `mosquito_nets`, `space_design`). |
| Footer contact city Tunis -> Sousse | Fixed (fallback) | `lang/fr/messages.php:190` (`Zone Industrielle, Sousse, Tunisie`), used by footer fallback: `resources/views/layouts/app.blade.php:637`. |
| Footer phone and WhatsApp set correctly | Fixed | Footer reads configured phone/WhatsApp: `resources/views/layouts/app.blade.php:627-629,683,689`. |

## Accueil - 4th dot

| Request | Status | Code evidence |
|---|---|---|
| Dot 4 becomes dot 2, dot 2 becomes dot 3, dot 3 becomes dot 4 (ordering issue) | Fixed | `data-order` mapping is now sequential `0,1,2,3` in `resources/views/pages/home.blade.php:19,59,92,125`, while sorting still occurs in `:187-193`. |
| Replace `500+ Projets réalisés` with `Des centaines de projets réalisés` | Fixed | `resources/views/pages/home.blade.php:142` uses `messages.hundreds_projects_completed`; value in `lang/fr/messages.php:252`. |

## Services page

| Request | Status | Code evidence |
|---|---|---|
| Remove non-Tunisian photos and photos with other company logos | Not fixed | Services page uses DB gallery content and fallback image logic; no content-quality filter exists: `resources/views/pages/services.blade.php:143`, `app/Models/Service.php:114-123`. |

## Réalisations page

| Request | Status | Code evidence |
|---|---|---|
| Replace intro sentence with requested wording | Fixed | `lang/fr/messages.php:233`, rendered in `resources/views/pages/portfolio.blade.php:15`. |
| Replace `Témoignages Clients` with `Ils nous ont fait confiance` | Fixed | `lang/fr/messages.php:235`, rendered in `resources/views/pages/portfolio.blade.php:112`. |
| Use real project photos | Not fixed | Unsplash fallbacks still present: `resources/views/pages/portfolio.blade.php:47,63,77,91`. |

## A propos de nous

| Request | Status | Code evidence |
|---|---|---|
| Replace 15-year intro sentence | Fixed | `lang/fr/messages.php:242`, rendered in `resources/views/pages/about.blade.php:74`. |
| Replace `Service pensé spécialement ...` sentence | Fixed | `lang/fr/messages.php:282`, rendered in `resources/views/pages/about.blade.php:195`. |
| Replace long `Nous comprenons les défis ...` sentence | Fixed | Added explicit rendered sentence via `messages.expats_context` and displayed in `resources/views/pages/about.blade.php` (new paragraph under expat intro). FR key in `lang/fr/messages.php` under expat section. |
| Replace `C'est pourquoi nous avons développé ...` sentence | Fixed | Added explicit rendered sentence via `messages.expats_commitment` and displayed in `resources/views/pages/about.blade.php` (new paragraph under expat intro). FR key in `lang/fr/messages.php` under expat section. |

## Contact page

| Request | Status | Code evidence |
|---|---|---|
| Remove small duplicate `Contactez-nous` with phone icon | Fixed | Contact header has title + intro only: `resources/views/pages/contact.blade.php:25-31`. |
| Set real Sousse address | Fixed (fallback + setting) | Address uses SiteSetting with Sousse fallback: `resources/views/pages/contact.blade.php:17`, `lang/fr/messages.php:190`. |
| Correct phone and WhatsApp icon/number | Fixed | Contact cards show phone and WhatsApp values: `resources/views/pages/contact.blade.php:44,68`. |
| Remove `budget estimé` subtitle under quote request | Fixed | No budget subtitle/field in form section; heading is `request_free_quote`: `resources/views/pages/contact.blade.php:95`. |
| Add first name field | Fixed | `resources/views/pages/contact.blade.php:123-124`. |
| Project type list must match home services list | Fixed | Uses canonical options: `resources/views/pages/contact.blade.php:18,190`; source `app/Support/CanonicalServiceCatalog.php:12-22`. |
| FAQ Q2/Q2A wording about residents abroad | Fixed | Translation keys remain correct (`lang/fr/messages.php:226-227`) and contact page now force-syncs FAQ item `sort_order = 2` with these translations at runtime: `app/Http/Controllers/PageController.php:50-69`. |

## Additional confirmed fixes performed

- Hero overflow into next section fixed by dynamic viewport minus header height: `resources/views/pages/home.blade.php:14,178-185`.
- Contact defaults updated to:
  - Phone: `+21626192898`
  - WhatsApp: `+21626192898`
  - Email: `promoaluplus@gmail.com`
  - Seen in: `resources/views/layouts/app.blade.php:627-629`, `resources/views/pages/contact.blade.php:7-8,16`, `resources/views/emails/quote-received.blade.php:44-46`, `app/Http/Controllers/PdfController.php:16-17`, `resources/views/components/chatbot.blade.php:179`.

## Remaining high-priority gaps

1. Replace hero/services/portfolio non-Tunisian placeholders with approved real assets.

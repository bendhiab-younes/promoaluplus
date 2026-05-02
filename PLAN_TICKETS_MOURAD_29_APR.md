# Plan — Tickets Mourad (29 Apr)

## Problem and approach
Apply all tickets in `TICKETS_MOURAD_29_APR.md` across homepage, services, portfolio, about, and contact, while keeping content consistent in shared surfaces (footer, chatbot, emails, admin) and preserving multilingual behavior.

Approach:
1. Handle shared data first (canonical service list + final contact details + approved assets).
2. Implement page-level tickets in focused batches.
3. Sync all duplicated contact/copy touchpoints.
4. Run formatting/tests at the end.

## Workstreams and file mapping

### 1) Shared prerequisites
- **Collect required business inputs/assets**: final Sousse address, phone, WhatsApp number, and replacement visuals.
- **Define one canonical service list** matching ticket wording for reuse in footer + contact form + quote validation.
- Likely files:
  - `resources\views\layouts\app.blade.php`
  - `resources\views\pages\contact.blade.php`
  - `app\Http\Controllers\QuoteController.php`
  - `app\Filament\Resources\QuoteResource.php`
  - `lang\fr\messages.php` (+ `lang\en\messages.php`, `lang\ar\messages.php` for parity)

### 2) Homepage tickets (T-1.1 → T-1.11)
- Carousel:
  - badge/text/photo replacements, slide reordering, "Des centaines de projets réalisés".
  - reduce dot size and fix overlap hiding contact/WhatsApp actions.
- Services intro line and footer services/contact updates.
- Likely files:
  - `resources\views\pages\home.blade.php`
  - `resources\views\layouts\app.blade.php`
  - `lang\fr\messages.php` (+ EN/AR parity where needed)
  - optional `app\Models\SiteSetting.php` usage alignment if content is setting-driven.

### 3) Services page media cleanup (T-2.1, T-2.2)
- Replace incompatible/non-local/logoed images with approved real or Tunisia-realistic images.
- Likely files/data sources:
  - `resources\views\pages\services.blade.php`
  - service content source used by `database\seeders\ServiceSeeder.php` (`../content_docs/json/services.json`) or admin-managed records.

### 4) Portfolio page updates (T-3.1, T-3.2, T-3.3)
- Replace intro text, testimonial section title, and project imagery.
- Likely files:
  - `resources\views\pages\portfolio.blade.php`
  - `lang\fr\messages.php` (+ EN/AR parity)
  - project records/fallback images (seed/admin content).

### 5) About page copy updates (T-4.1 → T-4.4)
- Update intro and "résidents à l'étranger" messaging.
- Keep consistency between translation fallbacks and SiteSettings-managed text.
- Likely files/data:
  - `resources\views\pages\about.blade.php`
  - `lang\fr\messages.php` (+ EN/AR parity)
  - `database\seeders\SiteSettingsSeeder.php` + about-related content JSON (if seeded).

### 6) Contact page + quote form updates (T-5.1 → T-5.6)
- Remove redundant mini "Contactez-nous" label.
- Update real Sousse address and contact channels.
- Remove "budget estimé" subtitle line under quote form heading.
- Add **prénom** field to quote form and persist through validation/model/admin/email surfaces.
- Align "Type du projet" options to canonical homepage service list.
- Likely files:
  - `resources\views\pages\contact.blade.php`
  - `app\Http\Controllers\QuoteController.php`
  - `app\Models\Quote.php`
  - `database\migrations\*quotes*.php` (new migration if new DB field is required)
  - `app\Filament\Resources\QuoteResource.php`
  - `resources\views\emails\quote-received.blade.php`
  - `resources\views\emails\quote-notification.blade.php`

### 7) FAQ update (T-5.7, T-5.8)
- Update FAQ question/answer wording to "résidents à l'étranger".
- Data source is DB-backed FAQ (`Faq` model), so update should target active FAQ content (seed/admin).
- Likely files/data:
  - FAQ records via Filament (`app\Filament\Resources\FaqResource.php`) or seed source (`database\seeders\FaqSeeder.php` + external JSON file).

### 8) Cross-surface contact consistency
- Ensure updated contact details are aligned in:
  - footer,
  - contact page cards,
  - WhatsApp float action in layout script,
  - chatbot defaults/flows,
  - quote emails,
  - PDF contact block defaults.
- Likely files:
  - `resources\views\layouts\app.blade.php`
  - `resources\views\components\chatbot.blade.php`
  - `database\seeders\ChatbotFlowSeeder.php`
  - `app\Http\Controllers\PdfController.php`
  - email blades listed above.

## Design-related tickets (impeccable usage)
- Tickets with UI/visual scope: **T-1.2, T-1.4, T-1.5, T-2.1, T-2.2, T-3.3**.
- During implementation, use impeccable-style passes to:
  1. refine spacing/stacking around floating controls and carousel bullets,
  2. keep visual hierarchy and readability after image swaps,
  3. preserve mobile/RTL behavior.

## Execution order
1. Shared prerequisites (assets + canonical services + contact data).
2. Homepage batch.
3. Services/portfolio/about content batches.
4. Contact/quote-form schema + FAQ.
5. Cross-surface consistency sweep.
6. Formatting/tests and final verification.

## Progress (as of 01 May 2026)

### Completed ✅
| Workstream | Status | Files |
|------------|--------|-------|
| **1. Shared prerequisites** | Partial | `app.blade.php`, `contact.blade.php` |
| **2. Homepage (T-1.1 → T-1.11)** | ✅ Done (including T-1.6, T-1.8 fixes) | `home.blade.php`, `app.blade.php`, `lang/*/messages.php` |
| **3. Services page media (T-2.1, T-2.2)** | ✅ Done | `content_docs/json/services.json` |
| **4. Portfolio page (T-3.1, T-3.2, T-3.3)** | ✅ Done | `portfolio.blade.php`, `lang/*/messages.php` |
| **5. About page (T-4.1 → T-4.4)** | ✅ Done | `lang/*/messages.php`, `content_docs/json/service_tunisiens_etranger.json` |
| **8. Chatbot flow updates** | ✅ Done | `ChatbotFlowSeeder.php` |
| **6. Contact/Quote form (T-5.1 → T-5.6)** | ✅ Done (including T-5.2 address fix) | `contact.blade.php`, `QuoteController.php`, `Quote.php`, `QuoteResource.php`, migration `2026_05_01_160500_add_first_name_to_quotes_table.php`, `lang/*/messages.php` |
| **7. FAQ (T-5.7, T-5.8)** | ✅ Done | `FaqSeeder.php` |
| **8. Cross-surface** | Partial | `app.blade.php`, `PdfController.php` |

### Remaining 🔲
- ~~Services page media (T-2.1, T-2.2)~~ ✅ Done (updated content_docs/json/services.json)
- ~~Portfolio page (T-3.1, T-3.2, T-3.3)~~ ✅ Done (updated translations, fallback images)
- ~~About page (T-4.1 → T-4.4)~~ ✅ Done (updated lang/*/messages.php, content_docs/json/service_tunisiens_etranger.json)
- ~~Chatbot flow updates~~ ✅ Done (updated ChatbotFlowSeeder.php with real phone/WhatsApp)

---

## Notes / risks
- Some seeders read external files (`../content_docs/json/...`), so content updates may require either:
  - updating those source files, or
  - applying changes directly via DB/admin content management.
- The "prénom" ticket needs a product decision: add `first_name` alongside existing `name`, or split existing `name` semantics. Plan assumes adding explicit `first_name` while preserving backward compatibility.

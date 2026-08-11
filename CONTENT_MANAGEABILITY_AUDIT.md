# Content manageability audit — Promo Alu Plus

**Date:** 2026-08-11
**Branch:** `feat/admin-manageable-content`
**Question audited:** is every piece of content on the public site dynamic and editable from `/admin`, with no hardcoded content left?

**Verdict:** the *collection* content (services, projects, testimonials, FAQs, hero slides, chatbot flows) is fully DB-driven and admin-managed. The **Paramètres du site** page is the problem: **28 of its 44 fields do nothing**, and several visible blocks have no admin field at all.

All findings below were verified against the code and, where noted, against live database state.

## Status

| Batch | Items | State |
|---|---|---|
| Fix tier | 1, 2, 12, 14, 15 | ✅ done |
| Delete tier | 3, 6, 7, 9, 10 | ✅ done — fields removed from the admin |
| Deferred | 4, 5, 8, 11, 13 | open — new features, your call |
| Won't fix | 16, 17, 18, 19, 20, 21 | open — low value, see notes |

Two defects found while fixing and also resolved:

- `SiteSetting::get()` uses `Cache::rememberForever`, so any change written outside `SiteSetting::set()` (a migration, a raw query) stays invisible until the cache is cleared. The consolidation migration now forgets the keys it touches.
- `SiteSettingsSeeder` used `updateOrCreate` throughout, so **every reseed silently reverted admin edits** to `about_story`, mission, values and the expat block. Now `firstOrCreate`, matching `HeroSlideSeeder`.

---

## 1. Broken promises — the field exists, saving succeeds, the page never changes

### 1a. Shadowed — the About page reads a *different* key than the one you edit ⚠️ worst case

`resources/views/pages/about.blade.php:25-37` reads `about_history_*` **first**, and only falls back to `about_*` if that is empty. Those rows exist (9 of them, seeded by `SiteSettingsSeeder`), so the fallback never fires.

Proven live via tinker:

```
about_history_mission_fr → "Notre mission est de garantir qualité, sécurité…"   ← what the page shows
about_mission_fr         → "Offrir à nos clients une expérience sereine…"      ← what the admin edits
```

| # | Admin field (`app/Filament/Pages/SiteSettings.php`) | Effect |
|---|---|---|
| 1 ✅ | **Notre mission** fr/en/ar (`:151-159`) | none — shadowed by `about_history_mission_*` |
| 2 ✅ | **Nos valeurs** fr/en/ar (`:163-171`) | none — shadowed by `about_history_values_*` |

### 1b. Write-only — no code anywhere reads these keys

| # | Tab | Fields | Where it should appear |
|---|---|---|---|
| 3 ✅ | **Horaires** (`:211-219`) | `hours_weekdays`, `hours_saturday`, `hours_sunday` | nowhere. Contact page shows `__('messages.working_hours')` from the lang file (`contact.blade.php:52`) |
| 4 | **Réseaux sociaux** (`:226-245`) | `social_facebook/instagram/linkedin/youtube/tiktok` | nowhere. The footer has **no social icons at all** |
| 5 | **SEO** (`:255-283`) | `seo_title_*`, `seo_description_*`, `seo_keywords` (7 fields) | nowhere. Meta tags come from `__('messages.seo_title_about')` etc. per page, and `layouts/app.blade.php:7` hardcodes the keywords list |
| 6 ✅ | **Statistiques** (`:84-102`) | `stats_projects`, `stats_satisfaction`, `stats_team` | nowhere — the stats counter block no longer exists on the homepage |
| 7 ✅ | Statistiques → `stats_years` (`:89`) | read **only** by `database/seeders/HeroSlideSeeder.php` at seed time | changing it does **not** update slide 4's already-saved title. Seed-time only |
| 8 | Entreprise → **Logo** (`:63`) | `company_logo` | nowhere. Header/footer hardcode `asset('images/logo-160.webp')` (`app.blade.php:192`); PDFs hardcode `public/images/promo-alu-plus-logo.png` (`app/Support/DevisDocument.php:160`) |
| 9 ✅ | Contact → **Lien Google Maps** (`:201`) | `contact_map_url` | nowhere — there is no map embed on the contact page |
| 10 ✅ | Contact → **Téléphone secondaire** (`:184`) | `contact_phone_2` | nowhere |

### 1c. Partially wired

| # | Field | Effect |
|---|---|---|
| 11 | **Section CTA** (`cta_title_*`, `cta_description_*`) | Honored on the homepage only (`home.blade.php:662,665`). The identical CTA blocks on **Services** (`services.blade.php:405,408`), **Réalisations** (`portfolio.blade.php` CTA section) and **À propos** (`about.blade.php:314,315`) ignore it and use the lang file |

**Working correctly:** `company_name`, `company_tax_id`, `contact_phone`, `contact_whatsapp`, `contact_email`, `contact_address`, `about_story_*`, `portfolio_enabled`. The devis PDF "Prestataire" block does honor its helper-text promise (`app/Support/DevisDocument.php:33-38`).

---

## 2. Gaps — visible content with no admin field anywhere

| # | Content | Location |
|---|---|---|
| 12 ✅ | **Notre vision** text | `about.blade.php:29-32` — reads `about_history_vision` / `about_vision`; **both are seeded, neither has an admin field**. This paragraph is unchangeable from the admin |
| 13 | **"Pourquoi les expatriés nous choisissent"** — title, intro, and the 3 feature cards | `about.blade.php:43-60`, keys `expat_service_title` / `expat_service_intro` / `expat_service_features` — seeded into the DB, no admin field |
| 14 ✅ | **Workshop photo on À propos** | `about.blade.php:95` — hardcoded **Unsplash URL** (`images.unsplash.com/photo-1504307651254…`). External stock photo, not an image of the company, not replaceable from admin |
| 15 ✅ | **Footer service list** | `app.blade.php:144` uses `CanonicalServiceCatalog::translatedOptions()` — a hardcoded 9-slug PHP const plus lang labels. A service you **add, rename, reorder or deactivate in admin never affects the footer** |
| 16 | **Brand name "PromoAlu+"** | Hardcoded 13× in views despite `company_name` being editable: `app.blade.php:8,12,15,16,25,87,160,193,248`, `services.blade.php:87`, `components/chatbot.blade.php:14`, `emails/quote-received.blade.php:55` |
| 17 | **Chatbot fallback strings** | `app/Http/Controllers/ChatbotController.php:174+` — `getLocalizedText()` is a hardcoded PHP array (welcome message, "Je n'ai pas compris…", "← Menu principal", etc.) in all 3 locales. Only fires when no matching `ChatbotFlow` exists, but it is unreachable from admin |
| 18 | **FAQ chatbot keywords** | `ChatbotController.php:157` reads `$faq->keywords` — the **`faqs` table has no `keywords` column** and `FaqResource` has no field. Keyword search is permanently inert |

> **Note on #15 (accepted limitation).** The footer now reads from the DB, but the
> quote form dropdown and quote validation still use `CanonicalServiceCatalog`
> (`contact.blade.php:18`, `CanonicalServiceCatalog::quoteValidationRule()`), because
> validation needs a fixed allowlist. So a service added in the admin appears on the
> services page and in the footer but is not a selectable option when a customer
> requests a quote. **Accepted:** the quote form already offers an « Autre » option
> (`CanonicalServiceCatalog::OTHER_SLUG`), which covers those requests. Revisit only
> if the service list starts changing often.

---

## 3. Hidden hardcoded blocks — same pattern as the Réalisations page

These are commented out in Blade. Re-enabling requires a code change; there is no admin toggle, and their content is hardcoded.

| # | Block | Location |
|---|---|---|
| 19 | **Certifications** — ISO 9001, marquage CE, garantie 10 ans | `about.blade.php:262-299` |
| 20 | **"Pourquoi nous choisir"** — 4 advantage cards | `home.blade.php:595-644` |
| 21 | **"TOP Produits Aluminium"** carousel | `home.blade.php:472-545` |

---

## 4. Out of scope by earlier decision

Static UI strings in `lang/{fr,en,ar}/messages.php` — page intros (`about_intro`, `contact_intro`, `services_page_intro`, `portfolio_intro`), section headings, per-page SEO title/description, button labels, the address fallback. These were explicitly ruled out of scope, so they are listed as a known boundary, not a defect.

Note that items 3, 5 and 11 above are cases where an admin field *appears* to override a lang string but does not — those are real bugs regardless of this boundary.

Also not content: inline SVG icons, Tailwind colour maps (`$heroAccents`, `$valueIcons`, `$expatStyles`).

---

## 5. Dead code

`app/Filament/Resources/CategoryResource.php` is **0 bytes**, has no registered route, and `Category::` is referenced nowhere — the `Category` model and its migration are unused leftovers, superseded by `ProjectType`.

---

## 6. What is fully manageable today (for contrast)

| Content type | Admin location | Notes |
|---|---|---|
| Services | Contenu → Services | title, descriptions, icon, colour, image + gallery upload, features, materials, specs, order, active — all 3 locales |
| Hero slides | Contenu → Slides d'accueil | full CRUD, image upload with zoom/focal/fit framing, badge, highlight, CTA, alt text, order, active |
| Projects | Contenu → Projets | title, description, category, location, image + gallery, featured, order, active |
| Project types | Contenu → Types de projet | name (3 locales), slug, icon, colour, order, active — drives the Réalisations filter bar |
| Testimonials | Contenu → Témoignages | name, location, photo, quote (3 locales), rating, project type, order, active |
| FAQs | Contenu → FAQ | question + answer (3 locales), order, active |
| Chatbot flows | Contenu → Flux chatbot | trigger, keywords, message (3 locales), quick replies, actions |
| Réalisations page visibility | Paramètres → Pages & visibilité | toggling off returns 404 and hides all links |

---

---

## 7. Remaining work

Everything below is still open. None of it is a bug — each is a feature that does not exist yet.

| # | Item | Why it is still open |
|---|---|---|
| 4 | Social links in the footer | The footer has no social icons at all. Worth building only if the accounts exist |
| 5 | SEO tab | Per-page SEO currently lives in the lang files. Wiring the settings up means deciding whether they override per-page values or only fill the homepage |
| 8 | Logo upload | Header, footer and PDFs each hardcode a different asset; unifying them on `company_logo` needs a sensible fallback for all three |
| 11 | CTA applied site-wide | `cta_title` / `cta_description` are honored on the homepage only; Services, Réalisations and À propos use lang keys |
| 13 | Expat section fields | Text is seeded and editable in the DB but has no admin form |
| 16 | Brand name hardcoded 13× | Only matters on a rebrand |
| 17 | Chatbot fallback strings | Unreachable as long as the flows are configured |
| 18 | FAQ chatbot keywords | Needs a `keywords` column plus a form field |
| 19–21 | Hidden blocks (certifications, "Pourquoi nous choisir", "TOP Produits") | Leave commented until there is a decision to bring them back |

The dead `CategoryResource` / `Category` code described in §5 can be deleted at any time.

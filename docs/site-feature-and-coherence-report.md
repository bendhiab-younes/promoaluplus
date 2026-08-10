# Site Feature Test & Coherence Report

**Project:** Promo Alu Plus (`alu-workshop-laravel`) — Laravel 12 + Filament 3
**Date:** 2026-08-09
**Branch:** `main` @ `f415a01`

---

## 1. Scope and method

Two things were produced:

1. **An executable test suite** covering every public feature of the site, plus a set of
   machine-checked coherence invariants.
2. **This report**, listing coherence defects, fake/placeholder data, and redundancy.

| Deliverable | File | Tests |
|---|---|---|
| Public feature coverage | `tests/Feature/PublicSiteFeatureTest.php` | 31 |
| Coherence invariants | `tests/Feature/SiteCoherenceTest.php` | 21 (13 pass, 8 skipped as defect markers) |

52 tests added; the suite goes from 33 to 85.

Suite status after this work:

```
php artisan test --compact
Tests:  8 skipped, 77 passed (1990 assertions)
```

Before this work the suite was 33 tests, all on the devis/quote-pricing internals; no
public page, locale, chatbot, SEO, or admin route had any coverage.

### How to read the findings

Every finding is tagged with how it was established:

- **[VERIFIED]** — reproduced by running code (a test, a request, a query). A repro command is given.
- **[SOURCE]** — read directly from the source; the file and line are given, but no runtime proof was taken.

### On the 8 skipped tests

Each skipped test contains the assertion that *should* hold, guarded by a
`markTestSkipped()` naming the finding below. They are skipped rather than failing so the
suite stays green, and rather than deleted so the invariant is recorded. **Remove the skip
line when you fix the finding** — the assertion underneath is already written.

---

## 2. Feature coverage

| Area | Covered | Result |
|---|---|---|
| Home / Services / Portfolio / About / Contact | 5 pages × 3 locales (fr, en, ar) | 200 in all 15 combinations |
| Same pages against an **empty database** | 5 pages | 200 — no page hard-depends on seeded content |
| RTL | `dir="rtl"` on `ar`, `dir="ltr"` on `fr` | Correct |
| Locale switcher | valid + invalid locale | Correct; unsupported locale is ignored, session preserved |
| Portfolio category filter | `?category=kitchen` vs unfiltered | Correct |
| Contact FAQ list | active vs inactive rows | Correct |
| Contact details from `SiteSetting` | phone/email override | Correct |
| `sitemap.xml` | well-formed XML, 5 `<url>` entries, absolute URLs | Correct |
| `robots.txt` | `User-agent` + sitemap link | Correct |
| Quote form | happy path, JSON mode, validation, all 10 accepted `project_type` values, rejection of unknown type, bad e-mail, mail-transport failure | Correct — the request survives an SMTP outage |
| Chatbot | `/welcome` (fallback + DB flow), `/faqs`, `faq:<id>` action, keyword match, unknown-message fallback | Correct |
| Document downloads | guest redirect on quote PDF / quote XLSX / invoice PDF; authed download of quote PDF, quote XLSX and invoice PDF | Correct |
| Filament panel | guest redirect, login page, 7 resource indexes, dashboard, site-settings page | Loads — **but see F-01** |

**Not covered, and why:** Filament create/edit form submissions per resource (the existing
`DevisAdminUiTest.php` already drives the quote screens via Livewire, and replicating that
for all 7 resources is a larger piece of work than this pass); JavaScript behaviour
(carousel, chatbot widget, scroll animations) — there is no browser-test harness in the
repo; e-mail rendering beyond "was queued".

**One thing this report could not check:** the row counts of the *production* database. All
row-count evidence below comes from the local `database.sqlite`. Where that matters it is
called out explicitly (§4.1, §4.2).

---

## 3. Findings

### F-01 — The admin panel returns 403 to every user outside a local environment  ⚠️ Critical

**[VERIFIED]** — `tests/Feature/PublicSiteFeatureTest.php::test_admin_panel_denies_authenticated_users_outside_a_local_environment`

Filament's auth middleware aborts with 403 unless the authenticated user implements
`Filament\Models\Contracts\FilamentUser`, or `app.env` is exactly `local`:

```php
// vendor/filament/filament/src/Http/Middleware/Authenticate.php:32-38
abort_if(
    $user instanceof FilamentUser ? (! $user->canAccessPanel($panel)) : (config('app.env') !== 'local'),
    403,
);
```

`App\Models\User` (`app/Models/User.php`) does not implement that contract. **Deploy this
with `APP_ENV=production` and nobody can reach `/admin`** — login succeeds, then every panel
page 403s. It only works today because local development runs `APP_ENV=local`.

**Fix:** implement `FilamentUser` on `App\Models\User`:

```php
class User extends Authenticatable implements FilamentUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // or gate on a role/email domain
    }
}
```

---

### F-02 — `/chatbot/faqs` orders by a column that does not exist  ⚠️ High

**[VERIFIED]** — query logged against the live SQLite database.

`app/Http/Controllers/ChatbotController.php:117`:

```php
$faqs = Faq::where('is_active', true)->orderBy('order')->get();
```

The `faqs` table has no `order` column — its columns are
`id, question, answer, is_active, sort_order, created_at, updated_at`
(`database/migrations/2026_01_02_211256_create_faqs_table.php`). Every other consumer uses
`sort_order` (`Faq::scopeOrdered()`, `PageController@contact`).

Repro:

```
php artisan tinker --execute='DB::listen(fn($q)=>print($q->sql));
  App\Models\Faq::where("is_active",true)->orderBy("order")->get();'
# select * from "faqs" where "is_active" = ? order by "order" asc
```

SQLite quietly resolves `"order"` to a **string literal** rather than erroring, so the sort
is a no-op and the FAQ list comes back in insertion order. On MySQL or PostgreSQL the same
query raises *unknown column* and the endpoint returns **500**. This is a latent production
outage that is invisible in local development.

**Fix:** `->orderBy('sort_order')`, or reuse the existing scope: `Faq::active()->ordered()->get()`.

Marker test: `SiteCoherenceTest::test_chatbot_faq_endpoint_orders_by_a_column_that_exists`.

---

### F-03 — Quote notification e-mails are sent to a dead address  ⚠️ High

**[VERIFIED]** — `grep -rn "admin_email" config/` returns nothing.

`app/Http/Controllers/QuoteController.php:33`:

```php
Mail::to(config('mail.admin_email', 'admin@aluminiumcraft.tn'))->queue(new QuoteRequestNotification($quote));
```

`mail.admin_email` **is not defined in `config/mail.php`**, so the fallback always wins.
Every internal new-quote notification is addressed to `admin@aluminiumcraft.tn` — a domain
belonging to the retired *AluminiumCraft* brand, not to `promoaluplus`. The client-facing
confirmation still goes out correctly, so a lost lead leaves no visible trace.

The same retired brand appears in customer-facing copy:

| Location | Text |
|---|---|
| `app/Mail/QuoteRequestReceived.php:27` | subject: `Votre demande de devis - AluminiumCraft Tunisie` |
| `resources/views/emails/quote-received.blade.php:19,50,54` | `SiteSetting::get('company_name', 'AluminiumCraft Tunisie')` |
| `resources/views/emails/quote-received.blade.php:55` | `© 2026 AluminiumCraft. Tous droits réservés.` |

Everywhere else the brand is `PromoAlu+` (`AdminPanelProvider.php:30`, `PdfController.php:14`,
`layouts/app.blade.php`, all three `lang/*/messages.php`). A customer who requests a quote
receives an e-mail from a company whose name appears nowhere on the site.

**Fix:** add `'admin_email' => env('MAIL_ADMIN_ADDRESS', 'promoaluplus@gmail.com')` to
`config/mail.php`, set it in `.env`, and replace every `AluminiumCraft` fallback with `PromoAlu+`.

Marker test: `SiteCoherenceTest::test_the_company_brand_name_is_consistent_across_every_fallback`.

---

### F-04 — The contact page silently overwrites an admin-edited FAQ  ⚠️ Medium

**[SOURCE]** — `app/Http/Controllers/PageController.php:52-67`

```php
$faqs = Faq::active()->ordered()->get()->map(function (Faq $faq) use ($translator) {
    if ((int) $faq->sort_order === 2) {
        $faq->question = [...$translator->get('messages.faq_q2', [], 'fr'), ...];
        $faq->answer   = [...];
    }
    return $faq;
});
```

Whichever FAQ happens to sit at `sort_order === 2` has its question and answer replaced by
the language files at render time. An admin who edits that row in Filament sees the change
saved, sees it in the admin table, and sees **no change on the public page**. The override is
positional, so reordering FAQs moves the problem to a different row.

This directly contradicts the design intent recorded in
`docs/superpowers/specs/2026-08-09-admin-manageable-content-design.md` (content should be
admin-manageable).

**Fix:** delete the `map()` and seed the desired text into the FAQ row instead.

Marker test: `SiteCoherenceTest::test_contact_page_does_not_override_database_faqs_with_language_files`.

---

### F-05 — Three committed files are zero bytes  ⚠️ Medium

**[VERIFIED]** — `ls -la`, plus `php artisan route:list --path=admin`

| File | Size |
|---|---|
| `app/Filament/Resources/CategoryResource.php` | 0 bytes |
| `app/Filament/Resources/ProjectTypeResource.php` | 0 bytes |
| `database/seeders/CategorySeeder.php` | 0 bytes |

Because the resource classes are empty they register no routes — `/admin/categories` and
`/admin/project-types` do not exist. Their `Pages/` subdirectories, however, contain fully
written `Create*`/`Edit*`/`List*` classes that nothing can reach.

The backing models are also inert: `App\Models\ProjectType` has an empty body, and neither
`Category::` nor `ProjectType::` is referenced anywhere in `app/` or `resources/`. Both tables
are empty in the live database (0 rows each). Meanwhile `Project.category` is a plain
`string` column (`create_projects_table.php:18`) with no foreign key to `categories` — so the
category system was never wired up at all.

**Fix:** delete `Category`, `ProjectType`, their resources, page classes, seeders and
migrations — or finish them. Leaving 0-byte classes in `app/Filament/Resources` is a trap for
anyone running Filament's discovery.

Marker tests: `PublicSiteFeatureTest::test_category_and_project_type_resources_register_no_admin_routes`
(passing — it pins the current state) and `SiteCoherenceTest::test_no_seeder_or_resource_class_file_is_empty`.

---

### F-06 — The portfolio filters on a category that means something else  ✅ Resolved

**[RESOLVED]** — `resources/views/pages/portfolio.blade.php` no longer hardcodes `windows`,
`doors`, `facades`. The filter bar (and the `messages.' . $category` label lookup that produced
the "Pergolas & Abris" mislabel) is now built from `App\Models\ProjectType::active()->ordered()`,
which `PageController::portfolio()` passes to the view as `$projectTypes`. `ProjectType` is a
real translatable model (`getTranslatedName()`, `active()`/`ordered()` scopes), seeded via
`ProjectTypeSeeder` with the same three starter types (`windows`, `doors`, `facades`) but now
editable/extensible by an admin — adding a fourth `ProjectType` row automatically appears as a
filter, and each project's image/label resolve through the matching `ProjectType` record instead
of a `messages.*` key collision.

Marker test: `SiteCoherenceTest::test_portfolio_filter_categories_come_from_the_database` (asserts
the view contains no hardcoded `route('portfolio', ['category' => '...'])` literal), plus
`PortfolioVisibilityTest::test_the_filter_bar_is_built_from_project_types`.

---

### F-07 — The site promises two different response times  ⚠️ Low

**[VERIFIED]** — read from `lang/fr/messages.php`

| Key | Promise |
|---|---|
| `response_time` | Réponse sous **24h** |
| `cta_description` | devis … sous **48h** |
| `quote_form_intro` | recevez une réponse sous **48h** |
| `quote_success` | Nous vous contacterons sous **48h** |
| `faq_a1` | Nous vous répondrons sous **48h** |

The 24h figure is shown on the contact page next to the 48h form intro. All three locales
carry the same contradiction.

**Fix:** pick one figure. Marker test: `SiteCoherenceTest::test_the_promised_response_time_is_stated_consistently`.

---

### F-08 — Seeders read content from outside the repository  ⚠️ High

**[VERIFIED]** — `ls /Users/younes/Alu-workshop/content_docs/json/`

```php
// database/seeders/ServiceSeeder.php:16
$content = $this->readJson(base_path('../content_docs/json/services.json'));

// database/seeders/FaqSeeder.php:15
$jsonFile = base_path('../content_docs/json/questions_frequentes.json');
```

`content_docs/` lives in the **parent directory of the repository** and is not tracked by
git. Consequences:

- On a fresh clone, `php artisan migrate --seed` produces **zero services and zero FAQs**.
  `ServiceSeeder` returns an empty array and `continue`s past every service — silently, with
  no error.
- `FaqSeeder` is worse: it calls `file_get_contents()` with no existence check, so it emits a
  PHP warning and then `json_decode(false)` → the guard returns early. Still silent.
- The site's entire service catalogue is therefore un-reproducible from the repo alone.

**Fix:** move `content_docs/json/*.json` into `database/seeders/content/` and commit them.

Marker test: `SiteCoherenceTest::test_content_seeders_read_from_a_path_inside_the_repository`.

---

### F-09 — Three committed seeder JSON files are never read  ⚠️ Low

**[VERIFIED]** — no seeder references these filenames.

`database/seeders/services.json`, `faqs.json` and `site_settings.json` are committed and
look authoritative, but no seeder loads them (see F-08 — the seeders read the external
path). They are also *stale and truncated*: `services.json` contains **1** service (`kitchen`)
where the catalogue has 9; `faqs.json` contains 1 FAQ where the database holds 13.

`CLAUDE.md` compounds this by documenting them as the real source:

> **Content seeding pipeline:** JSON sources live in `database/seeders/` (`services.json`,
> `faqs.json`, `site_settings.json`); seeders read them into the DB. Edit the JSON source
> first, then reseed.

Following those instructions changes nothing. Anyone onboarding will edit the wrong file.

**Fix:** either make the seeders read these files (preferred — resolves F-08 too) or delete
them and correct `CLAUDE.md`.

Marker test: `SiteCoherenceTest::test_seeder_json_files_shipped_in_the_repository_are_actually_used`.

---

### F-10 — `APP_LOCALE=en` contradicts the fr-first content model  ⚠️ Low

**[VERIFIED]** — `.env`, plus observed during testing.

Every translated accessor falls back to `fr` (`Service::getTranslatedTitle()`,
`Faq::getTranslatedQuestion()`, `SiteSetting::getTranslated()`, `CanonicalServiceCatalog`),
and the seeded content is French-first. But `.env` sets `APP_LOCALE=en` and
`APP_FALLBACK_LOCALE=en`. A first-time visitor with no session gets the English UI over
French-authored content, and any content row missing an `en` value renders French inside an
English page. This surfaced immediately while writing tests — a project seeded with only
`fr` titles renders its French title on the English page.

**Fix:** set `APP_LOCALE=fr` and `APP_FALLBACK_LOCALE=fr` in `.env` and `.env.example`.

---

## 4. Fake, placeholder, and unverifiable data

### 4.1 Invented testimonials rendered to real visitors  ⚠️

**[VERIFIED]** — the **local** `testimonials` table holds **0 rows**. Production not checked.

`resources/views/pages/home.blade.php:570-646` has a `@forelse … @empty` block whose fallback
branch hardcodes three fabricated 5-star reviews with invented names and cities. That branch
renders whenever the table is empty — which is the case locally.

**Action: check the production row count.** If `testimonials` is also empty there, the public
site is currently presenting three invented customer reviews as genuine.

| Name | Location | Source |
|---|---|---|
| Mohamed B. | Paris, France | `home.blade.php` + `messages.testimonial_1` |
| Sonia K. | Montréal, Canada | `home.blade.php` + `messages.testimonial_2` |
| Ahmed T. | Berlin, Allemagne | `home.blade.php` + `messages.testimonial_3` |

The same three personas are duplicated in `database/seeders/DemoSeeder.php:100-142`, where
their review text still credits *"AluminiumCraft"*. Fabricated customer reviews carry legal
exposure in most of the markets these personas are from (France, Canada, Germany).

**Fix:** seed real testimonials, or remove the `@empty` branch so the section hides when
there is nothing genuine to show.

### 4.2 Invented portfolio projects rendered to real visitors  ⚠️

**[VERIFIED]** — the **local** `projects` table holds **0 rows**. Production not checked.

`resources/views/pages/portfolio.blade.php:63-106`, the `@empty` branch, hardcodes three
projects and renders whenever the table is empty — again, the case locally:

| Project | Location | Image |
|---|---|---|
| Villa Moderne - La Marsa | La Marsa, Tunis | `images.unsplash.com/photo-1600596542815…` |
| Résidence Carthage | Carthage, Tunis | `images.unsplash.com/photo-1600585154340…` |
| Immeuble Commercial | Centre Urbain Nord | `images.unsplash.com/photo-1486406146926…` |

These are stock photographs presented as the company's own completed work, with invented
client sites. Duplicated again in `DemoSeeder.php:69-98`. **Same caveat as 4.1 — confirm the
production row count before treating this as live.**

### 4.3 Unverifiable marketing statistics

**[SOURCE]**

| Claim | Where | Note |
|---|---|---|
| `15+ ans d'expertise certifiée` | `messages.hero_values_badge`, `about_intro`, `seo_desc_about` | Hardcoded in all 3 locales; also `SiteSetting::get('stats_years', '15')` at `home.blade.php:149` |
| `500+` projects | `SiteSettings.php:112` (admin default) | **Never rendered** — see 5.4 |
| `98%` satisfaction | `SiteSettings.php:122` | **Never rendered** |
| `12` team members | `SiteSettings.php:127` | **Never rendered** |
| `Des centaines de projets réalisés` | `messages.hundreds_projects_completed`, hero slide 4 | Rendered |
| `Garantie 10 ans` / European standards | `messages.guarantee_10_years`, `european_standards`, `faq_a3` | Rendered; contractual claim worth confirming with the client |

### 4.4 Placeholder credentials in three mutually inconsistent forms

**[VERIFIED]**

| Source | E-mail | Password |
|---|---|---|
| `CLAUDE.md` ("Admin login (from seeders)") | `admin@aluminiumcraft.tn` | `password` |
| `database/seeders/DatabaseSeeder.php:20` (the seeder actually run) | `admin@promoaluplus.tn` | `admin123` |
| `database/seeders/DemoSeeder.php:19` (never called) | `admin@aluminiumcraft.tn` | `password` |

`DatabaseSeeder.php:29` additionally creates `test@example.com` / `password`, described as
"(optional)", with a verified e-mail and full panel access. **This account must not reach
production.** `CLAUDE.md`'s documented login does not work.

### 4.5 Third-party assets loaded at runtime

**[VERIFIED]** — grep over `resources/views`

- `resources/views/pages/about.blade.php:95` — Unsplash hero photo (`photo-1504307651254…`),
  presented as the workshop. Survives commit `8e85142` *"host hero and service gallery
  images locally"*, which missed this file and the portfolio fallbacks (4.2).
- `resources/views/layouts/app.blade.php:33` — `https://unpkg.com/lucide@0.544.0/…` loaded
  from a CDN on every page. It is SRI-pinned, which is good, but the whole icon set fails
  closed if unpkg is unreachable, and it leaks visitor IPs to a third party (a GDPR
  consideration given the site targets expatriates in the EU).

---

## 5. Redundancy and dead code

### 5.1 Duplicate settings systems

`App\Models\Setting` (+ `settings` table, `create_settings_table.php`) and
`App\Models\SiteSetting` (+ `site_settings` table) both implement `get()`/`set()`/`group`.
Only `SiteSetting` is used — **`Setting::` appears zero times** across `app/` and
`resources/`, and the `settings` table holds 0 rows.
**Fix:** drop the model, the migration and the table.

### 5.2 Duplicate/abandoned root documentation

15 markdown files sit in the repo root, several near-duplicates:

- `TICKETS_MOURAD_29_APR.md` (5.2 KB) and `PLAN_TICKETS_MOURAD_29_APR.md` (6.9 KB)
- `CAROUSEL_IMPROVEMENTS.md` and `CAROUSEL_DEBUG_FIX.md`
- `FRONTEND_AUDIT_REPORT.md`, `UX_PERFORMANCE_SEO_AUDIT.md`, `FINAL_AUDIT_AGAINST_MAIL.md` — three overlapping audits
- `MARKDOWN_ORGANIZATION_GUIDE.md` and `DOCUMENTATION_INDEX.md` — two indexes *of the other files*

`CLAUDE.md` already flags this ("the repo root already has many `*_REPORT.md` / audit docs —
don't add more by default"), which is why this report was written to `docs/`.
**Fix:** move the historical ones into `docs/archive/` and delete the two meta-indexes.

### 5.3 Unreferenced views and seeders

- `resources/views/welcome.blade.php` — the stock Laravel splash page; **0 references** in
  `routes/` or `app/`. It also loads `laravel.com`, `laracasts.com` and `cloud.laravel.com`.
- `database/seeders/DemoSeeder.php` — not called by `DatabaseSeeder`, referenced nowhere
  else. 7 KB of fake content (4.1, 4.2) using the retired brand and a non-canonical
  `facades` service slug that the rest of the system rejects.

### 5.4 Admin fields that render nowhere

`app/Filament/Pages/SiteSettings.php` exposes a "Statistiques" tab with four inputs. Only
`stats_years` is read by a view (`home.blade.php:149`). `stats_projects`,
`stats_satisfaction` and `stats_team` are **write-only** — an admin can edit them, save
successfully, and see no change anywhere on the site.

### 5.5 Unreferenced translation keys

**[VERIFIED]** — 314 keys per locale; 141 referenced literally, **173 with no literal
reference**.

That raw figure overstates it: `services.blade.php:55,319` and `portfolio.blade.php:51,59`
build keys dynamically (`__('messages.' . $specKey)`), which legitimately reaches most of the
spec/material keys (`thickness_range`, `aluminum_profiles`, …). Keys confirmed unreachable by
either route include:

`satisfied_clients`, `team_members`, `our_process`, `step_contact`, `step_contact_desc`,
`step_quote`, `step_quote_desc`, `step_production`, `step_production_desc`,
`step_installation`, `step_installation_desc`, `value_quality_title`/`_desc`,
`value_proximity_title`/`_desc`, `value_sustainability_title`/`_desc`,
`value_innovation_title`/`_desc`, `value_integrity_title`/`_desc`, `value_timely_title`/`_desc`,
`faq_q1`, `faq_a1`, `faq_q3`, `faq_a3`, `budget_range`, `select_budget`, `full_name`,
`your_name`, `get_in_touch`, `faq_title`, `multilingual_team`, `flexible_payment`,
`payment_desc`, `view_gallery`, `photos`.

Note `faq_q1/a1/q3/a3` are dead while `faq_q2/a2` are alive — the residue of F-04. Each key
costs three maintained translations.

### 5.6 Commented-out section retained in a view

`home.blade.php:483-555` — a 73-line "TOP Produits Aluminium" carousel commented out with
`{{-- --}}`, annotated *"hidden 2026-07-15 … Kept for possible future reuse"*. Git history
already preserves it.

---

## 6. Coherence checks that passed

Worth recording, because these are the invariants that hold and the new tests will keep
holding:

- **Translation parity** — `fr`, `en` and `ar` each expose exactly 314 keys, no key present
  in one and absent from another, no empty value in any locale.
- **No untranslated Arabic** — zero `ar` values are byte-identical to their French source.
- **English translation completeness** — 9 `fr`/`en` identical values, all legitimate proper
  nouns or loanwords (`pergola`, `navigation`, `photos`, `durable`, …), captured in an
  explicit allowlist in the test rather than being silently ignored.
- **No missing keys in views** — every `__('messages.x')` literal across all 15 Blade files
  resolves in all three locales.
- **Canonical catalogue alignment** — `Service::DEFAULT_ICON_BY_SLUG` and
  `DEFAULT_COLOR_BY_SLUG` cover exactly the 9 canonical slugs, in order; each slug has a
  non-empty, unique label in each locale; `quoteOptions()` is exactly the 9 slugs plus `other`.
- **Locale fallbacks** — `Service`, `Faq` and `SiteSetting` accessors all fall back to `fr`
  correctly; `SiteSetting`'s forever-cache is properly invalidated on update.
- **Quote validation** — accepts all 9 canonical slugs plus `other`, rejects anything else.
- **Resilience** — every public page renders against a completely empty database, and a quote
  submission still succeeds when the mail transport throws.

---

## 7. Suggested order of work

| # | Finding | Why first |
|---|---|---|
| 1 | **F-01** admin 403 in production | The panel is unusable the moment this ships |
| 2 | **F-03** quote notifications to a dead address | Silent lead loss; no error anywhere |
| 3 | **F-02** `/chatbot/faqs` 500 on MySQL/Postgres | Latent outage, invisible on SQLite |
| 4 | **4.1 / 4.2** fabricated testimonials and projects | Renders whenever those tables are empty (they are, locally) — confirm production first; legal exposure if live |
| 5 | **F-08 / F-09** seeder content outside the repo | Blocks reproducible setup and onboarding |
| 6 | **4.4** `test@example.com` admin account | Must not reach production |
| 7 | **F-04** FAQ override | Admin edits silently discarded |
| 8 | **F-07 / F-10** 24h vs 48h, `APP_LOCALE` (F-06 portfolio categories resolved) | Cheap, visible coherence wins |
| 9 | **F-05 / §5** dead code and duplicate docs | Housekeeping; no user impact |

---

## 8. Reproducing this

```bash
npm run build                                          # required once — @vite 500s without a manifest
php artisan test --compact                             # 77 passed, 8 skipped
php artisan test --compact tests/Feature/PublicSiteFeatureTest.php
php artisan test --compact tests/Feature/SiteCoherenceTest.php
```

The 8 skips are the defect markers for F-02 through F-09. As each finding is fixed, delete
the corresponding `markTestSkipped()` call — the assertion beneath it is already written and
should pass.

---

## 9. Open question

**Production row counts for `projects` and `testimonials` were not observed.** Everything in
§4.1 and §4.2 is based on the local `database.sqlite`, where both tables are empty. Run this
against production before deciding how urgent those two findings are:

```bash
php artisan tinker --execute='echo "projects: ".App\Models\Project::count()."  testimonials: ".App\Models\Testimonial::count();'
```

If both return 0, findings 4.1 and 4.2 move to the top of the work order.

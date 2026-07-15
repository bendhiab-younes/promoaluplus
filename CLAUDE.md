# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Laravel 12 + Filament 3 site for **Promo Alu Plus**, an aluminum joinery company in Tunisia. Public multilingual marketing site (FR/EN/AR with RTL) plus a Filament admin panel that manages content, quotes, and invoices. Database is SQLite; frontend is Blade + Alpine.js + Tailwind v4 (via Vite).

## Commands

```bash
composer run dev        # Run server + queue + pail logs + vite concurrently (primary dev loop)
php artisan serve       # App only — http://127.0.0.1:8000, admin at /admin
npm run dev             # Vite dev server (needed for asset changes to reflect)
npm run build           # Production asset build — required once per checkout: the layout
                        # loads CSS via @vite, so pages 500 (missing manifest) until a
                        # build exists or the Vite dev server is running

php artisan test --compact                                   # Full suite
php artisan test --compact tests/Feature/ExampleTest.php      # Single file
php artisan test --compact --filter=testName                 # Single test

vendor/bin/pint --dirty  # Format changed PHP files — run before finalizing changes

php artisan migrate --seed              # Migrate + seed
php artisan migrate:fresh --seed        # Full reset (destructive)
php artisan db:seed --class=ServiceSeeder --no-interaction   # Reseed one content type
php artisan optimize:clear              # Clear all caches (config/route/view) — first fix when changes don't appear
```

Admin login (from seeders): `admin@aluminiumcraft.tn` / `password`.

## Architecture

**Two surfaces, one Eloquent layer.** Controllers in `app/Http/Controllers` (`PageController`, `QuoteController`, `ChatbotController`, `PdfController`) serve the public site; `app/Filament/Resources/*` provide admin CRUD over the same models. Public routes are in `routes/web.php`; PDF routes are `auth`-gated. There is no REST API — the "chatbot API" is a set of internal web routes.

**Multilingual content lives in the database as JSON columns, not translation files.** Content models (`Service`, `Project`, `Testimonial`, `Faq`) cast fields like `title`, `description`, `features`, `gallery` to `array` and expose `getTranslatedX($locale)` accessors that fall back to `fr`. Static UI strings live in `lang/{fr,en,ar}`. When touching content, keep all three locales aligned and default-fallback to `fr`.

**Locale flow:** `SetLocale` middleware (registered in `bootstrap/app.php`, not a Kernel) reads `session('locale')` each request; `PageController@setLocale` (`/locale/{locale}`) writes it. Valid locales are hardcoded to `fr`, `ar`, `en` in multiple places — update all if adding one.

**Canonical service list is authoritative.** `App\Support\CanonicalServiceCatalog` defines the 9 service slugs and their ordering, shared across homepage, footer, contact, and quote validation (`CanonicalServiceCatalog::validationRule()`). `Service` model holds `DEFAULT_ICON_BY_SLUG` / `DEFAULT_COLOR_BY_SLUG` keyed by the same slugs. Adding/renaming a service means updating the catalog, the model defaults, the seeder, and `lang/*` message keys together.

**Content seeding pipeline:** JSON sources live in `database/seeders/` (`services.json`, `faqs.json`, `site_settings.json`); seeders read them into the DB. Edit the JSON source first, then reseed. See the `service-catalog-management` skill (`.ai/skills/`) for the full workflow.

**Quotes → Invoices workflow:** `Quote`/`QuoteItem` capture requests with a status lifecycle; accepted quotes convert to `Invoice`/`InvoiceItem` (auto-numbered `FAC-YYYY-XXXX`). Both render to PDF via DomPDF through `PdfController`. Managed entirely in Filament (`QuoteResource`, `InvoiceResource`).

## Conventions

This repo follows **Laravel Boost guidelines** — see `.github/copilot-instructions.md` for the full ruleset. Key points:
- Use `php artisan make:*` (with `--no-interaction`) to scaffold; use Filament's own `make:` commands for Filament files.
- Form Request classes for validation, not inline; explicit return types and constructor property promotion; curly braces always.
- Prefer Eloquent (`Model::query()`) over `DB::`; eager-load to avoid N+1.
- Tests are PHPUnit (not Pest); most should be feature tests. Run the affected test after changes.
- Do not create documentation files unless explicitly asked (the repo root already has many `*_REPORT.md` / audit docs — don't add more by default).
- Boost MCP tools (`search-docs`, `tinker`, `database-query`, `list-artisan-commands`) are available; prefer `search-docs` for Laravel/Filament questions.

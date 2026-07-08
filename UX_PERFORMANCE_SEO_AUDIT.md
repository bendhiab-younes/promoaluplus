# PromoAlu+ — UI/UX, Performance & SEO Audit

**Date:** 2026-07-08
**Scope:** `resources/views/layouts/app.blade.php` and all public pages (`home`, `services`, `portfolio`, `about`, `contact`), plus supporting config (`vite.config.js`, `.env`, `public/robots.txt`, controllers).

This report is organized by domain, each ranked most-severe first. Every finding below was verified against the actual source (file + line), not inferred. A prioritized action plan closes the report.

---

## 1. UI/UX Coherence

| # | Finding | Location | Severity |
|---|---------|----------|----------|
| 1 | Service/product card accent colors (`rose`, `violet`, `amber`, `yellow`, `teal`, `indigo`, etc. — 9 colors keyed by service slug) have no relation to the documented design system (blue `#1e3a8a`/`#3b82f6`, orange `#f97316`/`#ea580c`, gold `#d4af37`). This is systemic drift from `.github/design_context.md`. | `home.blade.php:415-419`, `services.blade.php:154` | High |
| 2 | Every section "badge" (icon + label above a heading) uses hardcoded `mr-1` instead of the logical `me-1`, so in Arabic (RTL) every badge icon renders on the wrong side of its label, sitewide. | `home.blade.php:403,500,631`; `services.blade.php:106`; `portfolio.blade.php:10,207`; `about.blade.php:69,85,106,191`; `contact.blade.php:92,245` | High |
| 3 | The same CTA button block is hand-rolled 4 times with raw utility classes instead of the shared `.btn-primary`/`.btn-secondary` classes, and all 4 copies use `mr-2` (not `me-2`) on the icon — duplicated markup *and* an RTL bug baked in 4 places. | `home.blade.php:642-648`, `services.blade.php:296-301`, `portfolio.blade.php:213-218`, `about.blade.php:305-310` | High |
| 4 | The per-service "Request quote" button (repeated once per service card) also uses `mr-2` instead of `me-2`, inconsistent with the hero CTA above it on the same page, which gets it right. | `services.blade.php:266` | High |
| 5 | Hero carousel prev/next arrows have no `rtl:rotate-180`, while the product carousel's arrows lower on the same page correctly flip for RTL — inconsistent treatment within a single page. | `home.blade.php:179-184` vs `home.blade.php:511,514` | Medium-High |
| 6 | Footer contact icons (map-pin, phone, WhatsApp, mail) use hardcoded `mr-2` instead of `me-2`, so they sit on the wrong side in the Arabic footer. | `layouts/app.blade.php:857,861,865,871` | Medium |
| 7 | Contact form success/error banners and the send button manually branch RTL via inline ternaries (`ml-3`/`mr-3`, `-scale-x-100`) instead of logical utilities — fragile and inconsistent with the logical-class approach used elsewhere on the same page. | `contact.blade.php:100,108,111,231,256,259` | Medium |
| 8 | Icon-only button `aria-label`s are inconsistently localized: back-to-top is hardcoded French, WhatsApp float is hardcoded English, while the chatbot toggle and mobile-menu buttons correctly branch by locale. | `layouts/app.blade.php:885,892` | Medium |
| 9 | "Client Testimonials" section on the portfolio page is a bare `<h2>` with no badge and no intro paragraph, breaking the badge+h2+p header pattern every other section uses. | `portfolio.blade.php:110-112` | Medium |
| 10 | Contact page's hero header has no badge above its `<h1>`, unlike the equivalent headers on Services, Portfolio, and About. | `contact.blade.php:25-32` | Medium |
| 11 | Portfolio testimonial cards use plain `bg-white p-8 rounded-xl shadow-lg` with no hover treatment, inconsistent with `.service-card`/`.portfolio-item`, which have defined hover-lift/shadow behavior. | `portfolio.blade.php:110,116-132` | Low-Medium |
| 12 | Section vertical-padding scale is inconsistent: most sections use `py-16 md:py-24` (or the 3-step `py-12 md:py-20 lg:py-24`), but Portfolio's testimonials section uses a flat `py-20` and Contact's info section stops at `py-12 md:py-20` (missing the `lg:` step used elsewhere). | `portfolio.blade.php:110`, `contact.blade.php:35` | Low-Medium |
| 13 | A ~50-line "Why Choose Us" section is commented out and left in the template rather than removed. | `home.blade.php:568-617` | Low |
| 14 | A "Certifications" section is similarly commented out and left in place ("hidden for now"). | `about.blade.php:250-287` | Low |
| 15 | `.section-divider`, `.text-gradient`, and `.img-loading` CSS classes are defined globally but never referenced by any markup — dead CSS. | `layouts/app.blade.php:452,458,683` | Low |
| 16 | The "our workshop" story image on the About page has no explicit height/aspect-ratio, unlike sibling images that consistently use `h-52`/`h-72`/`h-[400px]` — risks layout shift while it loads. | `about.blade.php:92-96` | Low |
| 17 | The same decorative SVG dot-pattern background + inline `<style>` is redeclared independently in `services.blade.php`, `portfolio.blade.php`, `about.blade.php`, and `home.blade.php` instead of being one shared layout-level class. | multiple | Low |

---

## 2. Performance

| # | Finding | Location | Severity |
|---|---------|----------|----------|
| 1 | Tailwind is loaded via the browser-JIT `cdn.tailwindcss.com` script, which compiles every utility class client-side on every page load, ships unminified/unpurged CSS, and is render-blocking. Tailwind's own docs mark this "not for production." | `layouts/app.blade.php:18` | High |
| 2 | A full Vite + `@tailwindcss/vite` pipeline is already configured (`vite.config.js`, `resources/css/app.css` with a real v4 `@theme` block) but is **never included** via `@vite([...])` in the layout — and `public/build` doesn't exist, confirming it has never actually been built for this environment. The entire proper build pipeline is dead code sitting next to the CDN script it should replace. | `vite.config.js`, `resources/css/app.css`, `public/build` (missing) | High |
| 3 | All content imagery — hero slides, service galleries, portfolio, testimonials — is hotlinked directly from `images.unsplash.com` (~25 URLs, hardcoded inline across `home.blade.php`, `portfolio.blade.php`, `about.blade.php`, plus DB-stored URLs via `Service::getFeaturedImage()`). `public/images/` contains only the 1.3MB logo. Every page load depends on a third-party CDN's availability/latency for core content, with no local fallback. | `home.blade.php`, `portfolio.blade.php`, `about.blade.php`, `app/Models/Service.php:128` | High |
| 4 | Service and portfolio images have no `srcset`/`sizes`, unlike the hero carousel (which correctly serves responsive widths up to 2200w) — mobile visitors download the same full-resolution image as desktop. | `services.blade.php`, `portfolio.blade.php` | Medium |
| 5 | `.env` routes cache, sessions, *and* the mail queue through the same SQLite file as content tables (`DB_CONNECTION=sqlite`, `CACHE_STORE=database`, `SESSION_DRIVER=database`, `QUEUE_CONNECTION=database`). SQLite allows one writer at a time, so cache/session/queue writes can contend with each other under real concurrent traffic. | `.env` | Medium |
| 6 | The ~685-line inline `<style>` block in the layout is duplicated into every single page response (no external, cacheable, minified stylesheet) — this goes away once #1/#2 are fixed by routing everything through the Vite build. | `layouts/app.blade.php:29-714` | Medium |
| 7 | `.navbar-scrolled` still applies `backdrop-filter: blur(20px)` on mobile — the mobile media query at `layouts/app.blade.php:558-562` disables backdrop-filter for `.glass-effect`/`.btn-secondary` but forgot `.navbar-scrolled`, so the expensive GPU blur still runs on phones once the user scrolls past 100px. | `layouts/app.blade.php:90,558-562` | Medium |
| 8 | Two separate `window.addEventListener('scroll', ...)` handlers (navbar toggle + back-to-top visibility) run unthrottled on every scroll event with no `requestAnimationFrame`/passive coalescing, compounding the blur-filter cost above during scroll. | `layouts/app.blade.php:979-990, 995-1002` | Low-Medium |
| 9 | The page depends on 5 third-party origins at load time (`cdn.tailwindcss.com`, `unpkg.com`, `fonts.googleapis.com`, `fonts.gstatic.com`, `images.unsplash.com`) — each a separate DNS+TLS handshake before first paint. Self-hosting Tailwind's build output and Lucide icons would cut this to 2 (fonts + image CDN). | `layouts/app.blade.php` | Medium |
| 10 | Lucide icons load from `unpkg.com/lucide@latest` — unpinned version, no SRI hash, and no cache reuse across deploys since `@latest` can change any day. | `layouts/app.blade.php:21` | Medium |
| 11 | No query-result caching anywhere in `PageController` (e.g. `Service::active()->orderBy('sort_order')->get()` re-runs on every request) — harmless at today's catalog size (9 services) but has zero headroom and compounds with the SQLite contention above. | `app/Http/Controllers/PageController.php` | Low |
| 12 | No `<link rel="preload">` for the hero's first (LCP) image, despite it already being `fetchpriority="high"`/`loading="eager"` — a preload hint would shave one discovery round-trip off the largest contentful paint. | `home.blade.php:14-31` | Low |
| 13 | Google Fonts pulls Noto Sans Arabic (5 weights) even for FR/EN visitors who never render Arabic text — could be conditionally injected only when `app()->getLocale() === 'ar'`. | `layouts/app.blade.php:26` | Low |

---

## 3. SEO

For a local aluminum-joinery business whose stated audience is Tunisians, Tunisian expats, and European clients searching in French/Arabic/English, these findings weigh organic discoverability and ranking potential.

| # | Finding | Location | Severity |
|---|---------|----------|----------|
| 1 | No `sitemap.xml` exists anywhere, and `public/robots.txt` has no `Sitemap:` directive — crawlers have no structured discovery path for any page. | `public/robots.txt` (missing sitemap entirely) | High |
| 2 | Locale switching is session-based (`SetLocale` middleware + `PageController@setLocale`), so every page has exactly **one URL** regardless of language, with **zero `hreflang` tags anywhere** (confirmed via full-codebase search). Search engines can only ever index whichever locale happens to be the session/server default per URL — the fr/en/ar variants are not independently crawlable, indexable, or linkable. This is the single biggest structural SEO gap given the site's multilingual audience. | `app/Http/Middleware/SetLocale.php`, `PageController.php` | High |
| 3 | **Not one of the 5 pages** overrides `@section('meta_description', ...)`, `@section('og_title', ...)`, or `@section('og_description', ...)` — confirmed by direct search. Every single page (home, services, portfolio, about, contact) ships the identical generic meta description and OG title/description. | `layouts/app.blade.php:6,13-14`; all of `resources/views/pages/*.blade.php` | High |
| 4 | Open Graph has no `og:image`, `og:url`, or `og:locale`, and there are no Twitter Card tags at all — every shared link (notably via the WhatsApp CTA present on every page) renders a generic/incomplete preview. | `layouts/app.blade.php:12-15` | High |
| 5 | No `<link rel="canonical">` anywhere. Combined with the portfolio page's `?category=` query-string filters, this risks duplicate-content dilution between filtered and unfiltered portfolio URLs. | `layouts/app.blade.php` (absent); `portfolio.blade.php:25-40` | High |
| 6 | No `LocalBusiness`/`Organization` structured data anywhere — NAP (name/address/phone) exists only as visible footer text, never as JSON-LD, so Google has no basis to build a Knowledge Panel or local-pack result for searches like "menuiserie aluminium Tunisie." The footer already computes `$footerAddress`/`$footerPhone`/`$footerEmail`, so the data needed is already in scope. | `layouts/app.blade.php` (footer `@php` block) | High |
| 7 | The Contact page renders a visible FAQ (via `PageController@contact`) with zero `FAQPage` schema, forfeiting FAQ rich-snippet eligibility on what is likely the highest-conversion-intent page. Services already does this correctly with `Service`/`ItemList` JSON-LD — same pattern should extend here. | `contact.blade.php:252-263` (schema absent); compare `services.blade.php:309-311` (schema present) | Medium |
| 8 | No explicit `<meta name="robots" content="index,follow">`, and no `noindex`/canonical guidance on the thin-duplicate `?category=` portfolio variants. Currently harmless (default behavior is index/follow) but there's no explicit signal and no dedup strategy for filtered URLs. | `layouts/app.blade.php` | Medium |
| 9 | Every page title is just the one-word nav label ("Accueil", "Contact", etc.) plus the fixed tagline suffix — no city or service-keyword targeting (no "Tunis", "fenêtres aluminium", etc.) despite the business needing to rank for exactly those local + product terms. | `layouts/app.blade.php:10`; `@section('title', ...)` in each page | Medium |
| 10 | All hero/portfolio/about imagery is hotlinked from `images.unsplash.com` with generic filenames and — per the performance section — doesn't even depict the company's actual work; self-hosted, descriptively-named images (e.g. `fenetre-aluminium-tunisie-tunis.jpg`) would carry real image-search value that stock photos cannot. | `home.blade.php`, `portfolio.blade.php`, `about.blade.php` | Medium |
| 11 | Portfolio project `alt` text is just the project title (e.g. "Résidence Carthage") with no material/product/location context — a missed long-tail image-search opportunity. | `portfolio.blade.php:47` | Low |
| 12 | Logo `alt` text is brand-only ("PromoAlu+") in both header and footer — a minor missed opportunity to reinforce core keywords in alt text. | `home.blade.php:749`, `layouts/app.blade.php:825` | Low |

**Confirmed correct, no action needed:** `<html lang>` and `dir="rtl"` are properly conditional on the active locale; every page has exactly one `<h1>` with logical h2→h3 nesting and no skipped levels.

---

## 4. Prioritized Action Plan

**Quick wins (low effort, high impact):**
1. Add unique `@section('meta_description', ...)`, `og_title`, and `og_description` to each of the 5 pages.
2. Add `og:image`, `og:url`, `og:locale`, and Twitter Card tags to the layout head.
3. Add a `<link rel="canonical">` to the layout head.
4. Generate `sitemap.xml` and add a `Sitemap:` line to `robots.txt`.
5. Replace all sitewide `mr-1`/`mr-2`/`ml-3` icon-spacing utilities with logical `me-`/`ms-` equivalents (fixes RTL layout across the entire site in one pass).
6. Extend the mobile `backdrop-filter: none` override to include `.navbar-scrolled`.

**Medium effort, high impact:**
7. Wire up the existing Vite pipeline (`@vite([...])` in the layout, remove the Tailwind CDN `<script>`, run `npm run build`) — this alone resolves the single biggest performance finding and multiple downstream ones (unminified CSS, dead build pipeline).
8. Add `LocalBusiness` JSON-LD to the layout using the footer's already-computed NAP data.
9. Add `FAQPage` JSON-LD to the Contact page's FAQ section, mirroring the Services page's existing schema pattern.
10. Consolidate the 4 duplicated CTA button blocks into one shared Blade partial/component.

**Larger structural investment:**
11. Move from session-based locale to path-based locales (`/en/...`, `/ar/...`) with `hreflang` alternates — the highest-leverage SEO fix given the site's multilingual audience, but the most invasive (routing changes across the app).
12. Self-host content imagery instead of hotlinking Unsplash — improves both performance (no third-party CDN dependency) and SEO (descriptive filenames/alt text of actual company work).
13. Move `CACHE_STORE` off SQLite (e.g. to `file` or `redis`) to remove write contention with sessions/queue/content data.

---

*Methodology: three focused read-only audits (UI/UX, performance, SEO) were run in parallel against the current codebase, then each domain's highest-severity claims were independently re-verified by direct file inspection before inclusion in this report.*

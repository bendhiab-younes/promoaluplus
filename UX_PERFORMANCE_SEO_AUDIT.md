# PromoAlu+ — UI/UX, Performance & SEO Audit

**Date:** 2026-07-08 · **Implementation status updated:** 2026-07-15 (Vite/Tailwind migration completed)
**Scope:** `resources/views/layouts/app.blade.php` and all public pages (`home`, `services`, `portfolio`, `about`, `contact`), plus supporting config (`vite.config.js`, `.env`, `public/robots.txt`, controllers).

This report is organized by domain, each ranked most-severe first. Every finding below was verified against the actual source (file + line), not inferred. A prioritized action plan closes the report.

**Status legend:** ✅ Done · 🟡 Partial · ⏳ Deferred (see §5 for why)

> **Implementation summary (2026-07-09):** The safe, high-value fixes were executed and verified end-to-end (all 5 pages + `/sitemap.xml` + `/robots.txt` return 200, no server errors; JSON-LD validated on every page; Arabic renders `dir="rtl"` with the Arabic font conditionally loaded). Three structural/risky items were deliberately deferred — see **§5 Deferred items**.

---

## 1. UI/UX Coherence

| Status | # | Finding | Location | Severity |
|:---:|---|---------|----------|----------|
| ⏳ | 1 | Service/product card accent colors (`rose`, `violet`, `amber`, `yellow`, `teal`, `indigo`, etc. — 9 colors keyed by service slug) have no relation to the documented design system (blue `#1e3a8a`/`#3b82f6`, orange `#f97316`/`#ea580c`, gold `#d4af37`). This is systemic drift from `.github/design_context.md`. | `home.blade.php:415-419`, `services.blade.php:154` | High |
| ✅ | 2 | Every section "badge" (icon + label above a heading) uses hardcoded `mr-1` instead of the logical `me-1`, so in Arabic (RTL) every badge icon renders on the wrong side of its label, sitewide. | `home.blade.php:403,500,631`; `services.blade.php:106`; `portfolio.blade.php:10,207`; `about.blade.php:69,85,106,191`; `contact.blade.php:92,245` | High |
| ✅ | 3 | The same CTA button block is hand-rolled 4 times with raw utility classes instead of the shared `.btn-primary`/`.btn-secondary` classes, and all 4 copies use `mr-2` (not `me-2`) on the icon — duplicated markup *and* an RTL bug baked in 4 places. **(Done 2026-07-15: all 4 call sites consolidated into the `<x-cta-buttons>` component.)** | `resources/views/components/cta-buttons.blade.php` | High |
| ✅ | 4 | The per-service "Request quote" button (repeated once per service card) also uses `mr-2` instead of `me-2`, inconsistent with the hero CTA above it on the same page, which gets it right. | `services.blade.php:266` | High |
| ✅ | 5 | Hero carousel prev/next arrows have no `rtl:rotate-180`, while the product carousel's arrows lower on the same page correctly flip for RTL — inconsistent treatment within a single page. | `home.blade.php:179-184` vs `home.blade.php:511,514` | Medium-High |
| ✅ | 6 | Footer contact icons (map-pin, phone, WhatsApp, mail) use hardcoded `mr-2` instead of `me-2`, so they sit on the wrong side in the Arabic footer. | `layouts/app.blade.php:857,861,865,871` | Medium |
| ✅ | 7 | Contact form success/error banners and the send button manually branch RTL via inline ternaries (`ml-3`/`mr-3`, `-scale-x-100`) instead of logical utilities — fragile and inconsistent. **(Rewritten to logical `me-*`/`ms-*` + `rtl:-scale-x-100`; redundant `flex-row-reverse` ternaries removed, letting `dir=rtl` handle order.)** | `contact.blade.php:100,108,111,231,256,259` | Medium |
| ✅ | 8 | Icon-only button `aria-label`s are inconsistently localized: back-to-top hardcoded French, WhatsApp float hardcoded English. **(Both now use `__()` keys in fr/en/ar.)** | `layouts/app.blade.php:885,892` | Medium |
| ✅ | 9 | "Client Testimonials" section on the portfolio page is a bare `<h2>` with no badge and no intro paragraph, breaking the badge+h2+p header pattern every other section uses. **(Added badge + intro; new `testimonials_badge`/`testimonials_subtitle` keys.)** | `portfolio.blade.php:110-112` | Medium |
| ✅ | 10 | Contact page's hero header has no badge above its `<h1>`, unlike Services, Portfolio, and About. **(Badge added.)** | `contact.blade.php:25-32` | Medium |
| ✅ | 11 | Portfolio testimonial cards use plain `bg-white p-8 rounded-xl shadow-lg` with no hover treatment, inconsistent with `.service-card`/`.portfolio-item`. **(Added hover-lift/shadow/border transition.)** | `portfolio.blade.php:110,116-132` | Low-Medium |
| ✅ | 12 | Section vertical-padding scale is inconsistent: Portfolio's testimonials used a flat `py-20` and Contact's info section stopped at `py-12 md:py-20`. **(Normalized to `py-16 md:py-24` / added `lg:py-24`.)** | `portfolio.blade.php:110`, `contact.blade.php:35` | Low-Medium |
| ⏳ | 13 | A ~50-line "Why Choose Us" section is commented out and left in the template. **(Left intact — this is intentionally-preserved, brand-aligned content, not merely dead code. Your call to re-enable or remove.)** | `home.blade.php:568-617` | Low |
| ⏳ | 14 | A "Certifications" section is similarly commented out and left in place. **(Left intact — same reasoning as #13.)** | `about.blade.php:250-287` | Low |
| ✅ | 15 | `.section-divider`, `.text-gradient`, and `.img-loading` CSS classes are defined globally but never referenced by any markup — dead CSS. **(Confirmed unreferenced anywhere incl. JS; all three + the `loading` keyframe removed.)** | `layouts/app.blade.php:452,458,683` | Low |
| ✅ | 16 | The "our workshop" story image on About has no explicit height/aspect-ratio, risking layout shift. **(Added `width`/`height` + `h-[320px] md:h-[420px] object-cover`.)** | `about.blade.php:92-96` | Low |
| ✅ | 17 | The same decorative SVG dot-pattern background + inline `<style>` is redeclared independently across pages instead of one shared layout-level class. **(Re-checked 2026-07-15: after the page redesigns, the remaining per-page `<style>` blocks are small and page-specific — no duplicated dot-pattern remains. Finding is stale; no action needed.)** | multiple | Low |

---

## 2. Performance

| Status | # | Finding | Location | Severity |
|:---:|---|---------|----------|----------|
| ✅ | 1 | Tailwind is loaded via the browser-JIT `cdn.tailwindcss.com` script (render-blocking, unminified, unpurged, "not for production"). **(Done 2026-07-15: CDN removed; Tailwind v4.3 compiled via Vite — 126 KB CSS / 19.7 KB gzipped vs the ~380 KB CDN script. v3-compat preflight layer added (border color, button cursor, placeholder color); dynamic `from-/to-{color}` gradients safelisted via `@source inline`; latent `scrollbar-hide` class now actually defined.)** | `layouts/app.blade.php:18` | High |
| ✅ | 2 | A full Vite + `@tailwindcss/vite` pipeline is configured but never included via `@vite([...])`, and `public/build` doesn't exist. **(Done — layout now loads `@vite('resources/css/app.css')`; `npm install` + `npm run build` executed; all 5 pages verified serving the compiled asset.)** | `vite.config.js`, `resources/css/app.css`, `public/build` (missing) | High |
| ⏳ | 3 | All content imagery is hotlinked from `images.unsplash.com` with no local fallback. **(Deferred — needs real company photos; content decision.)** | `home.blade.php`, `portfolio.blade.php`, `about.blade.php`, `app/Models/Service.php:128` | High |
| ⏳ | 4 | Service and portfolio images have no `srcset`/`sizes`. **(Deferred — tied to self-hosting imagery, #3.)** | `services.blade.php`, `portfolio.blade.php` | Medium |
| ⏳ | 5 | `.env` routes cache, sessions, and the mail queue through the same SQLite file (single-writer contention). **(Deferred — deployment/environment config.)** | `.env` | Medium |
| ✅ | 6 | The ~685-line inline `<style>` block is duplicated into every page response. **(Done 2026-07-15: entire block moved into `resources/css/app.css` — every page response is ~24 KB lighter; the styles now ship once in the cached, minified bundle. The v3-era `[dir=rtl] .space-x-*` overrides were dropped in the move: Tailwind v4's `space-x` uses logical `margin-inline` properties, so they had become an active RTL double-flip bug.)** | `resources/css/app.css` | Medium |
| ✅ | 7 | `.navbar-scrolled` still applied `backdrop-filter: blur(20px)` on mobile (the mobile override forgot it). **(Added `.navbar-scrolled` to the mobile `backdrop-filter: none` override.)** | `layouts/app.blade.php:90,558-562` | Medium |
| ✅ | 8 | Two separate unthrottled `scroll` handlers (navbar + back-to-top). **(Merged into one passive, `requestAnimationFrame`-throttled handler.)** | `layouts/app.blade.php:979-990, 995-1002` | Low-Medium |
| 🟡 | 9 | The page depends on 5 third-party origins at load time. **(Down to 4 — Tailwind CDN removed 2026-07-15. Remaining: unpkg (Lucide, pinned+SRI), Google Fonts ×2, Unsplash images.)** | `layouts/app.blade.php` | Medium |
| ✅ | 10 | Lucide loaded from `unpkg.com/lucide@latest` — unpinned, no SRI. **(Pinned to `0.544.0` (min build) with an `integrity` SRI hash + `crossorigin` + `defer`.)** | `layouts/app.blade.php:21` | Medium |
| ⏳ | 11 | No query-result caching in `PageController`. **(Deferred — Low; harmless at current catalog size.)** | `app/Http/Controllers/PageController.php` | Low |
| ✅ | 12 | No `<link rel="preload">` for the hero's LCP image. **(Done 2026-07-15: preload with `imagesrcset`/`imagesizes` + `fetchpriority=high` pushed into the head on the home page.)** | `home.blade.php` | Low |
| ✅ | 13 | Google Fonts pulled Noto Sans Arabic for all visitors. **(Now injected only when `app()->getLocale() === 'ar'` — verified absent in en/fr, present in ar.)** | `layouts/app.blade.php:26` | Low |

---

## 3. SEO

For a local aluminum-joinery business whose stated audience is Tunisians, Tunisian expats, and European clients searching in French/Arabic/English, these findings weigh organic discoverability and ranking potential.

| Status | # | Finding | Location | Severity |
|:---:|---|---------|----------|----------|
| ✅ | 1 | No `sitemap.xml` and no `Sitemap:` directive in robots.txt. **(Added dynamic `/sitemap.xml` + `/robots.txt` routes; robots now advertises the sitemap; static robots.txt removed. URLs match the live domain automatically.)** | `public/robots.txt` | High |
| ⏳ | 2 | Session-based locale = one URL per page, zero `hreflang`. **(Deferred — see §5: highest-leverage but most invasive, requires path-based routing.)** | `app/Http/Middleware/SetLocale.php`, `PageController.php` | High |
| ✅ | 3 | No page overrode `@section('meta_description')` — all identical. **(Unique per-page descriptions added in fr/en/ar; verified distinct on all 5 pages.)** | `layouts/app.blade.php:6`; all pages | High |
| ✅ | 4 | `og:title`/`og:description` never overridden. **(Unique per-page OG title/description added.)** | `layouts/app.blade.php:13-14` | High |
| ✅ | 5 | No `og:image`, `og:url`, `og:locale`; no Twitter Card tags. **(All added; `og:locale` mapped fr_FR/ar_TN/en_US; `twitter:card=summary_large_image` + title/description/image.)** | `layouts/app.blade.php:12-15` | High |
| ✅ | 6 | No `<link rel="canonical">`. **(Added `url()->current()` canonical to the layout head.)** | `layouts/app.blade.php` | High |
| ✅ | 7 | No `LocalBusiness`/`Organization` structured data. **(Added sitewide `LocalBusiness` JSON-LD using the footer NAP — validated on every page.)** | `layouts/app.blade.php` (footer) | High |
| ✅ | 8 | Contact FAQ had no `FAQPage` schema. **(Added `FAQPage` JSON-LD built from `$faqs` — validated: FAQPage → Question → Answer.)** | `contact.blade.php:252-263` | Medium |
| ✅ | 9 | No `<meta name="robots">`; no dedup for `?category=` variants. **(Robots meta added earlier. Re-verified 2026-07-15: the earlier "canonical includes query string" note was wrong — Laravel's `url()->current()` strips the query string, so `/portfolio?category=doors` already canonicalizes to `/portfolio`. Fully resolved.)** | `layouts/app.blade.php` | Medium |
| ⏳ | 10 | Imagery hotlinked from Unsplash with generic filenames. **(Deferred — bundled with Perf #3.)** | `home.blade.php`, `portfolio.blade.php`, `about.blade.php` | Medium |
| ✅ | 11 | Portfolio project `alt` text is just the title. **(Done 2026-07-15: dynamic cards now emit title — category, location — localized "PromoAlu+ project" suffix; static fallback cards got the suffix too.)** | `portfolio.blade.php` | Low |
| ✅ | 12 | Logo `alt` is brand-only. **(Done 2026-07-15: both layout logos use the localized `logo_alt` key — e.g. "PromoAlu+ — Menuiserie aluminium et inox en Tunisie".)** | `layouts/app.blade.php` | Low |

**Confirmed correct, no action needed:** `<html lang>`/`dir="rtl"` are properly conditional on the active locale (re-verified after changes); every page has exactly one `<h1>` with logical h2→h3 nesting.

> **Note on SEO #9 (`?category=` dedup) — corrected 2026-07-15:** the original note was mistaken. Laravel's `url()->current()` returns the URL *without* the query string, verified live: `/portfolio?category=doors` emits `<link rel="canonical" href="…/portfolio">`. No dedup problem exists.

---

## 4. Prioritized Action Plan

**Quick wins (low effort, high impact):**
- [x] 1. Unique `@section('meta_description')`, `og_title`, `og_description` per page.
- [x] 2. `og:image`, `og:url`, `og:locale`, Twitter Card tags in the layout head.
- [x] 3. `<link rel="canonical">` in the layout head.
- [x] 4. `sitemap.xml` + `Sitemap:` directive in robots.txt (done as dynamic routes).
- [x] 5. Replace sitewide `mr-*`/`ml-*` icon spacing with logical `me-*`/`ms-*`.
- [x] 6. Extend the mobile `backdrop-filter: none` override to `.navbar-scrolled`.

**Medium effort, high impact:**
- [x] 7. Wire up the Vite pipeline / remove the Tailwind CDN. **✅ Done 2026-07-15 — see Perf #1/#2.**
- [x] 8. `LocalBusiness` JSON-LD in the layout.
- [x] 9. `FAQPage` JSON-LD on the Contact page.
- [x] 10. Consolidate the 4 duplicated CTA button blocks into a shared partial. **✅ Done 2026-07-15 — `<x-cta-buttons>` component.**

**Larger structural investment:**
- [ ] 11. Path-based locales (`/en/...`, `/ar/...`) with `hreflang`. **⏳ Deferred — see §5.**
- [ ] 12. Self-host content imagery. **⏳ Deferred — needs real company photos.**
- [ ] 13. Move `CACHE_STORE` off SQLite. **⏳ Deferred — deployment config.**

**Also completed beyond the original quick-win list:** hero carousel RTL arrow flip, localized floating-button aria-labels, Portfolio testimonials header + card hover, section-padding normalization, About image aspect-ratio (CLS), dead-CSS removal, single rAF-throttled scroll handler, Lucide version pin + SRI, conditional Arabic font, `<meta name="robots">`.

---

## 5. Deferred items (rationale)

1. ~~**Vite / Tailwind build swap (Perf #1, #2, #6, #9)**~~ — **Completed 2026-07-15.** Tailwind v4.3 now compiled via Vite (19.7 KB gzipped CSS); v3-compat preflight layer added; dynamic gradient classes safelisted; all 5 pages + RTL verified against the built asset. Note: **deploys must now run `npm run build`** (`public/build` is gitignored).
2. **Path-based locales + `hreflang` (SEO #2)** — the highest-leverage SEO fix, but invasive: it rewrites routing and every `route()` call and needs a URL-structure/redirect decision. Warrants its own focused effort.
3. **Self-hosting imagery (Perf #3, #4; SEO #10)** — needs real photographs of the company's actual work (a content decision) plus bulk asset handling; stock Unsplash photos carry little SEO value even if self-hosted.
4. **`.env` cache/session/queue off SQLite (Perf #5, #11)** — environment/deployment configuration, not application code.
5. **Commented-out content sections (UI #13, #14)** — intentionally-preserved, brand-aligned content (commented, not deleted); left intact rather than discarding content the developer chose to keep.
6. ~~**Low-severity polish (UI #17, Perf #12, SEO #11, #12)**~~ — **Completed 2026-07-15** (UI #17 turned out to be stale after the redesigns; the other three were implemented).

---

*Methodology: three focused read-only audits (UI/UX, performance, SEO) run in parallel, each domain's high-severity claims independently re-verified by direct file inspection. Fixes executed 2026-07-09 and verified by serving the app and inspecting rendered output across all 5 pages in default and Arabic (RTL) locales.*

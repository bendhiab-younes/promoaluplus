# Client Feedback Implementation Plan (Updated)

## Summary (current verification)

| Status | Count | Items |
|--------|-------|-------|
| ✅ Resolved | 9 | Services list order, Prénom field, Project types, Duplicate contact not present, Form budget removed, Carousel dots & CTA z-index, Small wording fixes (FR/EN/AR), Email contact/address moved to SiteSetting, Full test suite passing |
| ⚠️ Partial / Needs follow-up | 1 | Replace portfolio location labels if needed |
| 🔴 Unresolved | 3 | Hero background images, Services photos, Partner logos/external images |

---

## Findings mapped to code (evidence & status)

1. Bullets/dots hide hero text (dot size / z-index conflict)
   - File: resources/views/pages/home.blade.php
   - Evidence: dots (carousel-dot) size & wrapper z adjusted; action buttons now rendered above controls.
   - Status: ✅ Resolved — z-index and dot size updates applied; please QA on mobile/tablet.

2. White bar overlapping contact / WhatsApp
   - File: resources/views/pages/home.blade.php
   - Evidence: buttons container and dots stacking were updated to avoid overlap.
   - Status: ✅ Resolved

3. Hero backgrounds are generic Unsplash (not Tunisia)
   - File: resources/views/pages/home.blade.php lines with img src (examples: lines ~21,61,94,127)
   - Status: 🔴 Unresolved — replace with customer project images or SiteSetting keys.

4. Services list ordering
   - File: app/Support/CanonicalServiceCatalog.php
   - Evidence: MESSAGE_KEYS_BY_SLUG contains canonical order (kitchen, doors, windows, ...)
   - Status: ✅ Resolved — used across contact/footer/home.

5. Non-Tunisian / inappropriate images & partner logos
   - Files: resources/views/pages/services.blade.php (gallery fallbacks), resources/views/pages/portfolio.blade.php (fallbacks), resources/views/pages/home.blade.php (partners list)
   - Status: 🔴 Unresolved — need owned photos; remove external logos or host local approved logos.

6. Footer / Contact address and phone
   - Files: resources/views/layouts/app.blade.php (footer), resources/views/pages/contact.blade.php (contact block)
   - Evidence: UI reads SiteSetting contact_address/phone/whatsapp; email template quote-received now also uses SiteSetting for company/address/phone/email.
   - Status: ✅ Resolved

7. Budget field removal
   - Files: contact form no longer shows budget field; emails and PDFs do not render budget_range.
   - Status: ✅ Resolved

8. First name (Prénom)
   - File: resources/views/pages/contact.blade.php includes first_name input
   - Status: ✅ Resolved

9. Project type list sync
   - File: CanonicalServiceCatalog::translatedOptions() used in contact and footer
   - Status: ✅ Resolved

---

## Recommended next steps (small, actionable tasks)

1. Images & Partners
   - Replace hero Unsplash src with SiteSetting-managed URLs or storage paths (SiteSetting keys: hero_slide1_image, etc.). File: resources/views/pages/home.blade.php. (P2)
   - Remove or replace external partner logos with local copies or company-approved assets. File: resources/views/pages/home.blade.php (partners list). (P2)
   - Populate project images in DB/storage so portfolio shows real photos; remove unsplash fallbacks once real images exist. File: resources/views/pages/portfolio.blade.php. (P2-P3)

2. Slide ordering clarification
   - Confirm desired human-readable order (1-based) and adjust data-order attributes accordingly in resources/views/pages/home.blade.php. Current code sorts by data-order numeric. (P1)

3. QA / Deployment
   - Create branch: feature/wording-images-contact-fixes
   - Run: vendor/bin/pint --dirty && php artisan test --compact
   - QA: manual check on homepage, services, portfolio, contact, and email outputs. (P0)

---

## Exact remaining hardcoded occurrences (evidence from repo)
- resources/views/pages/portfolio.blade.php: sample project captions include "La Marsa, Tunis" and "Carthage, Tunis" — review whether these should be updated to Sousse or left as-is depending on the project location.

---

## Plan maintenance
- Update this file when tasks move between Resolved / Partial / Unresolved.
- Add owner initials and target dates in the TODO table below for tracking.

## TODO (to insert into tracker)
- replace-hero-images: obtain client images, store in storage/app/public/projects, update SiteSettings. (owner: @client)
- partners-logos: remove external links, add local assets with license. (owner: @frontend)
- portfolio-location-review: audit portfolio entries and update location labels if incorrect (owner: @content)

---

*Plan verified: 2026-05-17 — updated to reflect repository findings*

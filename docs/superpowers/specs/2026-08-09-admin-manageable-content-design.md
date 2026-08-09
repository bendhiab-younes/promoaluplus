# Admin-Manageable Site Content — Design

**Date:** 2026-08-09
**Status:** Approved for planning

## Goal

Make the public site's content editable from the Filament admin panel: service images
(with previews and ordering), the Réalisations/portfolio page (including a
show/hide toggle), the homepage hero slides, and testimonials. Remove the hardcoded
content and half-finished "hidden" states that currently make parts of the site
uneditable without a developer.

## Current State (verified 2026-08-09)

| Finding | Evidence |
|---|---|
| Service images are URL text inputs, not uploads | `ServiceResource.php:250`, `:264` |
| `->url()` renders `<input type="url">`, which rejects the root-relative paths already in the DB, blocking form submit even when the admin only reordered | `ServiceResource.php:250`, `:264`; 6 of 9 services store `/images/services/...` |
| Service ordering already works — no change needed | `ServiceResource.php:388` `defaultSort`, `:401` `reorderable('sort_order')`, `:268` gallery repeater `reorderable()` |
| `public/storage` symlink does not exist, so the existing `ProjectResource` FileUpload has never worked | `ls -la public/` |
| `projects`, `project_types`, `testimonials` tables are empty (0 rows) | `Project::count()`, `ProjectType::count()`, `Testimonial::count()` |
| Portfolio page renders hardcoded fake projects when the DB is empty | `portfolio.blade.php:65-95` ("Villa Moderne", "Résidence Carthage", "Immeuble Commercial") |
| Homepage renders 3 hardcoded testimonials | `home.blade.php:589-636` |
| Portfolio is only half-hidden: nav commented out, footer link still live | `layouts/app.blade.php:55`, `:84` commented; `:194` live |
| Hero slides 2–4 are hardcoded lang strings + fixed image paths; only slide 1's text comes from settings | `home.blade.php:66-165` |
| Project categories hardcoded in 3 places while an unused `ProjectType` model + resource exist | `ProjectResource.php` form Select + table filter, `portfolio.blade.php:28-44` |
| `PageController@contact` overwrites FAQ #2 from lang files every request, discarding admin edits | `PageController.php` `contact()` |
| Service images exist in three path shapes simultaneously | 6× `/images/services/...`, 3× external `https://...` |
| Local service images have `-thumb` siblings, used by the gallery | `services.blade.php:164`; `public/images/services/doors/doors-01-thumb.jpeg` |

## Out of Scope

Making the static UI strings in `lang/{fr,en,ar}` database-editable. Button labels,
nav items, section headings and SEO strings stay in code. This is a
translation-management subsystem, not a feature, and the user explicitly declined it.

---

## 1. Foundation: Image Storage

Everything image-related depends on this, so it lands first.

### 1.1 The `uploads` disk

Add to `config/filesystems.php`:

```php
'uploads' => [
    'driver' => 'local',
    'root' => public_path('uploads'),
    'url' => '/uploads',
    'visibility' => 'public',
    'throw' => false,
],
```

Rationale: no `storage:link` symlink. The project ships an Apache `.htaccess` and is
destined for shared hosting, where symlinks are frequently unavailable or broken on
deploy. Files land directly under the web root.

Add `/public/uploads` to `.gitignore`. Deploys that replace `public/` wholesale must
preserve or re-sync this directory — call this out in the README.

Directory layout: `uploads/services/`, `uploads/projects/`, `uploads/hero/`,
`uploads/testimonials/`, `uploads/settings/`.

### 1.2 `App\Support\MediaPath`

A single resolver, because three path shapes will coexist in the database
indefinitely (external URLs can never be migrated away without downloading them).

```php
MediaPath::url(?string $value): ?string
```

- empty/null → `null`
- starts with `http://`, `https://`, or `//` → returned unchanged
- starts with `/` → returned unchanged (already web-root-relative)
- otherwise → `asset('uploads/'.$value)`

```php
MediaPath::thumb(?string $value): ?string
```

Returns the `-thumb` sibling **only if that file actually exists** — checked against
`public_path()` for root-relative legacy values and against the uploads disk for
disk-relative ones. External URLs never get a thumb. This preserves the existing
thumbnail optimisation both before and after `media:import` runs, while guaranteeing
freshly-uploaded files never produce a 404.

`Service::getFeaturedImage()` and `getGalleryImages()` resolve through `MediaPath`.
`services.blade.php:164`'s `str_starts_with($img, '/images/services/')` check is
replaced by `MediaPath::thumb()`.

### 1.3 `php artisan media:import`

One-time, idempotent, non-destructive:

1. For every `Service.image` and `Service.gallery[]` value beginning with
   `/images/services/`: copy the file **and its `-thumb` sibling** into
   `uploads/services/{slug}/`, then rewrite the DB value to the disk-relative path.
2. For every external `https://` value: attempt download into the same location and
   rewrite. On any failure (timeout, 404, no network), leave the value untouched.
3. Print a summary listing every value that could not be imported, so the admin knows
   exactly which services need a manual re-upload.

Originals under `public/images/` are left in place. Because `MediaPath` handles all
three shapes, the public site renders correctly before, during, and after the import.

---

## 2. Services

### 2.1 Fix the reorder-blocks-submit bug

Root cause: `->url()` sets `type="url"` on the input. Values like
`/images/services/doors/doors-01.jpeg` fail the browser's native URL validation, so
Chrome refuses to submit and reports an invalid form control — even when the admin
changed nothing but the drag order.

Fix: replace both URL text inputs with `FileUpload`. The `type="url"` inputs cease to
exist, so the failure mode is gone by construction rather than by validation tweak.

### 2.2 Form changes (`ServiceResource`, Médias tab)

- `image` → `FileUpload` on the `uploads` disk, `directory('services')`, `->image()`,
  `->imageEditor()`, `->imagePreviewHeight('150')`.
- `gallery` → `FileUpload::make('gallery')->multiple()->reorderable()
  ->panelLayout('grid')->appendFiles()->image()->imageEditor()`. Drag-to-reorder real
  thumbnails instead of a list of URL strings. Satisfies both "manage the images" and
  "manage their order of appearing".
- `ImageColumn::make('image')` in the list table resolves through `MediaPath` so
  legacy and uploaded paths both render.

### 2.3 Explicitly unchanged

Service ordering. `->reorderable('sort_order')` at `ServiceResource.php:401` and
`defaultSort('sort_order')` at `:388` already provide drag-to-reorder. Verify in the
browser, then leave it alone.

---

## 3. Réalisations / Portfolio

### 3.1 Visibility toggle

New boolean setting `portfolio_enabled`, edited via a new **"Pages & visibilité"** tab
on the Site Settings page. Shared to every view (e.g. `View::share('portfolioEnabled', …)`
in `AppServiceProvider`), reading through `SiteSetting::get()` which is already
cached forever and invalidated on save.

Every surface, not just the two currently commented out:

| Surface | Today | After |
|---|---|---|
| `layouts/app.blade.php:55` mobile nav | commented out | `@if($portfolioEnabled)` |
| `layouts/app.blade.php:84` desktop nav | commented out | `@if($portfolioEnabled)` |
| `layouts/app.blade.php:194` footer | **live — leaks today** | `@if($portfolioEnabled)` |
| `home.blade.php:122` hero slide 3 CTA | → portfolio | → services when off, with label swapped to `view_all_services` |
| `home.blade.php:728` CTA secondary | → portfolio | → contact when off, label `contact_us` |
| `about.blade.php:319` CTA secondary | → portfolio | → contact when off, label `contact_us` |
| `services.blade.php:379` CTA secondary | → portfolio | → contact when off, label `contact_us` |
| `PageController@portfolio` | always reachable | `abort_unless(...)` → 404 when off |
| `sitemap.xml` `$pages` array | hardcodes `portfolio` | conditional |

The route guard lives in `PageController@portfolio` as `abort_unless(...)`, keeping the
single-use logic next to its only consumer.

### 3.2 Content

- Delete the hardcoded demo projects at `portfolio.blade.php:65-95`. Replace with a
  translated empty state. Turning the toggle on with an empty database must not show
  invented content the admin cannot edit.
- `ProjectResource` image fields → `FileUpload` on the `uploads` disk with previews,
  `imageEditor()`, and a reorderable multiple-file gallery, matching services.
- `ImageColumn` resolves through `MediaPath`.

### 3.3 Wire up `ProjectType`

The model and its Filament resource already exist but are empty stubs, while
categories are hardcoded in three places.

- Add `$fillable` to `ProjectType`; add `scopeActive()` and `scopeOrdered()`.
- Migration converting `project_types.name` from `string` to `json` (translatable
  fr/en/ar), plus a `getTranslatedName()` accessor mirroring the other content models.
- `ProjectTypeResource` form: 3-language name, slug, colour, icon, order, active
  toggle; table with `reorderable('order')`.
- Seed the 3 existing categories (`windows`, `doors`, `facades`) with their current
  `lang/*` labels, so nothing visibly changes.
- `Project.category` stays a `string` holding a `project_types.slug` — no foreign key
  migration. `ProjectResource`'s Select options and table filter, and the portfolio
  filter bar at `portfolio.blade.php:28-44`, all read from `ProjectType::active()->ordered()`.

---

## 4. Hero Slides

### 4.1 Model

New `hero_slides` table and `HeroSlide` model:

| Column | Type | Notes |
|---|---|---|
| `image` | string | uploads-disk path |
| `badge`, `title`, `highlight`, `description` | json | fr/en/ar, cast to `array` |
| `cta_type` | string | `quote`, `services`, `portfolio`, `contact`, `custom`, `none` |
| `cta_url` | string, nullable | used when `cta_type = custom` |
| `cta_label` | json, nullable | fr/en/ar |
| `accent_color` | string | matches the existing per-slide highlight colours |
| `image_fit` | string | `cover` or `contain` |
| `image_zoom` | integer | percent, 100–200 |
| `focal_x`, `focal_y` | integer | percent, 0–100 |
| `is_active` | boolean | |
| `sort_order` | integer | |

Standard `getTranslatedX()` accessors and `scopeActive()` / `scopeOrdered()`, matching
`Service` and `Project`.

### 4.2 Image framing controls

Two independent layers:

- **Destructive** — `FileUpload->imageEditor()` with preset aspect ratios. Crops and
  rotates the stored file itself at upload time.
- **Non-destructive** — per-slide display settings, changeable at any time without
  re-uploading:
  - `image_fit`: *Remplir* (`object-cover`, fills the frame and crops) or *Entier*
    (`object-contain`, whole image visible over a blurred duplicate backdrop — the
    same technique already used in the services gallery).
  - `image_zoom`: 100–200% slider, applied as a CSS `transform: scale()` inside the
    slide's `overflow-hidden` frame.
  - `focal_x` / `focal_y`: applied as `object-position`, so a zoomed image crops
    toward what matters instead of dead centre.

Values below 100% zoom are not offered: on a `cover` image they would reveal empty
gutters. *Entier* is the correct control for "show me the whole image".

### 4.3 Filament resource and Blade

- `HeroSlideResource` with the fields above, `reorderable('sort_order')`, an
  `ImageColumn` preview, and an `is_active` toggle column.
- Seeder reproducing the current 4 slides exactly — images from
  `public/images/hero/slide-{1..4}.jpeg`, text pulled from `lang/{fr,en,ar}` for
  slides 2–4 and from the existing `hero_*` site settings for slide 1. Slide 4's
  `{{ stats_years }}+` interpolation is baked to its current literal value; the admin
  edits it as plain text afterwards.
- `home.blade.php` replaces the four near-identical hardcoded blocks with a loop over
  `HeroSlide::active()->ordered()->get()`. The carousel JS keys off slide count and dot
  indicators — verify it still works with 1, 4, and 6 slides.
- Remove the now-redundant "Accueil - Hero" tab from the Site Settings page: slide 1
  must not be editable in two places. The underlying `hero_*` settings rows are left in
  the database, unread.

**Acceptance:** with the seeder run and no admin edits, the rendered homepage hero is
visually identical to today's.

---

## 5. Testimonials

- Seed the 3 real testimonials from `lang/*` `testimonial_1..3` into the empty
  `testimonials` table, with client name, location, and rating.
- Delete the hardcoded fallback block at `home.blade.php:589-636`; render `@forelse`
  over the DB collection with a translated empty state.
- `TestimonialResource`: `client_photo` → `FileUpload` with preview on the uploads
  disk; `reorderable('sort_order')`; `ImageColumn` via `MediaPath`.

## 6. FAQ Fix

Remove the `sort_order === 2` lang-file override in `PageController@contact`. Ensure
the seeded FAQ #2 row already contains the current `messages.faq_q2` / `faq_a2` text
in all three locales, so removing the override changes nothing visible while making
the record editable.

---

## Testing

PHPUnit feature tests (per repo convention — not Pest):

1. **Portfolio toggle off** — `/portfolio` returns 404; homepage, about, services and
   footer contain no `route('portfolio')` link; sitemap omits it.
2. **Portfolio toggle on** — route returns 200; nav and footer links present; sitemap
   includes it.
3. **Service save-after-reorder-only** — regression test for the reported bug: load a
   service whose gallery holds legacy `/images/...` paths, submit with only the order
   changed, assert a successful save and the new order persisted.
4. **`MediaPath` unit test** — all three input shapes plus null; `thumb()` with and
   without an on-disk `-thumb` sibling.
5. **Hero slides render from the DB** — homepage shows N slides for N active records;
   inactive slides excluded; ordering respected.
6. **Empty states** — empty `projects` table renders the empty state, not the removed
   demo markup; empty `testimonials` likewise.
7. **FAQ #2 is editable** — updating the record changes what the contact page renders.

Run `vendor/bin/pint --dirty` before finalising.

## Risks

| Risk | Mitigation |
|---|---|
| `public/uploads` wiped by a deploy that replaces `public/` | Documented in README; directory is gitignored, not gitremoved |
| External image URLs unreachable during `media:import` | Command leaves them untouched and reports them; `MediaPath` keeps passing them through |
| Hero carousel JS breaks with a variable slide count | Explicitly tested at 1, 4, and 6 slides |
| Admin opens a service whose image failed to import, saves, and wipes the value | `media:import` output names them; re-upload is the documented follow-up |
| Two sources of truth for hero slide 1 | Site Settings hero tab removed in the same change |

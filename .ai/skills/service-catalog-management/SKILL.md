---
name: service-catalog-management
description: Work on the multilingual service catalog, JSON content, galleries, and seeding workflow for this Laravel app.
user-invocable: true
---

# Service Catalog Management

Use this skill when editing the project’s service content, galleries, or seeders.

## When to use this skill
- Updating `database/seeders/content/services.json`
- Updating `service_images.json`
- Editing `database/seeders/ServiceSeeder.php`
- Adding or syncing new services, translations, or galleries

## Project Rules
- Keep `fr`, `en`, and `ar` sections aligned for every service.
- Preserve existing slugs unless a route or model change requires a rename.
- Keep gallery arrays valid and ensure image lists stay arrays of URLs.
- Prefer updating the JSON content source first, then adjust the seeder to match.
- Keep service titles, descriptions, and features consistent across languages.

## Seeder Workflow
- The services seeder reads from the content JSON and the centralized image list.
- If image overrides exist in `service_images.json`, keep them in sync with the service slugs.
- Verify the seeder still handles fallback galleries safely.

## Useful Commands
- `php artisan db:seed --class=ServiceSeeder --no-interaction`
- `php artisan migrate:fresh --seed --no-interaction` only when a full reset is acceptable
- `vendor/bin/pint --dirty` after PHP changes

## Things to Check
- JSON remains valid after edits.
- Every language entry has the same service keys.
- New images are reachable and appropriate for the service.
- Seeder output still matches the site content shown in the UI.

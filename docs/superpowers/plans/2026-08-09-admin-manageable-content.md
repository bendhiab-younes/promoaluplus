# Admin-Manageable Site Content Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make service images, the Réalisations page (content + on/off toggle), the homepage hero slides, and testimonials fully editable from the Filament admin panel, and fix the form bug that blocks saving a service after reordering its images.

**Architecture:** A `public/uploads` filesystem disk (no `storage:link` symlink) plus a single `App\Support\MediaPath` resolver that handles the three image-path shapes which will coexist in the database: external `https://` URLs, legacy root-relative `/images/...` paths, and new disk-relative upload paths. Every single-image field becomes a `FileUpload` + optional `image_url` text pair so a `FileUpload` can never silently wipe a value it cannot find on its disk. Page visibility is a `SiteSetting` row exposed to Blade through a view composer.

**Tech Stack:** Laravel 12, Filament 3, SQLite, Blade + Alpine.js + Tailwind v4 (Vite), PHPUnit (not Pest), Pint.

**Design spec:** `docs/superpowers/specs/2026-08-09-admin-manageable-content-design.md`

---

## Conventions for every task

- Run `vendor/bin/pint --dirty` before each commit.
- Tests are **PHPUnit, not Pest**. Feature tests extend `Tests\TestCase` and use `Illuminate\Foundation\Testing\RefreshDatabase`. The test DB is in-memory SQLite (see `phpunit.xml`).
- Run a single test file: `php artisan test --compact tests/Feature/Foo.php`
- Run one test: `php artisan test --compact --filter=test_name`
- Scaffold with `php artisan make:* --no-interaction`; use Filament's own `make:` commands for Filament files.
- Commit after every task. Do not batch commits.

---

## File Structure

**Created:**

| Path | Responsibility |
|---|---|
| `app/Support/MediaPath.php` | Resolve any stored image value to a public URL; find `-thumb` siblings |
| `app/Models/Concerns/HasImageSource.php` | `imageSrc()` — pick between the `image` upload column and the `image_url` fallback |
| `app/Console/Commands/MediaImportCommand.php` | One-time import of legacy + external images onto the uploads disk |
| `app/Models/HeroSlide.php` | Hero carousel slide content model |
| `app/Filament/Resources/HeroSlideResource.php` (+ `Pages/`) | Hero slide admin CRUD |
| `app/Providers/ViewServiceProvider.php` | View composer sharing `$portfolioEnabled` to the layout |
| `database/seeders/HeroSlideSeeder.php` | Seeds the current 4 slides verbatim |
| `database/seeders/TestimonialSeeder.php` | Seeds the 3 currently-hardcoded testimonials |
| `database/seeders/ProjectTypeSeeder.php` | Seeds windows / doors / facades |
| `tests/Unit/MediaPathTest.php` | Path resolution unit tests |
| `tests/Feature/AdminContentManagementTest.php` | Filament form round-trip regression tests |
| `tests/Feature/PortfolioVisibilityTest.php` | Toggle on/off across every surface |
| `tests/Feature/HeroSlideTest.php` | Hero renders from the DB |

**Modified:** `config/filesystems.php`, `.gitignore`, `bootstrap/providers.php`, `app/Models/{Service,Project,Testimonial,ProjectType}.php`, `app/Http/Controllers/PageController.php`, `routes/web.php`, `app/Filament/Resources/{Service,Project,ProjectType,Testimonial}Resource.php`, `app/Filament/Pages/SiteSettings.php`, `database/seeders/DatabaseSeeder.php`, `resources/views/layouts/app.blade.php`, `resources/views/pages/{home,portfolio,services,about}.blade.php`, `tests/Feature/PublicSiteFeatureTest.php`, `tests/Feature/SiteCoherenceTest.php`.

---

# PHASE 1 — Foundation

## Task 1: The `uploads` disk and `MediaPath`

**Files:**
- Modify: `config/filesystems.php:31-63`
- Modify: `.gitignore`
- Create: `app/Support/MediaPath.php`
- Test: `tests/Unit/MediaPathTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Unit/MediaPathTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Support\MediaPath;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MediaPathTest extends TestCase
{
    public function test_null_and_empty_values_resolve_to_null(): void
    {
        $this->assertNull(MediaPath::url(null));
        $this->assertNull(MediaPath::url(''));
        $this->assertNull(MediaPath::url('   '));
    }

    public function test_external_urls_pass_through_untouched(): void
    {
        $this->assertSame(
            'https://images.unsplash.com/photo-1.jpg',
            MediaPath::url('https://images.unsplash.com/photo-1.jpg')
        );
        $this->assertSame('http://example.test/a.png', MediaPath::url('http://example.test/a.png'));
        $this->assertSame('//cdn.example.test/a.png', MediaPath::url('//cdn.example.test/a.png'));
    }

    public function test_root_relative_legacy_paths_pass_through_untouched(): void
    {
        $this->assertSame(
            '/images/services/doors/doors-01.jpeg',
            MediaPath::url('/images/services/doors/doors-01.jpeg')
        );
    }

    public function test_disk_relative_paths_resolve_against_the_uploads_directory(): void
    {
        $this->assertSame(
            asset('uploads/services/doors/doors-01.jpeg'),
            MediaPath::url('services/doors/doors-01.jpeg')
        );
    }

    public function test_thumb_returns_the_thumb_sibling_when_the_file_exists(): void
    {
        $dir = public_path('uploads/testing');
        File::ensureDirectoryExists($dir);
        File::put($dir.'/pic.jpeg', 'x');
        File::put($dir.'/pic-thumb.jpeg', 'x');

        $this->assertSame(asset('uploads/testing/pic-thumb.jpeg'), MediaPath::thumb('testing/pic.jpeg'));

        File::deleteDirectory($dir);
    }

    public function test_thumb_falls_back_to_the_full_image_when_no_sibling_exists(): void
    {
        $dir = public_path('uploads/testing');
        File::ensureDirectoryExists($dir);
        File::put($dir.'/solo.jpeg', 'x');

        $this->assertSame(asset('uploads/testing/solo.jpeg'), MediaPath::thumb('testing/solo.jpeg'));

        File::deleteDirectory($dir);
    }

    public function test_thumb_never_rewrites_an_external_url(): void
    {
        $url = 'https://images.unsplash.com/photo-1.jpg';
        $this->assertSame($url, MediaPath::thumb($url));
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact tests/Unit/MediaPathTest.php`
Expected: FAIL — `Class "App\Support\MediaPath" not found`.

- [ ] **Step 3: Add the disk**

In `config/filesystems.php`, inside the `'disks' => [...]` array, after the `'public'` block and before `'s3'`:

```php
        'uploads' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'url' => '/uploads',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],
```

- [ ] **Step 4: Ignore the uploads directory**

In `.gitignore`, directly below the existing `/public/storage` line, add:

```
/public/uploads
```

- [ ] **Step 5: Write `MediaPath`**

Create `app/Support/MediaPath.php`:

```php
<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Resolves the three image-path shapes that coexist in this database:
 *
 *   1. External absolute URLs   — "https://images.unsplash.com/..."
 *   2. Legacy root-relative     — "/images/services/doors/doors-01.jpeg"
 *   3. Uploads-disk relative    — "services/doors/doors-01.jpeg"
 *
 * Only shape 3 is produced by new admin uploads; the other two predate the
 * admin panel and are migrated opportunistically by `php artisan media:import`.
 */
class MediaPath
{
    public static function url(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (self::isExternal($value) || Str::startsWith($value, '/')) {
            return $value;
        }

        return asset('uploads/'.ltrim($value, '/'));
    }

    /**
     * The "-thumb" sibling if it actually exists on disk, otherwise the full
     * image. Legacy service images ship with pre-generated thumbnails; admin
     * uploads do not, and must never produce a 404.
     */
    public static function thumb(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || self::isExternal($value)) {
            return self::url($value === '' ? null : $value);
        }

        $thumbValue = preg_replace('/(\.jpe?g|\.png|\.webp)$/i', '-thumb$1', $value);

        if ($thumbValue === null || $thumbValue === $value) {
            return self::url($value);
        }

        return self::exists($thumbValue) ? self::url($thumbValue) : self::url($value);
    }

    public static function isExternal(string $value): bool
    {
        return Str::startsWith($value, ['http://', 'https://', '//']);
    }

    /**
     * Absolute filesystem check. Root-relative values live under public/,
     * disk-relative values under public/uploads/.
     */
    public static function exists(string $value): bool
    {
        if (self::isExternal($value)) {
            return false;
        }

        $path = Str::startsWith($value, '/')
            ? public_path(ltrim($value, '/'))
            : public_path('uploads/'.$value);

        return is_file($path);
    }
}
```

- [ ] **Step 6: Run the test to verify it passes**

Run: `php artisan test --compact tests/Unit/MediaPathTest.php`
Expected: PASS, 7 tests.

- [ ] **Step 7: Format and commit**

```bash
vendor/bin/pint --dirty
git add config/filesystems.php .gitignore app/Support/MediaPath.php tests/Unit/MediaPathTest.php
git commit -m "feat(media): add uploads disk and MediaPath resolver"
```

---

## Task 2: `image_url` fallback column and `HasImageSource`

The `FileUpload` component dehydrates to `null` when it cannot find the stored value on its disk. Without a second column, editing a service whose image is an Unsplash URL would delete that image.

**Files:**
- Create: `database/migrations/<timestamp>_add_image_url_to_content_tables.php`
- Create: `app/Models/Concerns/HasImageSource.php`
- Modify: `app/Models/Service.php`, `app/Models/Project.php`, `app/Models/Testimonial.php`
- Test: `tests/Unit/MediaPathTest.php` (add a class), or a new `tests/Feature/ImageSourceTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/ImageSourceTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImageSourceTest extends TestCase
{
    use RefreshDatabase;

    private function service(array $attributes = []): Service
    {
        return Service::create(array_merge([
            'slug' => 'windows',
            'title' => ['fr' => 'Fenêtres'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'is_active' => true,
            'sort_order' => 1,
        ], $attributes));
    }

    public function test_the_uploaded_image_wins_when_both_columns_are_set(): void
    {
        $service = $this->service([
            'image' => 'services/windows/a.jpeg',
            'image_url' => 'https://images.unsplash.com/photo-1.jpg',
        ]);

        $this->assertSame(asset('uploads/services/windows/a.jpeg'), $service->imageSrc());
    }

    public function test_the_external_url_is_used_when_no_file_is_uploaded(): void
    {
        $service = $this->service([
            'image' => null,
            'image_url' => 'https://images.unsplash.com/photo-1.jpg',
        ]);

        $this->assertSame('https://images.unsplash.com/photo-1.jpg', $service->imageSrc());
    }

    public function test_null_is_returned_when_neither_column_is_set(): void
    {
        $this->assertNull($this->service()->imageSrc());
    }

    public function test_featured_image_prefers_the_first_gallery_entry(): void
    {
        $service = $this->service([
            'image' => 'services/windows/main.jpeg',
            'gallery' => ['services/windows/g1.jpeg', 'services/windows/g2.jpeg'],
        ]);

        $this->assertSame(asset('uploads/services/windows/g1.jpeg'), $service->getFeaturedImage());
    }

    public function test_gallery_images_are_returned_as_resolved_urls(): void
    {
        $service = $this->service([
            'gallery' => ['services/windows/g1.jpeg', 'https://cdn.test/x.jpg', '/images/services/legacy.jpeg'],
        ]);

        $this->assertSame([
            asset('uploads/services/windows/g1.jpeg'),
            'https://cdn.test/x.jpg',
            '/images/services/legacy.jpeg',
        ], $service->getGalleryImages());
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact tests/Feature/ImageSourceTest.php`
Expected: FAIL — no such column `image_url`.

- [ ] **Step 3: Create the migration**

Run: `php artisan make:migration add_image_url_to_content_tables --no-interaction`

Replace the generated file's body with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = ['services', 'projects', 'testimonials'];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->string('image_url')->nullable()->after('image');
            });
        }

        // Move any existing absolute URL out of the upload column so that a
        // FileUpload component can never fail to hydrate it and wipe it on save.
        foreach (['services', 'projects'] as $table) {
            DB::table($table)
                ->where('image', 'like', 'http%')
                ->update([
                    'image_url' => DB::raw('image'),
                    'image' => null,
                ]);
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('image_url');
            });
        }
    }
};
```

Note: `testimonials` stores its image in `client_photo`, not `image`; the column is added for symmetry but the backfill loop deliberately skips it.

- [ ] **Step 4: Create the trait**

Create `app/Models/Concerns/HasImageSource.php`:

```php
<?php

namespace App\Models\Concerns;

use App\Support\MediaPath;

/**
 * Pairs an uploaded-file column with an external-URL fallback column so no
 * stored image is ever unreachable from the Filament form.
 */
trait HasImageSource
{
    public function imageSrc(): ?string
    {
        $value = filled($this->image) ? $this->image : $this->image_url;

        return MediaPath::url($value);
    }
}
```

- [ ] **Step 5: Wire the models**

In `app/Models/Service.php`: add `use App\Models\Concerns\HasImageSource;` to the imports, `use HasImageSource;` as the first line inside the class body, and add `'image_url'` to `$fillable` directly after `'image'`.

Replace `getGalleryImages()` and `getFeaturedImage()` with:

```php
    /**
     * Gallery image URLs, falling back to the main image when empty.
     *
     * @return array<int, string>
     */
    public function getGalleryImages(): array
    {
        $gallery = array_values(array_filter(
            array_map(
                static fn ($image) => is_string($image) ? MediaPath::url($image) : null,
                $this->gallery ?? []
            )
        ));

        if ($gallery === [] && ($main = $this->imageSrc()) !== null) {
            return [$main];
        }

        return $gallery;
    }

    public function getFeaturedImage(): ?string
    {
        return $this->getGalleryImages()[0] ?? $this->imageSrc();
    }
```

Add `use App\Support\MediaPath;` to the imports.

In `app/Models/Project.php`: add the same `use HasImageSource;` and `'image_url'` in `$fillable` after `'image'`.

In `app/Models/Testimonial.php`: add `'image_url'` to `$fillable` after `'client_photo'`, and add:

```php
    public function photoSrc(): ?string
    {
        $value = filled($this->client_photo) ? $this->client_photo : $this->image_url;

        return \App\Support\MediaPath::url($value);
    }
```

- [ ] **Step 6: Run the test to verify it passes**

Run: `php artisan test --compact tests/Feature/ImageSourceTest.php`
Expected: PASS, 5 tests.

- [ ] **Step 7: Format and commit**

```bash
vendor/bin/pint --dirty
git add database/migrations app/Models tests/Feature/ImageSourceTest.php
git commit -m "feat(media): add image_url fallback column and HasImageSource"
```

---

## Task 3: `php artisan media:import`

**Files:**
- Create: `app/Console/Commands/MediaImportCommand.php`
- Test: `tests/Feature/MediaImportTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/MediaImportTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MediaImportTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        File::deleteDirectory(public_path('images/mediaimporttest'));
        File::deleteDirectory(public_path('uploads/services'));

        parent::tearDown();
    }

    public function test_it_copies_legacy_local_images_and_rewrites_the_database(): void
    {
        $source = public_path('images/mediaimporttest');
        File::ensureDirectoryExists($source);
        File::put($source.'/pic.jpeg', 'image-bytes');
        File::put($source.'/pic-thumb.jpeg', 'thumb-bytes');

        $service = Service::create([
            'slug' => 'windows',
            'title' => ['fr' => 'Fenêtres'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'image' => '/images/mediaimporttest/pic.jpeg',
            'gallery' => ['/images/mediaimporttest/pic.jpeg'],
            'is_active' => true,
        ]);

        $this->artisan('media:import')->assertExitCode(0);

        $service->refresh();

        $this->assertSame('services/windows/pic.jpeg', $service->image);
        $this->assertSame(['services/windows/pic.jpeg'], $service->gallery);
        $this->assertFileExists(public_path('uploads/services/windows/pic.jpeg'));
        $this->assertFileExists(public_path('uploads/services/windows/pic-thumb.jpeg'));
    }

    public function test_it_is_idempotent(): void
    {
        $source = public_path('images/mediaimporttest');
        File::ensureDirectoryExists($source);
        File::put($source.'/pic.jpeg', 'image-bytes');

        $service = Service::create([
            'slug' => 'doors',
            'title' => ['fr' => 'Portes'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'image' => '/images/mediaimporttest/pic.jpeg',
            'is_active' => true,
        ]);

        $this->artisan('media:import')->assertExitCode(0);
        $this->artisan('media:import')->assertExitCode(0);

        $this->assertSame('services/doors/pic.jpeg', $service->refresh()->image);
    }

    public function test_it_leaves_unreachable_values_untouched(): void
    {
        $service = Service::create([
            'slug' => 'pergola',
            'title' => ['fr' => 'Pergola'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'image' => '/images/does-not-exist/nope.jpeg',
            'is_active' => true,
        ]);

        $this->artisan('media:import --skip-remote')->assertExitCode(0);

        $this->assertSame('/images/does-not-exist/nope.jpeg', $service->refresh()->image);
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact tests/Feature/MediaImportTest.php`
Expected: FAIL — `The command "media:import" does not exist.`

- [ ] **Step 3: Write the command**

Run: `php artisan make:command MediaImportCommand --no-interaction`

Replace `app/Console/Commands/MediaImportCommand.php` with:

```php
<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Service;
use App\Support\MediaPath;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Brings legacy and external images onto the uploads disk so they can be
 * managed through Filament. Non-destructive: originals under public/images
 * are left in place, and anything that cannot be fetched is reported and
 * left untouched in the database.
 */
class MediaImportCommand extends Command
{
    protected $signature = 'media:import {--skip-remote : Do not attempt to download external URLs}';

    protected $description = 'Copy legacy and external content images onto the uploads disk';

    /** @var array<int, string> */
    private array $failures = [];

    public function handle(): int
    {
        foreach (Service::all() as $service) {
            $this->importModel($service, 'services/'.$service->slug);
        }

        foreach (Project::all() as $project) {
            $this->importModel($project, 'projects/'.$project->getKey());
        }

        if ($this->failures === []) {
            $this->info('All content images are on the uploads disk.');

            return self::SUCCESS;
        }

        $this->warn('Could not import '.count($this->failures).' image(s). Re-upload these through the admin panel:');

        foreach ($this->failures as $failure) {
            $this->line('  - '.$failure);
        }

        return self::SUCCESS;
    }

    private function importModel(Model $model, string $directory): void
    {
        $dirty = false;

        $image = $this->importValue($model->image, $directory, $model);
        if ($image !== $model->image) {
            $model->image = $image;
            $dirty = true;
        }

        if (is_array($model->gallery)) {
            $gallery = array_map(
                fn ($entry) => is_string($entry) ? $this->importValue($entry, $directory, $model) : $entry,
                $model->gallery
            );

            if ($gallery !== $model->gallery) {
                $model->gallery = $gallery;
                $dirty = true;
            }
        }

        if ($dirty) {
            $model->save();
        }
    }

    /**
     * Returns the new disk-relative value, or the original value unchanged if
     * the file could not be brought across.
     */
    private function importValue(?string $value, string $directory, Model $model): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // Already on the uploads disk.
        if (! MediaPath::isExternal($value) && ! Str::startsWith($value, '/')) {
            return $value;
        }

        if (MediaPath::isExternal($value)) {
            return $this->option('skip-remote')
                ? $this->fail($value, $model)
                : $this->importRemote($value, $directory, $model);
        }

        return $this->importLocal($value, $directory, $model);
    }

    private function importLocal(string $value, string $directory, Model $model): string
    {
        $source = public_path(ltrim($value, '/'));

        if (! is_file($source)) {
            return $this->fail($value, $model);
        }

        $basename = basename($source);
        $target = public_path('uploads/'.$directory.'/'.$basename);
        File::ensureDirectoryExists(dirname($target));

        if (! is_file($target)) {
            File::copy($source, $target);
        }

        // Bring the pre-generated thumbnail across too, if there is one.
        $thumbSource = preg_replace('/(\.jpe?g|\.png|\.webp)$/i', '-thumb$1', $source);
        if (is_string($thumbSource) && is_file($thumbSource)) {
            $thumbTarget = public_path('uploads/'.$directory.'/'.basename($thumbSource));
            if (! is_file($thumbTarget)) {
                File::copy($thumbSource, $thumbTarget);
            }
        }

        return $directory.'/'.$basename;
    }

    private function importRemote(string $value, string $directory, Model $model): string
    {
        try {
            $response = Http::timeout(20)->get($value);
        } catch (\Throwable) {
            return $this->fail($value, $model);
        }

        if (! $response->successful()) {
            return $this->fail($value, $model);
        }

        $extension = match (true) {
            str_contains($response->header('Content-Type') ?? '', 'png') => 'png',
            str_contains($response->header('Content-Type') ?? '', 'webp') => 'webp',
            default => 'jpg',
        };

        $basename = Str::slug(Str::limit(pathinfo(parse_url($value, PHP_URL_PATH) ?: 'image', PATHINFO_FILENAME), 40, '')).'-'.Str::random(6).'.'.$extension;
        $target = public_path('uploads/'.$directory.'/'.$basename);
        File::ensureDirectoryExists(dirname($target));
        File::put($target, $response->body());

        return $directory.'/'.$basename;
    }

    private function fail(string $value, Model $model): string
    {
        $label = class_basename($model).' #'.$model->getKey().' → '.$value;

        if (! in_array($label, $this->failures, true)) {
            $this->failures[] = $label;
        }

        return $value;
    }
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php artisan test --compact tests/Feature/MediaImportTest.php`
Expected: PASS, 3 tests.

- [ ] **Step 5: Run the import against the real database**

```bash
php artisan media:import
```

Expected: either "All content images are on the uploads disk." or a short list naming the three known external services (kitchen, pergola, mosquito_nets) if the downloads failed.

- [ ] **Step 6: Verify no external gallery entries remain**

This is the prerequisite for Task 5. Run:

```bash
php artisan tinker --execute="
\$bad = 0;
foreach (\App\Models\Service::all() as \$s) {
  foreach (\$s->gallery ?? [] as \$g) {
    if (is_string(\$g) && (str_starts_with(\$g,'http') || str_starts_with(\$g,'/'))) { \$bad++; echo \$s->slug.': '.\$g.PHP_EOL; }
  }
}
echo 'remaining='.\$bad.PHP_EOL;"
```

Expected: `remaining=0`.

If any remain, they are single-entry galleries duplicating the service's main image (which is now safe in `image_url`). Clear them manually:

```bash
php artisan tinker --execute="
foreach (\App\Models\Service::all() as \$s) {
  \$g = array_values(array_filter(\$s->gallery ?? [], fn(\$i) => is_string(\$i) && !str_starts_with(\$i,'http') && !str_starts_with(\$i,'/')));
  if (\$g !== (\$s->gallery ?? [])) { \$s->gallery = \$g; \$s->save(); echo 'cleaned '.\$s->slug.PHP_EOL; }
}"
```

- [ ] **Step 7: Format and commit**

```bash
vendor/bin/pint --dirty
git add app/Console/Commands/MediaImportCommand.php tests/Feature/MediaImportTest.php
git commit -m "feat(media): add media:import command for legacy and external images"
```

---

# PHASE 2 — Services

## Task 4: Service image uploads with previews (fixes the reorder bug)

Root cause of the reported bug: `->url()` renders `<input type="url">`. Values like `/images/services/doors/doors-01.jpeg` fail the browser's native URL validation, so the form refuses to submit even when only the drag order changed.

**Files:**
- Modify: `app/Filament/Resources/ServiceResource.php:244-272` (Médias tab), `:347` (ImageColumn)
- Test: `tests/Feature/AdminContentManagementTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/AdminContentManagementTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    /**
     * Regression: the gallery field used ->url(), which rendered
     * <input type="url">. Stored values like "/images/services/..." are not
     * valid absolute URLs, so the browser blocked submission after a reorder.
     */
    public function test_a_service_saves_when_only_the_gallery_order_changed(): void
    {
        $service = Service::create([
            'slug' => 'doors',
            'title' => ['fr' => 'Portes', 'en' => 'Doors', 'ar' => 'أبواب'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'gallery' => ['services/doors/a.jpeg', 'services/doors/b.jpeg'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(EditService::class, ['record' => $service->getKey()])
            ->set('data.gallery', ['services/doors/b.jpeg', 'services/doors/a.jpeg'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            ['services/doors/b.jpeg', 'services/doors/a.jpeg'],
            array_values($service->refresh()->gallery)
        );
    }

    public function test_an_external_image_url_survives_an_unrelated_edit(): void
    {
        $service = Service::create([
            'slug' => 'pergola',
            'title' => ['fr' => 'Pergola', 'en' => 'Pergola', 'ar' => 'برجولا'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'image' => null,
            'image_url' => 'https://images.pexels.com/photos/7587884/x.jpeg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(EditService::class, ['record' => $service->getKey()])
            ->set('data.title.fr', 'Pergola Modifiée')
            ->call('save')
            ->assertHasNoFormErrors();

        $service->refresh();

        $this->assertSame('Pergola Modifiée', $service->title['fr']);
        $this->assertSame('https://images.pexels.com/photos/7587884/x.jpeg', $service->image_url);
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact tests/Feature/AdminContentManagementTest.php`
Expected: FAIL — the gallery Repeater state shape does not match a flat array, and/or `image_url` is not in the form.

If `User::factory()` does not exist, check `database/factories/UserFactory.php`; Laravel 12 ships it by default. If `Livewire\Livewire` is missing, it is provided transitively by Filament — confirm with `composer show livewire/livewire`.

- [ ] **Step 3: Replace the Médias tab**

In `app/Filament/Resources/ServiceResource.php`, replace the whole `Tabs\Tab::make('Médias')` block (currently lines 243-272) with:

```php
                        // Media Tab
                        Tabs\Tab::make('Médias')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make('Image principale')
                                    ->description('Image affichée sur la carte du service. Glissez un fichier ou utilisez le bouton.')
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Fichier')
                                            ->disk('uploads')
                                            ->directory('services')
                                            ->visibility('public')
                                            ->image()
                                            ->imageEditor()
                                            ->imagePreviewHeight('150')
                                            ->maxSize(5120)
                                            ->helperText('JPG, PNG ou WebP — 5 Mo maximum.'),
                                        Forms\Components\TextInput::make('image_url')
                                            ->label('URL externe (optionnel)')
                                            ->maxLength(2048)
                                            ->rule('nullable')
                                            ->rules(['nullable', 'string', 'max:2048'])
                                            ->helperText('Utilisée uniquement si aucun fichier n\'est téléversé ci-dessus.'),
                                    ]),

                                Forms\Components\Section::make('Galerie d\'images')
                                    ->description('Glissez-déposez les vignettes pour changer l\'ordre d\'affichage dans le carrousel.')
                                    ->schema([
                                        Forms\Components\FileUpload::make('gallery')
                                            ->label('')
                                            ->disk('uploads')
                                            ->directory('services')
                                            ->visibility('public')
                                            ->image()
                                            ->imageEditor()
                                            ->multiple()
                                            ->reorderable()
                                            ->appendFiles()
                                            ->panelLayout('grid')
                                            ->imagePreviewHeight('120')
                                            ->maxSize(5120)
                                            ->helperText('La première image est utilisée comme image mise en avant.'),
                                    ]),
                            ]),
```

**Do not reintroduce `->url()` anywhere.** That modifier is the cause of this bug.

- [ ] **Step 4: Fix the list-table image column**

In the same file, replace the `Tables\Columns\ImageColumn::make('image')` block (around line 347) with:

```php
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->getStateUsing(fn (Service $record): ?string => $record->imageSrc()),
```

Add `use App\Models\Service;` if it is not already imported (it is — line 5).

- [ ] **Step 5: Run the test to verify it passes**

Run: `php artisan test --compact tests/Feature/AdminContentManagementTest.php`
Expected: PASS, 2 tests.

- [ ] **Step 6: Format and commit**

```bash
vendor/bin/pint --dirty
git add app/Filament/Resources/ServiceResource.php tests/Feature/AdminContentManagementTest.php
git commit -m "fix(admin): replace service URL inputs with file uploads

The ->url() modifier rendered <input type=\"url\">, which rejected the
root-relative paths already stored in the database and blocked form
submission after a gallery reorder."
```

---

## Task 5: Fix the collapsed-`required` repeater blocker

A native `required` attribute on an input inside a collapsed — therefore hidden — container makes the browser refuse to submit while highlighting nothing. `features`, `materials` and `specs` are all `->collapsible()->collapsed()` repeaters containing `->required()` inputs.

**Files:**
- Modify: `app/Filament/Resources/ServiceResource.php:220`, `:290`, `:315`, `:319`
- Test: `tests/Feature/AdminContentManagementTest.php`

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/AdminContentManagementTest.php`, inside the class:

```php
    /**
     * Regression: ->required() inside a ->collapsed() repeater sets the native
     * HTML required attribute on a hidden input, which makes the browser
     * refuse to submit without reporting anything. Validation must be
     * server-side so Filament can surface the error against the right item.
     */
    public function test_an_invalid_collapsed_repeater_item_reports_a_form_error(): void
    {
        $service = Service::create([
            'slug' => 'railings',
            'title' => ['fr' => 'Garde-corps', 'en' => 'Railings', 'ar' => 'درابزين'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'features' => [],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(EditService::class, ['record' => $service->getKey()])
            ->set('data.features', [['fr' => '', 'en' => '', 'ar' => '']])
            ->call('save')
            ->assertHasFormErrors();
    }

    public function test_a_valid_collapsed_repeater_item_saves(): void
    {
        $service = Service::create([
            'slug' => 'kitchen',
            'title' => ['fr' => 'Cuisine', 'en' => 'Kitchen', 'ar' => 'مطبخ'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'features' => [],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(EditService::class, ['record' => $service->getKey()])
            ->set('data.features', [['fr' => 'Installation rapide', 'en' => 'Fast install', 'ar' => 'تركيب سريع']])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Installation rapide', $service->refresh()->features[0]['fr']);
    }
```

- [ ] **Step 2: Run the tests to verify the first fails**

Run: `php artisan test --compact --filter=collapsed_repeater`
Expected: the invalid-item test may already pass (Filament also validates server-side), but the *native* attribute is still emitted. Proceed regardless — Step 3 removes the browser-level block, which is the actual defect.

- [ ] **Step 3: Replace `->required()` with server-side validation in the three repeaters**

In `app/Filament/Resources/ServiceResource.php`, make these four edits. In each case swap `->required()` for `->rules(['required', 'string'])`, which validates on the server without emitting the HTML `required` attribute:

1. `features` repeater, the `fr` input (line ~220):
```php
                                                        Forms\Components\TextInput::make('fr')
                                                            ->label('🇫🇷 Français')
                                                            ->rules(['required', 'string'])
                                                            ->placeholder('Ex: Installation rapide et professionnelle'),
```

2. `materials` repeater, the `fr` input (line ~290):
```php
                                                        Forms\Components\TextInput::make('fr')
                                                            ->label('🇫🇷 Français')
                                                            ->rules(['required', 'string'])
                                                            ->placeholder('Ex: Profilés aluminium'),
```

3. `specs` repeater, the `label` input (line ~315):
```php
                                                Forms\Components\TextInput::make('label')
                                                    ->label('Nom')
                                                    ->rules(['required', 'string'])
                                                    ->placeholder('Ex: Épaisseur')
                                                    ->columnSpan(1),
```

4. `specs` repeater, the `value` input (line ~319):
```php
                                                Forms\Components\TextInput::make('value')
                                                    ->label('Valeur')
                                                    ->rules(['required', 'string'])
                                                    ->placeholder('Ex: 1.2 - 2.0 mm')
                                                    ->columnSpan(1),
```

- [ ] **Step 4: Verify no native `required` remains inside a collapsed repeater**

Run: `grep -n "required()" app/Filament/Resources/ServiceResource.php`
Expected: only the `slug` field at line ~46 (which is not inside a collapsed container).

- [ ] **Step 5: Run the tests to verify they pass**

Run: `php artisan test --compact tests/Feature/AdminContentManagementTest.php`
Expected: PASS, 4 tests.

- [ ] **Step 6: Format and commit**

```bash
vendor/bin/pint --dirty
git add app/Filament/Resources/ServiceResource.php tests/Feature/AdminContentManagementTest.php
git commit -m "fix(admin): move collapsed-repeater validation server-side

Native required on a hidden input makes the browser block submit with
nothing highlighted."
```

---

## Task 6: Render service images through `MediaPath`

**Files:**
- Modify: `resources/views/pages/services.blade.php:163-173`

- [ ] **Step 1: Replace the thumbnail derivation**

The current block hardcodes the legacy path prefix. Replace lines 162-173 (the `@php` block computing `$galleryItems` and `$mainImage`) with:

```php
                    @php
                        $galleryItems = collect($service->gallery ?? [])
                            ->filter(fn ($img) => is_string($img) && trim($img) !== '')
                            ->map(fn ($img) => [
                                'full' => \App\Support\MediaPath::url($img),
                                'thumb' => \App\Support\MediaPath::thumb($img),
                            ])
                            ->values();

                        if ($galleryItems->isEmpty() && ($main = $service->imageSrc()) !== null) {
                            $galleryItems = collect([['full' => $main, 'thumb' => $main]]);
                        }

                        $mainImage = $galleryItems->first()['full'] ?? asset('images/promo-alu-plus-logo.png');
                    @endphp
```

Note: `$gallery` was the previous variable; confirm what feeds it by reading lines 140-162 first and keep any other use of it intact.

- [ ] **Step 2: Verify the homepage cards still resolve**

`home.blade.php:415` calls `$service->getFeaturedImage()`, which Task 2 already routed through `MediaPath`. No change needed there.

- [ ] **Step 3: Build assets and eyeball the pages**

```bash
npm run build
php artisan optimize:clear
php artisan serve
```

Open `http://127.0.0.1:8000/` and `http://127.0.0.1:8000/services`. Every service card and gallery must show an image — no broken-image icons. Stop the server when done.

- [ ] **Step 4: Run the full suite**

Run: `php artisan test --compact`
Expected: PASS (the pre-existing skipped test in `SiteCoherenceTest` stays skipped).

- [ ] **Step 5: Format and commit**

```bash
vendor/bin/pint --dirty
git add resources/views/pages/services.blade.php
git commit -m "refactor(views): resolve service gallery images through MediaPath"
```

---

# PHASE 3 — Portfolio visibility toggle

## Task 7: The `portfolio_enabled` setting, view composer and route guard

**Default is OFF**, matching the page's current hidden state.

**Files:**
- Create: `app/Providers/ViewServiceProvider.php`
- Modify: `bootstrap/providers.php`
- Modify: `app/Http/Controllers/PageController.php`
- Modify: `routes/web.php` (sitemap)
- Test: `tests/Feature/PortfolioVisibilityTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/PortfolioVisibilityTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function enablePortfolio(bool $enabled): void
    {
        SiteSetting::set('portfolio_enabled', $enabled ? '1' : '0', 'boolean', 'pages');
    }

    public function test_the_portfolio_route_is_hidden_by_default(): void
    {
        $this->get(route('portfolio'))->assertNotFound();
    }

    public function test_the_portfolio_route_is_reachable_when_enabled(): void
    {
        $this->enablePortfolio(true);

        $this->get(route('portfolio'))->assertOk();
    }

    public function test_no_page_links_to_the_portfolio_when_disabled(): void
    {
        $this->enablePortfolio(false);

        foreach (['home', 'services', 'about', 'contact'] as $page) {
            $this->get(route($page))
                ->assertOk()
                ->assertDontSee(route('portfolio'), false);
        }
    }

    public function test_the_nav_and_footer_link_to_the_portfolio_when_enabled(): void
    {
        $this->enablePortfolio(true);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('portfolio'), false);
    }

    public function test_the_sitemap_omits_the_portfolio_when_disabled(): void
    {
        $this->enablePortfolio(false);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertDontSee(route('portfolio'), false);
    }

    public function test_the_sitemap_includes_the_portfolio_when_enabled(): void
    {
        $this->enablePortfolio(true);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('portfolio'), false);
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact tests/Feature/PortfolioVisibilityTest.php`
Expected: FAIL — the route returns 200 instead of 404.

- [ ] **Step 3: Add the boolean reader to `SiteSetting`**

In `app/Models/SiteSetting.php`, after `getTranslated()`, add:

```php
    /**
     * Boolean settings are stored as the strings "1" / "0".
     */
    public static function enabled(string $key, bool $default = false): bool
    {
        $value = static::get($key);

        if ($value === null || $value === '') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
```

- [ ] **Step 4: Create the view composer**

Run: `php artisan make:provider ViewServiceProvider --no-interaction`

Replace `app/Providers/ViewServiceProvider.php` with:

```php
<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Scoped to the layout rather than View::share so console commands and a
     * cold-cache `migrate:fresh` never query site_settings before it exists.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $view->with('portfolioEnabled', self::portfolioEnabled());
        });
    }

    public static function portfolioEnabled(): bool
    {
        if (! Schema::hasTable('site_settings')) {
            return false;
        }

        return SiteSetting::enabled('portfolio_enabled');
    }
}
```

Register it in `bootstrap/providers.php` by adding `App\Providers\ViewServiceProvider::class,` to the returned array.

- [ ] **Step 5: Guard the controller**

In `app/Http/Controllers/PageController.php`, change the first line of `portfolio()` to:

```php
    public function portfolio(Request $request)
    {
        abort_unless(\App\Providers\ViewServiceProvider::portfolioEnabled(), 404);

        $category = $request->get('category', 'all');
```

Leave the rest of the method unchanged for now (Task 13 revisits it).

- [ ] **Step 6: Make the sitemap conditional**

In `routes/web.php`, replace the `$pages` line inside the sitemap closure with:

```php
    $pages = ['home', 'services', 'about', 'contact'];

    if (\App\Providers\ViewServiceProvider::portfolioEnabled()) {
        array_splice($pages, 2, 0, ['portfolio']);
    }
```

- [ ] **Step 7: Run the test**

Run: `php artisan test --compact tests/Feature/PortfolioVisibilityTest.php`
Expected: the route/sitemap tests PASS. `test_no_page_links_to_the_portfolio_when_disabled` still FAILS — the footer link is hardcoded. Task 8 fixes that.

- [ ] **Step 8: Format and commit**

```bash
vendor/bin/pint --dirty
git add app/Models/SiteSetting.php app/Providers/ViewServiceProvider.php bootstrap/providers.php app/Http/Controllers/PageController.php routes/web.php tests/Feature/PortfolioVisibilityTest.php
git commit -m "feat(admin): add portfolio_enabled setting with route and sitemap guards"
```

---

## Task 8: Wire every Blade surface to the toggle

**Files:**
- Modify: `resources/views/layouts/app.blade.php:55`, `:84`, `:194`
- Modify: `resources/views/pages/home.blade.php:122`, `:728`
- Modify: `resources/views/pages/about.blade.php:319`
- Modify: `resources/views/pages/services.blade.php:379`

- [ ] **Step 1: Mobile nav — uncomment and guard**

`resources/views/layouts/app.blade.php:55`, replace:

```blade
            {{-- <a href="{{ route('portfolio') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_portfolio') }}</a> --}}
```

with:

```blade
            @if($portfolioEnabled ?? false)
                <a href="{{ route('portfolio') }}" class="text-white text-2xl font-semibold hover:text-orange-400 transition-colors">{{ __('messages.nav_portfolio') }}</a>
            @endif
```

- [ ] **Step 2: Desktop nav — uncomment and guard**

`resources/views/layouts/app.blade.php:84`, replace:

```blade
                    {{-- <a href="{{ route('portfolio') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('portfolio') ? 'text-blue-300' : '' }}">{{ __('messages.nav_portfolio') }}</a> --}}
```

with:

```blade
                    @if($portfolioEnabled ?? false)
                        <a href="{{ route('portfolio') }}" class="nav-link text-white hover:text-blue-300 transition-colors font-medium duration-300 {{ request()->routeIs('portfolio') ? 'text-blue-300' : '' }}">{{ __('messages.nav_portfolio') }}</a>
                    @endif
```

- [ ] **Step 3: Footer — guard the live link**

`resources/views/layouts/app.blade.php:194`, replace:

```blade
                        <li><a href="{{ route('portfolio') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_portfolio') }}</a></li>
```

with:

```blade
                        @if($portfolioEnabled ?? false)
                            <li><a href="{{ route('portfolio') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('messages.nav_portfolio') }}</a></li>
                        @endif
```

- [ ] **Step 4: Hero slide 3 CTA**

`resources/views/pages/home.blade.php:122-125`, replace:

```blade
                                    <a href="{{ route('portfolio') }}" class="btn-primary inline-flex items-center justify-center group shadow-2xl hover:shadow-blue-500/40">
                                        {{ __('messages.view_our_work') }}
```

with:

```blade
                                    <a href="{{ ($portfolioEnabled ?? false) ? route('portfolio') : route('services') }}" class="btn-primary inline-flex items-center justify-center group shadow-2xl hover:shadow-blue-500/40">
                                        {{ ($portfolioEnabled ?? false) ? __('messages.view_our_work') : __('messages.view_all_services') }}
```

Task 17 replaces this markup entirely with the hero loop; make the edit anyway so the toggle is correct in the meantime and so the loop has the right pattern to copy.

- [ ] **Step 5: The three `<x-cta-buttons>` secondary links**

All three sites use identical markup (`home.blade.php:728-729`, `about.blade.php:319-320`, `services.blade.php:379-380`). In each file replace:

```blade
                    :secondary-href="route('portfolio')"
                    :secondary-label="__('messages.view_our_work')" />
```

with:

```blade
                    :secondary-href="($portfolioEnabled ?? false) ? route('portfolio') : route('contact')"
                    :secondary-label="($portfolioEnabled ?? false) ? __('messages.view_our_work') : __('messages.contact_us')" />
```

Both `view_our_work` (`lang/fr/messages.php:181`) and `contact_us` (`:184`) already exist in all three locales.

- [ ] **Step 6: Confirm no unguarded portfolio links remain**

Run: `grep -rn "route('portfolio')" resources/views/`
Expected: every hit is inside an `@if($portfolioEnabled ?? false)` block, a ternary on `$portfolioEnabled`, or inside `pages/portfolio.blade.php` itself (that page only renders when enabled).

- [ ] **Step 7: Run the test to verify it passes**

Run: `php artisan test --compact tests/Feature/PortfolioVisibilityTest.php`
Expected: PASS, 6 tests.

- [ ] **Step 8: Format and commit**

```bash
vendor/bin/pint --dirty
git add resources/views
git commit -m "feat(views): gate every portfolio link behind portfolio_enabled"
```

---

## Task 9: "Pages & visibilité" tab in Site Settings

**Files:**
- Modify: `app/Filament/Pages/SiteSettings.php:41-55`

- [ ] **Step 1: Add the tab**

In `app/Filament/Pages/SiteSettings.php`, insert this as the **first** tab in the `->tabs([...])` array, before `Tabs\Tab::make('Entreprise')`:

```php
                        // === PAGES & VISIBILITÉ ===
                        Forms\Components\Tabs\Tab::make('Pages & visibilité')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Forms\Components\Section::make('Page Réalisations')
                                    ->description('Affichez ou masquez la page Réalisations sur le site public. Lorsqu\'elle est masquée, la page renvoie une erreur 404 et tous les liens vers celle-ci disparaissent du menu, du pied de page et des boutons d\'appel à l\'action.')
                                    ->schema([
                                        Forms\Components\Toggle::make('portfolio_enabled')
                                            ->label('Afficher la page Réalisations')
                                            ->helperText('Ajoutez d\'abord vos projets dans Contenu → Projets, puis activez cette option.')
                                            ->default(false),
                                    ]),
                            ]),
```

- [ ] **Step 2: Verify the toggle round-trips**

The page's `mount()` fills the form from `SiteSetting::all()->pluck('value','key')` and its save action writes each key back. A `Toggle` hydrates from the string `"1"`/`"0"` and dehydrates to a PHP bool, which `SiteSetting::set()` will store as `1`/`0`. Confirm the save action casts booleans — read the `save()` method and, if it writes raw values, ensure booleans become `'1'`/`'0'`:

```php
$value = is_bool($value) ? ($value ? '1' : '0') : $value;
```

- [ ] **Step 3: Manual check**

```bash
php artisan serve
```

Log in at `http://127.0.0.1:8000/admin` (`admin@aluminiumcraft.tn` / `password`), open **Paramètres du site → Pages & visibilité**, toggle it on, save, then load `http://127.0.0.1:8000/` — the Réalisations link must appear in the nav and footer. Toggle it off and confirm it disappears and `/portfolio` 404s. Leave it **off**. Stop the server.

- [ ] **Step 4: Run the suite and commit**

```bash
php artisan test --compact tests/Feature/PortfolioVisibilityTest.php
vendor/bin/pint --dirty
git add app/Filament/Pages/SiteSettings.php
git commit -m "feat(admin): add Pages & visibilité tab with the Réalisations toggle"
```

---

## Task 10: Update the pre-existing tests for the new default

`PublicSiteFeatureTest` assumes `/portfolio` always returns 200 and that the sitemap lists five pages.

**Files:**
- Modify: `tests/Feature/PublicSiteFeatureTest.php:35`, `:93-112`, `:138-160`, `:216-229`

- [ ] **Step 1: Split the route constant**

Replace line 35:

```php
    private const PAGE_ROUTES = ['home', 'services', 'portfolio', 'about', 'contact'];
```

with:

```php
    /** Pages that are always public. */
    private const PAGE_ROUTES = ['home', 'services', 'about', 'contact'];

    /** Pages gated behind a SiteSetting toggle. */
    private const TOGGLEABLE_PAGE_ROUTES = ['portfolio'];

    private function enablePortfolio(): void
    {
        SiteSetting::set('portfolio_enabled', '1', 'boolean', 'pages');
    }
```

`SiteSetting` is already imported at line 15.

- [ ] **Step 2: Cover the toggleable page in the locale sweep**

In `test_every_public_page_renders_in_every_locale()`, after `$this->seedContent();` add `$this->enablePortfolio();`, and change the inner loop to:

```php
            foreach ([...self::PAGE_ROUTES, ...self::TOGGLEABLE_PAGE_ROUTES] as $page) {
```

- [ ] **Step 3: Cover it in the empty-database test**

In `test_public_pages_render_with_a_completely_empty_database()`, add `$this->enablePortfolio();` as the first line, then change the loop to `foreach ([...self::PAGE_ROUTES, ...self::TOGGLEABLE_PAGE_ROUTES] as $page) {`.

- [ ] **Step 4: Enable it in the category-filter test**

In `test_portfolio_filters_projects_by_category()`, add `$this->enablePortfolio();` immediately after `$this->seedContent();`.

Note this test currently asserts `assertDontSee('Villa Test')` when filtering by `kitchen`. Task 13 removes the hardcoded demo projects, which does not affect this assertion.

- [ ] **Step 5: Fix the sitemap count test**

Replace the body of `test_sitemap_lists_every_public_page_as_valid_xml()` with:

```php
    public function test_sitemap_lists_every_public_page_as_valid_xml(): void
    {
        $this->enablePortfolio();

        $response = $this->get('/sitemap.xml')->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'sitemap.xml is not well-formed XML.');

        $expected = [...self::PAGE_ROUTES, ...self::TOGGLEABLE_PAGE_ROUTES];
        $this->assertCount(count($expected), $xml->url);

        foreach ($expected as $page) {
            $response->assertSee(route($page), false);
        }
    }
```

- [ ] **Step 6: Run the full suite**

Run: `php artisan test --compact`
Expected: PASS. If `SiteCoherenceTest::test_portfolio_filter_categories_are_canonical_service_slugs` is still skipped, that is correct — Task 11 revisits it.

- [ ] **Step 7: Format and commit**

```bash
vendor/bin/pint --dirty
git add tests/Feature/PublicSiteFeatureTest.php
git commit -m "test: account for the portfolio visibility toggle in existing coverage"
```

---

# PHASE 4 — Portfolio content

## Task 11: Make `ProjectType` usable

**Files:**
- Create: `database/migrations/<timestamp>_make_project_type_name_translatable.php`
- Modify: `app/Models/ProjectType.php`
- Create: `database/seeders/ProjectTypeSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Modify: `app/Filament/Resources/ProjectTypeResource.php`
- Test: `tests/Feature/ProjectTypeTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/ProjectTypeTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\ProjectType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_name_is_translatable_and_falls_back_to_french(): void
    {
        $type = ProjectType::create([
            'name' => ['fr' => 'Fenêtres', 'en' => 'Windows'],
            'slug' => 'windows',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->assertSame('Fenêtres', $type->getTranslatedName('fr'));
        $this->assertSame('Windows', $type->getTranslatedName('en'));
        $this->assertSame('Fenêtres', $type->getTranslatedName('ar'));
    }

    public function test_active_and_ordered_scopes(): void
    {
        ProjectType::create(['name' => ['fr' => 'B'], 'slug' => 'b', 'order' => 2, 'is_active' => true]);
        ProjectType::create(['name' => ['fr' => 'A'], 'slug' => 'a', 'order' => 1, 'is_active' => true]);
        ProjectType::create(['name' => ['fr' => 'X'], 'slug' => 'x', 'order' => 0, 'is_active' => false]);

        $this->assertSame(['a', 'b'], ProjectType::active()->ordered()->pluck('slug')->all());
    }

    public function test_the_seeder_creates_the_three_existing_categories(): void
    {
        $this->seed(\Database\Seeders\ProjectTypeSeeder::class);

        $this->assertSame(['windows', 'doors', 'facades'], ProjectType::ordered()->pluck('slug')->all());
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact tests/Feature/ProjectTypeTest.php`
Expected: FAIL — `Add [name] to fillable`.

- [ ] **Step 3: Create the migration**

Run: `php artisan make:migration make_project_type_name_translatable --no-interaction`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The table is empty in every known environment, so a plain type
        // change is safe. Any stray row is converted to the fr-only shape.
        foreach (DB::table('project_types')->get() as $row) {
            if (! str_starts_with((string) $row->name, '{')) {
                DB::table('project_types')
                    ->where('id', $row->id)
                    ->update(['name' => json_encode(['fr' => $row->name], JSON_UNESCAPED_UNICODE)]);
            }
        }

        Schema::table('project_types', function (Blueprint $table) {
            $table->json('name')->change();
        });
    }

    public function down(): void
    {
        Schema::table('project_types', function (Blueprint $table) {
            $table->string('name')->change();
        });
    }
};
```

- [ ] **Step 4: Fill in the model**

Replace `app/Models/ProjectType.php` with:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'is_active' => 'boolean',
    ];

    public function getTranslatedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->name[$locale] ?? $this->name['fr'] ?? '';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
```

- [ ] **Step 5: Write the seeder**

Run: `php artisan make:seeder ProjectTypeSeeder --no-interaction`

```php
<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['slug' => 'windows', 'order' => 1, 'color' => 'info', 'icon' => 'app-window',
                'name' => ['fr' => 'Fenêtres', 'en' => 'Windows', 'ar' => 'نوافذ']],
            ['slug' => 'doors', 'order' => 2, 'color' => 'warning', 'icon' => 'door-open',
                'name' => ['fr' => 'Portes', 'en' => 'Doors', 'ar' => 'أبواب']],
            ['slug' => 'facades', 'order' => 3, 'color' => 'success', 'icon' => 'layout-grid',
                'name' => ['fr' => 'Façades', 'en' => 'Facades', 'ar' => 'واجهات']],
        ];

        foreach ($types as $type) {
            ProjectType::updateOrCreate(
                ['slug' => $type['slug']],
                $type + ['is_active' => true]
            );
        }
    }
}
```

Register it in `database/seeders/DatabaseSeeder.php` by adding `ProjectTypeSeeder::class,` to the `$this->call([...])` array. Read the file first to match its existing style.

- [ ] **Step 6: Build `ProjectTypeResource` from scratch**

**Important:** `app/Filament/Resources/ProjectTypeResource.php` and its three `Pages/` classes are **0-byte files**. Filament's `discoverResources` skips files that define no class, which is why the admin panel currently boots without them. Delete them and scaffold cleanly:

```bash
rm -f app/Filament/Resources/ProjectTypeResource.php
rm -rf app/Filament/Resources/ProjectTypeResource
php artisan make:filament-resource ProjectType --generate --no-interaction
```

Leave `CategoryResource` and its pages alone — they are empty too, but `Category` is out of scope for this plan.

Then replace the generated `form()` and `table()` with:

```php
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Nom (multilingue)')
                ->schema([
                    Forms\Components\TextInput::make('name.fr')
                        ->label('🇫🇷 Français')
                        ->rules(['required', 'string', 'max:60']),
                    Forms\Components\TextInput::make('name.en')
                        ->label('🇬🇧 English')
                        ->maxLength(60),
                    Forms\Components\TextInput::make('name.ar')
                        ->label('🇸🇦 العربية')
                        ->maxLength(60)
                        ->extraInputAttributes(['dir' => 'rtl']),
                ])->columns(3),

            Forms\Components\Section::make('Paramètres')
                ->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('Identifiant unique (slug)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->alphaDash()
                        ->maxLength(50)
                        ->helperText('Utilisé dans l\'URL de filtrage des réalisations.'),
                    Forms\Components\Select::make('color')
                        ->label('Couleur du badge')
                        ->options([
                            'info' => 'Bleu',
                            'warning' => 'Orange',
                            'success' => 'Vert',
                            'danger' => 'Rouge',
                            'gray' => 'Gris',
                        ])
                        ->default('info')
                        ->native(false),
                    Forms\Components\TextInput::make('icon')
                        ->label('Icône')
                        ->maxLength(50)
                        ->helperText('Nom d\'icône Lucide, par ex. app-window, door-open.'),
                    Forms\Components\TextInput::make('order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name.fr')
                    ->label('Nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Actif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order');
    }
```

Set the navigation properties near the top of the class:

```php
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $modelLabel = 'Type de projet';

    protected static ?string $pluralModelLabel = 'Types de projet';

    protected static ?int $navigationSort = 3;
```

- [ ] **Step 7: Run the test and migrate**

```bash
php artisan migrate
php artisan db:seed --class=ProjectTypeSeeder --no-interaction
php artisan test --compact tests/Feature/ProjectTypeTest.php
```

Expected: PASS, 3 tests.

- [ ] **Step 8: Format and commit**

```bash
vendor/bin/pint --dirty
git add app/Models/ProjectType.php app/Filament/Resources/ProjectTypeResource.php database/migrations database/seeders
git commit -m "feat(admin): make project types translatable and manageable"
```

---

## Task 12: `ProjectResource` uploads and dynamic categories

**Files:**
- Modify: `app/Filament/Resources/ProjectResource.php:55-62` (category Select), `:70-81` (Images), `:96-98` (ImageColumn), `:104-112` (category badge), `:124-131` (filter)

- [ ] **Step 1: Category select from the database**

Replace the `Forms\Components\Select::make('category')` block with:

```php
                        Forms\Components\Select::make('category')
                            ->label('Catégorie')
                            ->options(fn (): array => \App\Models\ProjectType::query()
                                ->active()
                                ->ordered()
                                ->get()
                                ->mapWithKeys(fn (\App\Models\ProjectType $type): array => [$type->slug => $type->getTranslatedName('fr')])
                                ->all())
                            ->required()
                            ->native(false)
                            ->helperText('Gérez la liste dans Contenu → Types de projet.'),
```

- [ ] **Step 2: Images section with uploads and previews**

Replace the `Forms\Components\Section::make('Images')` block with:

```php
                Forms\Components\Section::make('Images')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image principale')
                            ->disk('uploads')
                            ->directory('projects')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight('150')
                            ->maxSize(5120),
                        Forms\Components\TextInput::make('image_url')
                            ->label('URL externe (optionnel)')
                            ->maxLength(2048)
                            ->rules(['nullable', 'string', 'max:2048'])
                            ->helperText('Utilisée uniquement si aucun fichier n\'est téléversé.'),
                        Forms\Components\FileUpload::make('gallery')
                            ->label('Galerie')
                            ->disk('uploads')
                            ->directory('projects')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->imagePreviewHeight('120')
                            ->maxSize(5120)
                            ->columnSpanFull()
                            ->helperText('Glissez-déposez les vignettes pour changer leur ordre.'),
                    ])->columns(2),
```

- [ ] **Step 3: Table image column and category badge**

Replace the `ImageColumn::make('image')` block with:

```php
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->getStateUsing(fn (Project $record): ?string => $record->imageSrc()),
```

Replace the `TextColumn::make('category')` block with:

```php
                Tables\Columns\TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => \App\Models\ProjectType::query()
                        ->where('slug', $state)
                        ->first()?->getTranslatedName('fr') ?? (string) $state),
```

- [ ] **Step 4: Table filter from the database**

Replace the `SelectFilter::make('category')` block with:

```php
                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options(fn (): array => \App\Models\ProjectType::query()
                        ->active()
                        ->ordered()
                        ->get()
                        ->mapWithKeys(fn (\App\Models\ProjectType $type): array => [$type->slug => $type->getTranslatedName('fr')])
                        ->all()),
```

- [ ] **Step 5: Verify nothing hardcodes the three categories**

Run: `grep -n "facades" app/Filament/Resources/ProjectResource.php`
Expected: no matches.

- [ ] **Step 6: Run the suite and commit**

```bash
php artisan test --compact
vendor/bin/pint --dirty
git add app/Filament/Resources/ProjectResource.php
git commit -m "feat(admin): project uploads with previews and database-driven categories"
```

---

## Task 13: Rewrite the portfolio page

**Files:**
- Modify: `resources/views/pages/portfolio.blade.php:26-108`
- Modify: `app/Http/Controllers/PageController.php` (`portfolio()`)
- Modify: `tests/Feature/SiteCoherenceTest.php:198-215`
- Test: `tests/Feature/PortfolioVisibilityTest.php`

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/PortfolioVisibilityTest.php`, inside the class:

```php
    public function test_an_empty_portfolio_shows_an_empty_state_not_invented_projects(): void
    {
        $this->enablePortfolio(true);

        $this->get(route('portfolio'))
            ->assertOk()
            ->assertDontSee('Villa Moderne')
            ->assertDontSee('Résidence Carthage')
            ->assertDontSee('Immeuble Commercial')
            ->assertSee(__('messages.portfolio_empty'));
    }

    public function test_the_filter_bar_is_built_from_project_types(): void
    {
        $this->enablePortfolio(true);
        $this->seed(\Database\Seeders\ProjectTypeSeeder::class);

        \App\Models\ProjectType::create([
            'name' => ['fr' => 'Pergolas', 'en' => 'Pergolas', 'ar' => 'برجولات'],
            'slug' => 'pergola',
            'order' => 4,
            'is_active' => true,
        ]);

        $this->withSession(['locale' => 'fr'])
            ->get(route('portfolio'))
            ->assertOk()
            ->assertSee('Pergolas');
    }
```

- [ ] **Step 2: Add the empty-state translation key**

Add to all three `lang/*/messages.php`, next to `portfolio_intro`:

- `lang/fr/messages.php`: `'portfolio_empty' => 'Nos réalisations seront publiées ici prochainement. Contactez-nous pour découvrir nos projets en cours.',`
- `lang/en/messages.php`: `'portfolio_empty' => 'Our projects will be published here soon. Get in touch to discover our current work.',`
- `lang/ar/messages.php`: `'portfolio_empty' => 'سيتم نشر إنجازاتنا هنا قريبًا. اتصل بنا لاكتشاف مشاريعنا الجارية.',`

- [ ] **Step 3: Pass the types to the view**

In `app/Http/Controllers/PageController.php`, replace the body of `portfolio()` with:

```php
    public function portfolio(Request $request)
    {
        abort_unless(\App\Providers\ViewServiceProvider::portfolioEnabled(), 404);

        $category = $request->get('category', 'all');

        $query = Project::active()->orderBy('sort_order');

        if ($category !== 'all') {
            $query->byCategory($category);
        }

        $projects = $query->get();
        $projectTypes = ProjectType::active()->ordered()->get();
        $testimonials = Testimonial::active()->orderBy('sort_order')->get();

        return view('pages.portfolio', compact('projects', 'projectTypes', 'testimonials', 'category'));
    }
```

Add `use App\Models\ProjectType;` to the imports.

- [ ] **Step 4: Rewrite the filter bar**

In `resources/views/pages/portfolio.blade.php`, replace the whole filter-buttons `<div>` (lines 27-44) with:

```blade
            <div class="flex flex-wrap justify-center gap-3 md:gap-4 mb-10 md:mb-14 scroll-fade">
                <a href="{{ route('portfolio') }}"
                   class="portfolio-filter px-5 md:px-6 py-2.5 rounded-full font-semibold transition-all duration-300 {{ $category === 'all' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ __('messages.all') }}
                </a>
                @foreach($projectTypes as $projectType)
                    <a href="{{ route('portfolio', ['category' => $projectType->slug]) }}"
                       class="portfolio-filter px-5 md:px-6 py-2.5 rounded-full font-semibold transition-all duration-300 {{ $category === $projectType->slug ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $projectType->getTranslatedName() }}
                    </a>
                @endforeach
            </div>
```

- [ ] **Step 5: Rewrite the grid — real images, no invented projects**

Replace the whole portfolio-grid `<div>` (lines 46-108, from `<!-- Portfolio Grid -->` through the closing `</div>` after `@endforelse`) with:

```blade
            <!-- Portfolio Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @forelse($projects as $project)
                    @php
                        $projectImage = $project->imageSrc();
                        $projectTypeName = $projectTypes->firstWhere('slug', $project->category)?->getTranslatedName() ?? $project->category;
                    @endphp
                    <div class="portfolio-item group relative overflow-hidden rounded-2xl shadow-lg scroll-fade">
                        @if($projectImage)
                            <img src="{{ $projectImage }}"
                                 alt="{{ $project->getTranslatedTitle() }} — {{ $projectTypeName }}{{ $project->location ? ', '.$project->location : '' }} — {{ __('messages.alt_realisation') }}"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-72 object-cover transition-transform duration-500 group-hover:scale-110">
                        @else
                            <div class="w-full h-72 bg-gray-100 flex items-center justify-center">
                                <i data-lucide="image" class="w-10 h-10 text-gray-400"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                                <h3 class="text-xl font-bold mb-2">{{ $project->getTranslatedTitle() }}</h3>
                                <p class="text-sm text-gray-200 mb-2">{{ $project->location }}</p>
                                <span class="inline-block px-3 py-1 bg-blue-600 rounded-full text-xs">{{ $projectTypeName }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center">
                        <i data-lucide="image-off" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg max-w-xl mx-auto">{{ __('messages.portfolio_empty') }}</p>
                        <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center justify-center mt-6">
                            {{ __('messages.request_quote') }}
                        </a>
                    </div>
                @endforelse
            </div>
```

Note this also removes the `asset('storage/'.$project->image)` call, which was broken — there is no `public/storage` symlink.

- [ ] **Step 6: Un-skip the coherence test**

In `tests/Feature/SiteCoherenceTest.php`, the categories are no longer hardcoded in the Blade, so the regex-based test no longer applies. Replace `test_portfolio_filter_categories_are_canonical_service_slugs()` entirely with:

```php
    public function test_portfolio_filter_categories_come_from_the_database(): void
    {
        $this->assertStringNotContainsString(
            "route('portfolio', ['category' => '",
            File::get(resource_path('views/pages/portfolio.blade.php')),
            'Portfolio categories must be driven by ProjectType records, not hardcoded in the view.'
        );
    }
```

If this test class does not use `RefreshDatabase`, no database access is needed here — the assertion is purely on file contents.

- [ ] **Step 7: Run the tests**

```bash
php artisan test --compact tests/Feature/PortfolioVisibilityTest.php tests/Feature/SiteCoherenceTest.php tests/Feature/PublicSiteFeatureTest.php
```

Expected: PASS.

- [ ] **Step 8: Format and commit**

```bash
vendor/bin/pint --dirty
git add resources/views/pages/portfolio.blade.php app/Http/Controllers/PageController.php lang tests
git commit -m "feat(portfolio): database-driven categories, real images, honest empty state"
```

---

# PHASE 5 — Hero slides

## Task 14: `hero_slides` table and model

**Files:**
- Create: `database/migrations/<timestamp>_create_hero_slides_table.php`
- Create: `app/Models/HeroSlide.php`
- Test: `tests/Feature/HeroSlideTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/HeroSlideTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroSlideTest extends TestCase
{
    use RefreshDatabase;

    private function slide(array $attributes = []): HeroSlide
    {
        return HeroSlide::create(array_merge([
            'title' => ['fr' => 'Titre FR', 'en' => 'Title EN', 'ar' => 'عنوان'],
            'highlight' => ['fr' => 'Accroche FR'],
            'description' => ['fr' => 'Description FR'],
            'badge' => ['fr' => 'Badge FR'],
            'cta_type' => 'contact',
            'accent_color' => 'orange',
            'image_fit' => 'cover',
            'image_zoom' => 100,
            'focal_x' => 50,
            'focal_y' => 50,
            'is_active' => true,
            'sort_order' => 1,
        ], $attributes));
    }

    public function test_translated_accessors_fall_back_to_french(): void
    {
        $slide = $this->slide();

        $this->assertSame('Titre FR', $slide->getTranslatedTitle('fr'));
        $this->assertSame('Title EN', $slide->getTranslatedTitle('en'));
        $this->assertSame('Accroche FR', $slide->getTranslatedHighlight('ar'));
    }

    public function test_the_cta_url_resolves_from_the_cta_type(): void
    {
        $this->assertSame(route('contact'), $this->slide(['cta_type' => 'contact'])->ctaUrl());
        $this->assertSame(route('services'), $this->slide(['cta_type' => 'services'])->ctaUrl());
        $this->assertSame(
            'https://example.test/x',
            $this->slide(['cta_type' => 'custom', 'cta_url' => 'https://example.test/x'])->ctaUrl()
        );
        $this->assertNull($this->slide(['cta_type' => 'none'])->ctaUrl());
    }

    public function test_the_portfolio_cta_falls_back_to_services_when_the_page_is_hidden(): void
    {
        $this->assertSame(route('services'), $this->slide(['cta_type' => 'portfolio'])->ctaUrl());
    }

    public function test_active_and_ordered_scopes(): void
    {
        $this->slide(['sort_order' => 2]);
        $this->slide(['sort_order' => 1]);
        $this->slide(['sort_order' => 0, 'is_active' => false]);

        $this->assertSame([1, 2], HeroSlide::active()->ordered()->pluck('sort_order')->all());
    }

    public function test_the_homepage_renders_one_block_per_active_slide(): void
    {
        $this->slide(['title' => ['fr' => 'Premier Slide'], 'sort_order' => 1]);
        $this->slide(['title' => ['fr' => 'Second Slide'], 'sort_order' => 2]);
        $this->slide(['title' => ['fr' => 'Slide Caché'], 'sort_order' => 3, 'is_active' => false]);

        $response = $this->withSession(['locale' => 'fr'])->get(route('home'))->assertOk();

        $response->assertSee('Premier Slide');
        $response->assertSee('Second Slide');
        $response->assertDontSee('Slide Caché');

        $this->assertSame(2, substr_count($response->getContent(), 'class="carousel-slide'));
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact tests/Feature/HeroSlideTest.php`
Expected: FAIL — `Class "App\Models\HeroSlide" not found`.

- [ ] **Step 3: Create the migration**

Run: `php artisan make:migration create_hero_slides_table --no-interaction`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('image_url')->nullable();
            $table->json('alt_text')->nullable();
            $table->json('badge')->nullable();
            $table->string('badge_icon')->default('star');
            $table->json('title');
            $table->json('highlight')->nullable();
            $table->json('description')->nullable();
            $table->string('cta_type')->default('contact');
            $table->string('cta_url')->nullable();
            $table->json('cta_label')->nullable();
            $table->boolean('show_whatsapp')->default(false);
            $table->string('accent_color')->default('orange');
            $table->string('image_fit')->default('cover');
            $table->unsignedSmallInteger('image_zoom')->default(100);
            $table->unsignedTinyInteger('focal_x')->default(50);
            $table->unsignedTinyInteger('focal_y')->default(50);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
```

- [ ] **Step 4: Create the model**

Run: `php artisan make:model HeroSlide --no-interaction` then replace `app/Models/HeroSlide.php`:

```php
<?php

namespace App\Models;

use App\Models\Concerns\HasImageSource;
use App\Providers\ViewServiceProvider;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasImageSource;

    public const CTA_TYPES = ['quote', 'services', 'portfolio', 'contact', 'custom', 'none'];

    public const ACCENT_COLORS = ['orange', 'blue', 'cyan', 'emerald'];

    protected $fillable = [
        'image', 'image_url', 'alt_text', 'badge', 'badge_icon', 'title', 'highlight',
        'description', 'cta_type', 'cta_url', 'cta_label', 'show_whatsapp',
        'accent_color', 'image_fit', 'image_zoom', 'focal_x', 'focal_y',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'alt_text' => 'array',
        'badge' => 'array',
        'title' => 'array',
        'highlight' => 'array',
        'description' => 'array',
        'cta_label' => 'array',
        'show_whatsapp' => 'boolean',
        'is_active' => 'boolean',
        'image_zoom' => 'integer',
        'focal_x' => 'integer',
        'focal_y' => 'integer',
    ];

    public function getTranslatedTitle(?string $locale = null): string
    {
        return $this->translate('title', $locale);
    }

    public function getTranslatedHighlight(?string $locale = null): string
    {
        return $this->translate('highlight', $locale);
    }

    public function getTranslatedDescription(?string $locale = null): string
    {
        return $this->translate('description', $locale);
    }

    public function getTranslatedBadge(?string $locale = null): string
    {
        return $this->translate('badge', $locale);
    }

    public function getTranslatedCtaLabel(?string $locale = null): string
    {
        return $this->translate('cta_label', $locale);
    }

    public function getTranslatedAltText(?string $locale = null): string
    {
        return $this->translate('alt_text', $locale);
    }

    private function translate(string $attribute, ?string $locale): string
    {
        $locale = $locale ?? app()->getLocale();
        $values = $this->{$attribute} ?? [];

        return $values[$locale] ?? $values['fr'] ?? '';
    }

    /**
     * Resolves the CTA target. A slide pointing at the portfolio degrades to
     * the services page while that page is hidden, so the button is never dead.
     */
    public function ctaUrl(): ?string
    {
        return match ($this->cta_type) {
            'quote', 'contact' => route('contact'),
            'services' => route('services'),
            'portfolio' => ViewServiceProvider::portfolioEnabled() ? route('portfolio') : route('services'),
            'custom' => filled($this->cta_url) ? $this->cta_url : null,
            default => null,
        };
    }

    /** Inline style implementing the admin's zoom and focal-point settings. */
    public function imageStyle(): string
    {
        $scale = max(100, min(200, $this->image_zoom ?? 100)) / 100;

        return sprintf(
            'object-position: %d%% %d%%; transform: scale(%s);',
            $this->focal_x ?? 50,
            $this->focal_y ?? 50,
            rtrim(rtrim(number_format($scale, 2, '.', ''), '0'), '.')
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
```

- [ ] **Step 5: Run the test**

Run: `php artisan test --compact tests/Feature/HeroSlideTest.php`
Expected: the first four tests PASS; `test_the_homepage_renders_one_block_per_active_slide` FAILS (the Blade still hardcodes 4 slides). Task 17 fixes that.

- [ ] **Step 6: Format and commit**

```bash
php artisan migrate
vendor/bin/pint --dirty
git add database/migrations app/Models/HeroSlide.php tests/Feature/HeroSlideTest.php
git commit -m "feat(hero): add HeroSlide model with framing and CTA resolution"
```

---

## Task 15: Seed the current four slides verbatim

**Files:**
- Create: `database/seeders/HeroSlideSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Write the seeder**

Run: `php artisan make:seeder HeroSlideSeeder --no-interaction`

```php
<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Reproduces the four hero slides that were hardcoded in home.blade.php, so
 * the rendered homepage is unchanged the first time this runs.
 */
class HeroSlideSeeder extends Seeder
{
    private const LOCALES = ['fr', 'en', 'ar'];

    public function run(): void
    {
        foreach ($this->slides() as $index => $slide) {
            HeroSlide::updateOrCreate(
                ['sort_order' => $index + 1],
                $slide + ['is_active' => true, 'sort_order' => $index + 1]
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function slides(): array
    {
        $years = SiteSetting::get('stats_years', '15');

        return [
            [
                'image' => 'hero/slide-1.jpeg',
                'alt_text' => $this->lang('hero_slide1_alt'),
                'badge' => $this->setting('hero_badge', 'hero_values_badge'),
                'badge_icon' => 'star',
                'title' => $this->setting('hero_title', 'hero_title'),
                'highlight' => $this->setting('hero_subtitle', 'hero_subtitle'),
                'description' => $this->setting('hero_description', 'hero_description'),
                'cta_type' => 'contact',
                'cta_label' => $this->lang('request_quote'),
                'show_whatsapp' => true,
                'accent_color' => 'orange',
            ],
            [
                'image' => 'hero/slide-2.jpeg',
                'alt_text' => $this->lang('hero_slide2_alt'),
                'badge' => $this->lang('hero_slide2_badge'),
                'badge_icon' => 'home',
                'title' => $this->lang('hero_slide2_title'),
                'highlight' => $this->lang('hero_slide2_highlight'),
                'description' => $this->lang('hero_slide2_description'),
                'cta_type' => 'services',
                'cta_label' => $this->lang('learn_more'),
                'accent_color' => 'orange',
            ],
            [
                'image' => 'hero/slide-3.jpeg',
                'alt_text' => $this->lang('hero_slide3_alt'),
                'badge' => $this->lang('hero_slide3_badge'),
                'badge_icon' => 'layout-grid',
                'title' => $this->lang('hero_slide3_title'),
                'highlight' => $this->lang('hero_slide3_highlight'),
                'description' => $this->lang('hero_slide3_description'),
                'cta_type' => 'portfolio',
                'cta_label' => $this->lang('view_our_work'),
                'accent_color' => 'cyan',
            ],
            [
                'image' => 'hero/slide-4.jpeg',
                'alt_text' => $this->lang('hero_slide4_alt'),
                'badge' => $this->lang('hero_slide4_badge'),
                'badge_icon' => 'star',
                // Slide 4's heading was composed from a setting plus a string;
                // it is baked to literal text so the admin can edit it freely.
                'title' => $this->map(fn (string $locale): string => $years.'+ '.$this->line('years_experience', $locale)),
                'highlight' => $this->lang('hundreds_projects_completed'),
                'description' => $this->lang('hero_slide4_description'),
                'cta_type' => 'contact',
                'cta_label' => $this->lang('start_your_project'),
                'accent_color' => 'emerald',
            ],
        ];
    }

    /** @return array<string, string> */
    private function lang(string $key): array
    {
        return $this->map(fn (string $locale): string => $this->line($key, $locale));
    }

    /**
     * Prefers an existing SiteSetting value (which the admin may already have
     * edited), falling back to the lang file.
     *
     * @return array<string, string>
     */
    private function setting(string $settingKey, string $langKey): array
    {
        return $this->map(function (string $locale) use ($settingKey, $langKey): string {
            $value = SiteSetting::get("{$settingKey}_{$locale}");

            return filled($value) ? (string) $value : $this->line($langKey, $locale);
        });
    }

    /**
     * @param  callable(string): string  $resolver
     * @return array<string, string>
     */
    private function map(callable $resolver): array
    {
        $values = [];

        foreach (self::LOCALES as $locale) {
            $values[$locale] = $resolver($locale);
        }

        return $values;
    }

    private function line(string $key, string $locale): string
    {
        return (string) app('translator')->get("messages.{$key}", [], $locale);
    }
}
```

- [ ] **Step 2: Register it**

Add `HeroSlideSeeder::class,` to the `$this->call([...])` array in `database/seeders/DatabaseSeeder.php`.

- [ ] **Step 3: Copy the hero images onto the uploads disk**

The seeder stores `hero/slide-N.jpeg` as disk-relative paths, so the files must exist under `public/uploads/hero/`:

```bash
mkdir -p public/uploads/hero && cp public/images/hero/slide-*.jpeg public/uploads/hero/ && ls public/uploads/hero
```

Expected: `slide-1.jpeg slide-2.jpeg slide-3.jpeg slide-4.jpeg`.

- [ ] **Step 4: Seed and verify**

```bash
php artisan db:seed --class=HeroSlideSeeder --no-interaction
php artisan tinker --execute="foreach (\App\Models\HeroSlide::ordered()->get() as \$s) { echo \$s->sort_order.': '.\$s->getTranslatedTitle('fr').' | '.\$s->image.PHP_EOL; }"
```

Expected: four rows whose French titles match the current homepage.

- [ ] **Step 5: Format and commit**

```bash
vendor/bin/pint --dirty
git add database/seeders
git commit -m "feat(hero): seed the four existing slides into the database"
```

---

## Task 16: `HeroSlideResource`

**Files:**
- Create: `app/Filament/Resources/HeroSlideResource.php` and its `Pages/`

- [ ] **Step 1: Scaffold**

```bash
php artisan make:filament-resource HeroSlide --generate --no-interaction
```

- [ ] **Step 2: Replace the form and table**

Replace the generated `form()` and `table()` methods in `app/Filament/Resources/HeroSlideResource.php` with:

```php
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Slide')->tabs([
                Forms\Components\Tabs\Tab::make('Contenu')
                    ->icon('heroicon-o-language')
                    ->schema([
                        Forms\Components\Section::make('Badge')->schema([
                            Forms\Components\TextInput::make('badge.fr')->label('🇫🇷 Français'),
                            Forms\Components\TextInput::make('badge.en')->label('🇬🇧 English'),
                            Forms\Components\TextInput::make('badge.ar')->label('🇸🇦 العربية')->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3),
                        Forms\Components\Section::make('Titre')->schema([
                            Forms\Components\TextInput::make('title.fr')->label('🇫🇷 Français')->rules(['required', 'string']),
                            Forms\Components\TextInput::make('title.en')->label('🇬🇧 English'),
                            Forms\Components\TextInput::make('title.ar')->label('🇸🇦 العربية')->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3),
                        Forms\Components\Section::make('Accroche (deuxième ligne, colorée)')->schema([
                            Forms\Components\TextInput::make('highlight.fr')->label('🇫🇷 Français'),
                            Forms\Components\TextInput::make('highlight.en')->label('🇬🇧 English'),
                            Forms\Components\TextInput::make('highlight.ar')->label('🇸🇦 العربية')->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3),
                        Forms\Components\Section::make('Description')->schema([
                            Forms\Components\Textarea::make('description.fr')->label('🇫🇷 Français')->rows(2),
                            Forms\Components\Textarea::make('description.en')->label('🇬🇧 English')->rows(2),
                            Forms\Components\Textarea::make('description.ar')->label('🇸🇦 العربية')->rows(2)->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3),
                    ]),

                Forms\Components\Tabs\Tab::make('Image')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image de fond')
                            ->disk('uploads')
                            ->directory('hero')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['16:9', '21:9', null])
                            ->imagePreviewHeight('200')
                            ->maxSize(8192)
                            ->helperText('Recadrez et pivotez avec l\'éditeur. Format paysage recommandé.'),
                        Forms\Components\TextInput::make('image_url')
                            ->label('URL externe (optionnel)')
                            ->maxLength(2048)
                            ->rules(['nullable', 'string', 'max:2048']),
                        Forms\Components\Section::make('Cadrage')
                            ->description('Réglages non destructifs — modifiables à tout moment sans re-téléverser l\'image.')
                            ->schema([
                                Forms\Components\Radio::make('image_fit')
                                    ->label('Mode d\'affichage')
                                    ->options([
                                        'cover' => 'Remplir — l\'image remplit le cadre, les bords sont rognés',
                                        'contain' => 'Entier — toute l\'image est visible sur un fond flouté',
                                    ])
                                    ->default('cover')
                                    ->inline(false),
                                Forms\Components\TextInput::make('image_zoom')
                                    ->label('Zoom')
                                    ->numeric()
                                    ->minValue(100)
                                    ->maxValue(200)
                                    ->step(5)
                                    ->default(100)
                                    ->suffix('%')
                                    ->helperText('100 % = taille normale, 200 % = zoom maximal.'),
                                Forms\Components\TextInput::make('focal_x')
                                    ->label('Point focal horizontal')
                                    ->numeric()->minValue(0)->maxValue(100)->default(50)->suffix('%')
                                    ->helperText('0 % = gauche, 100 % = droite.'),
                                Forms\Components\TextInput::make('focal_y')
                                    ->label('Point focal vertical')
                                    ->numeric()->minValue(0)->maxValue(100)->default(50)->suffix('%')
                                    ->helperText('0 % = haut, 100 % = bas.'),
                            ])->columns(2),
                        Forms\Components\Section::make('Texte alternatif (accessibilité et SEO)')->schema([
                            Forms\Components\TextInput::make('alt_text.fr')->label('🇫🇷 Français'),
                            Forms\Components\TextInput::make('alt_text.en')->label('🇬🇧 English'),
                            Forms\Components\TextInput::make('alt_text.ar')->label('🇸🇦 العربية')->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3),
                    ]),

                Forms\Components\Tabs\Tab::make('Bouton & affichage')
                    ->icon('heroicon-o-cursor-arrow-rays')
                    ->schema([
                        Forms\Components\Select::make('cta_type')
                            ->label('Destination du bouton')
                            ->options([
                                'contact' => 'Page Contact',
                                'services' => 'Page Services',
                                'portfolio' => 'Page Réalisations (bascule vers Services si masquée)',
                                'custom' => 'URL personnalisée',
                                'none' => 'Aucun bouton',
                            ])
                            ->default('contact')
                            ->native(false)
                            ->live(),
                        Forms\Components\TextInput::make('cta_url')
                            ->label('URL personnalisée')
                            ->maxLength(2048)
                            ->visible(fn (Forms\Get $get): bool => $get('cta_type') === 'custom')
                            ->rules(['nullable', 'string', 'max:2048']),
                        Forms\Components\Section::make('Libellé du bouton')->schema([
                            Forms\Components\TextInput::make('cta_label.fr')->label('🇫🇷 Français'),
                            Forms\Components\TextInput::make('cta_label.en')->label('🇬🇧 English'),
                            Forms\Components\TextInput::make('cta_label.ar')->label('🇸🇦 العربية')->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3)->visible(fn (Forms\Get $get): bool => $get('cta_type') !== 'none'),
                        Forms\Components\Toggle::make('show_whatsapp')
                            ->label('Afficher aussi le bouton WhatsApp'),
                        Forms\Components\Select::make('accent_color')
                            ->label('Couleur d\'accent')
                            ->options([
                                'orange' => 'Orange', 'blue' => 'Bleu', 'cyan' => 'Cyan', 'emerald' => 'Vert',
                            ])
                            ->default('orange')
                            ->native(false),
                        Forms\Components\TextInput::make('badge_icon')
                            ->label('Icône du badge')
                            ->default('star')
                            ->helperText('Nom d\'icône Lucide, par ex. star, home, layout-grid, shield.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Slide visible')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Aperçu')
                    ->getStateUsing(fn (HeroSlide $record): ?string => $record->imageSrc()),
                Tables\Columns\TextColumn::make('title.fr')->label('Titre')->searchable(),
                Tables\Columns\TextColumn::make('cta_type')->label('Bouton')->badge(),
                Tables\Columns\IconColumn::make('is_active')->label('Visible')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Ordre')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Visible'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order');
    }
```

Set the resource's navigation properties near the top of the class:

```php
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $modelLabel = 'Slide d\'accueil';

    protected static ?string $pluralModelLabel = 'Slides d\'accueil';

    protected static ?int $navigationSort = 0;
```

Ensure the imports include `Filament\Forms`, `Filament\Forms\Form`, `Filament\Tables`, `Filament\Tables\Table`, and `App\Models\HeroSlide`.

- [ ] **Step 3: Manual check**

```bash
php artisan serve
```

At `/admin`, open **Contenu → Slides d'accueil**. Confirm four rows with image previews, drag-reorder works, and opening one shows the three tabs. Stop the server.

- [ ] **Step 4: Format and commit**

```bash
vendor/bin/pint --dirty
git add app/Filament/Resources/HeroSlideResource.php app/Filament/Resources/HeroSlideResource
git commit -m "feat(admin): add hero slide resource with framing controls"
```

---

## Task 17: Render the hero from the database

**Files:**
- Modify: `resources/views/pages/home.blade.php:1-183`
- Modify: `app/Http/Controllers/PageController.php` (`home()`)

- [ ] **Step 1: Pass the slides to the view**

In `PageController@home()`:

```php
    public function home()
    {
        $heroSlides = HeroSlide::active()->ordered()->get();
        $services = Service::active()->orderBy('sort_order')->get();
        $featuredProjects = Project::active()->featured()->orderBy('sort_order')->take(6)->get();
        $testimonials = Testimonial::active()->orderBy('sort_order')->take(3)->get();

        return view('pages.home', compact('heroSlides', 'services', 'featuredProjects', 'testimonials'));
    }
```

Add `use App\Models\HeroSlide;` to the imports.

- [ ] **Step 2: Replace the preload tag**

`resources/views/pages/home.blade.php`, replace the `@push('styles')` block (lines 15-18) with:

```blade
@push('styles')
    {{-- Preload the first hero slide (LCP element) so the browser fetches it before layout --}}
    @if($heroSlides->isNotEmpty() && $heroSlides->first()->imageSrc())
        <link rel="preload" as="image" fetchpriority="high" href="{{ $heroSlides->first()->imageSrc() }}">
    @endif
@endpush
```

The `@php` block at lines 2-9 computing `$heroBadge` is now dead — delete it, along with the `use App\Models\SiteSetting;` line **only if** `SiteSetting::` appears nowhere else in the file. Check with `grep -n "SiteSetting::" resources/views/pages/home.blade.php` before deleting.

- [ ] **Step 3: Replace the four hardcoded slides with a loop**

Replace everything from `<!-- Carousel Slides -->` (line 24) through the closing `</div>` that ends the `.carousel-slides` container (line 165), with:

```blade
            <!-- Carousel Slides -->
            @php
                $heroAccents = [
                    'orange' => ['badge' => 'bg-orange-500/20 text-orange-200 border-orange-300/30', 'highlight' => 'text-orange-300', 'shadow' => 'hover:shadow-orange-500/40'],
                    'blue' => ['badge' => 'bg-blue-500/20 text-blue-200 border-blue-300/30', 'highlight' => 'text-blue-300', 'shadow' => 'hover:shadow-blue-500/40'],
                    'cyan' => ['badge' => 'bg-blue-500/20 text-blue-200 border-blue-300/30', 'highlight' => 'text-cyan-200', 'shadow' => 'hover:shadow-blue-500/40'],
                    'emerald' => ['badge' => 'bg-green-500/20 text-green-200 border-green-300/30', 'highlight' => 'text-emerald-200', 'shadow' => 'hover:shadow-green-500/40'],
                ];
            @endphp
            <div class="carousel-slides relative h-full">
                @foreach($heroSlides as $slide)
                    @php
                        $accent = $heroAccents[$slide->accent_color] ?? $heroAccents['orange'];
                        $slideImage = $slide->imageSrc();
                        $ctaUrl = $slide->ctaUrl();
                        $ctaLabel = $slide->getTranslatedCtaLabel();
                    @endphp
                    <div class="carousel-slide {{ $loop->first ? 'active' : '' }} absolute inset-0 transition-opacity duration-1000 ease-in-out"
                         data-slide="{{ $loop->index }}"
                         data-order="{{ $loop->index }}"
                         style="opacity: {{ $loop->first ? '1' : '0' }}; z-index: {{ $loop->first ? '10' : '5' }};">
                        <div class="relative h-full overflow-hidden">
                            @if($slideImage)
                                @if($slide->image_fit === 'contain')
                                    {{-- Blurred backdrop fills the frame while the foreground shows the whole image --}}
                                    <img src="{{ $slideImage }}" alt="" aria-hidden="true"
                                         class="absolute inset-0 w-full h-full object-cover blur-2xl scale-110"
                                         loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async">
                                @endif
                                <img src="{{ $slideImage }}"
                                     alt="{{ $slide->getTranslatedAltText() }}"
                                     class="absolute inset-0 w-full h-full {{ $slide->image_fit === 'contain' ? 'object-contain' : 'object-cover' }}"
                                     style="{{ $slide->imageStyle() }}"
                                     @if($loop->first) loading="eager" fetchpriority="high" @else loading="lazy" @endif
                                     decoding="async">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                            <div class="absolute inset-0 flex items-center">
                                <div class="container mx-auto px-6 md:px-8 lg:px-12">
                                    <div class="max-w-3xl text-white slide-content pt-6 pb-24 md:pt-8 md:pb-28 lg:pt-10 lg:pb-32">
                                        @if($slide->getTranslatedBadge() !== '')
                                            <span class="inline-flex items-center px-4 py-2 backdrop-blur-md rounded-full text-sm font-semibold mb-6 md:mb-8 border shadow-lg {{ $accent['badge'] }}">
                                                <i data-lucide="{{ $slide->badge_icon ?: 'star' }}" class="w-4 h-4 me-2 flex-shrink-0"></i>
                                                {{ $slide->getTranslatedBadge() }}
                                            </span>
                                        @endif
                                        <{{ $loop->first ? 'h1' : 'h2' }} class="font-display text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold mb-6 md:mb-8 lg:mb-10 leading-[1.15] drop-shadow-2xl">
                                            {{ $slide->getTranslatedTitle() }}
                                            @if($slide->getTranslatedHighlight() !== '')
                                                <span class="{{ $accent['highlight'] }} block mt-3 md:mt-4 lg:mt-5">{{ $slide->getTranslatedHighlight() }}</span>
                                            @endif
                                        </{{ $loop->first ? 'h1' : 'h2' }}>
                                        @if($slide->getTranslatedDescription() !== '')
                                            <p class="text-base sm:text-lg md:text-xl mb-8 md:mb-10 lg:mb-12 text-gray-200 leading-relaxed max-w-2xl drop-shadow-lg">
                                                {{ $slide->getTranslatedDescription() }}
                                            </p>
                                        @endif
                                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 relative z-30">
                                            @if($ctaUrl)
                                                <a href="{{ $ctaUrl }}" class="btn-primary text-center inline-flex items-center justify-center group shadow-2xl {{ $accent['shadow'] }}">
                                                    {{ $ctaLabel !== '' ? $ctaLabel : __('messages.learn_more') }}
                                                    <i data-lucide="arrow-right" class="w-5 h-5 ms-2 flex-shrink-0 group-hover:translate-x-1 transition-transform rtl:rotate-180"></i>
                                                </a>
                                            @endif
                                            @if($slide->show_whatsapp)
                                                <button onclick="openWhatsApp()" class="btn-secondary inline-flex items-center justify-center group shadow-xl">
                                                    <i data-lucide="message-circle" class="w-5 h-5 me-2 flex-shrink-0 group-hover:scale-110 transition-transform"></i>
                                                    WhatsApp
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
```

- [ ] **Step 4: Generate the dots from the slide count**

Replace the `<!-- Dots Indicators -->` block (the four hardcoded `goToSlide(N)` buttons) with:

```blade
            <!-- Dots Indicators -->
            @if($heroSlides->count() > 1)
                <div class="carousel-dots absolute bottom-5 md:bottom-7 left-1/2 -translate-x-1/2 flex items-center gap-2.5 z-10 px-3 py-2 bg-black/30 backdrop-blur-sm rounded-full border border-white/20">
                    @foreach($heroSlides as $slide)
                        <button onclick="goToSlide({{ $loop->index }})"
                                class="carousel-dot w-2 h-2 md:w-2.5 md:h-2.5 rounded-full {{ $loop->first ? 'bg-white' : 'bg-white/40 hover:bg-white/60' }} shadow-sm transition-all duration-300 hover:scale-110"
                                aria-label="{{ __('messages.nav_home') }} {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            @endif
```

Also wrap the two navigation arrow buttons in `@if($heroSlides->count() > 1) ... @endif`.

- [ ] **Step 5: Guard the whole hero against zero slides**

Wrap the entire `<section class="relative overflow-hidden home-hero-section ...">` in `@if($heroSlides->isNotEmpty()) ... @endif`. Without this, an admin who deactivates every slide gets a full-height empty black band.

- [ ] **Step 6: Run the test**

Run: `php artisan test --compact tests/Feature/HeroSlideTest.php`
Expected: PASS, 5 tests.

- [ ] **Step 7: Verify the carousel at 1, 4 and 6 slides**

```bash
npm run build
php artisan optimize:clear
php artisan serve
```

At `/`, confirm 4 slides autoplay, arrows and dots work. Then in another shell:

```bash
php artisan tinker --execute="\App\Models\HeroSlide::whereIn('sort_order',[2,3,4])->update(['is_active'=>false]);"
```

Reload `/` — one slide, no arrows, no dots, no JS console errors. Then:

```bash
php artisan tinker --execute="
\App\Models\HeroSlide::query()->update(['is_active'=>true]);
foreach ([5,6] as \$n) {
  \$base = \App\Models\HeroSlide::where('sort_order',1)->first()->replicate();
  \$base->sort_order = \$n; \$base->title = ['fr'=>'Test '.\$n]; \$base->save();
}"
```

Reload — six slides, six dots, autoplay cycles through all of them. Clean up:

```bash
php artisan tinker --execute="\App\Models\HeroSlide::where('sort_order','>',4)->delete();"
```

Stop the server.

- [ ] **Step 8: Format and commit**

```bash
vendor/bin/pint --dirty
git add resources/views/pages/home.blade.php app/Http/Controllers/PageController.php
git commit -m "feat(hero): render the carousel from HeroSlide records"
```

---

## Task 18: Remove the duplicate hero editor from Site Settings

**Files:**
- Modify: `app/Filament/Pages/SiteSettings.php:56-104`

- [ ] **Step 1: Delete the tab**

Remove the entire `Forms\Components\Tabs\Tab::make('Accueil - Hero')` block. Slide 1 is now edited in **Contenu → Slides d'accueil**; leaving both would create two sources of truth.

The underlying `hero_badge_*`, `hero_title_*`, `hero_subtitle_*` and `hero_description_*` rows stay in `site_settings`, unread. Do not delete them — `HeroSlideSeeder` reads them when seeding a fresh database.

- [ ] **Step 2: Confirm nothing still reads those settings for display**

Run: `grep -rn "hero_title\|hero_subtitle\|hero_description\|hero_badge" resources/views/ app/`
Expected: hits only in `database/seeders/HeroSlideSeeder.php`. If `resources/views/pages/home.blade.php` still appears, Task 17 was incomplete.

- [ ] **Step 3: Run the suite and commit**

```bash
php artisan test --compact
vendor/bin/pint --dirty
git add app/Filament/Pages/SiteSettings.php
git commit -m "refactor(admin): remove the hero tab now superseded by hero slides"
```

---

# PHASE 6 — Testimonials and FAQ

## Task 19: Real testimonials in the database

**Files:**
- Create: `database/seeders/TestimonialSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Modify: `resources/views/pages/home.blade.php` (testimonials `@forelse`)
- Modify: `app/Filament/Resources/TestimonialResource.php`
- Test: `tests/Feature/PublicSiteFeatureTest.php`

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/PublicSiteFeatureTest.php`, inside the class:

```php
    public function test_the_homepage_shows_no_invented_testimonials_when_the_table_is_empty(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Mohamed B.')
            ->assertDontSee('Sonia K.')
            ->assertDontSee('Ahmed T.');
    }

    public function test_the_testimonial_seeder_populates_the_previously_hardcoded_reviews(): void
    {
        $this->seed(\Database\Seeders\TestimonialSeeder::class);

        $this->withSession(['locale' => 'fr'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Mohamed B.')
            ->assertSee('Sonia K.')
            ->assertSee('Ahmed T.');
    }
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter=testimonial`
Expected: the first FAILS (hardcoded names still render), the second FAILS (no seeder class).

- [ ] **Step 3: Write the seeder**

Run: `php artisan make:seeder TestimonialSeeder --no-interaction`

```php
<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * The quote text lived in lang/*; the client names and locations were
 * hardcoded in home.blade.php markup. Both are consolidated here so the
 * admin panel is the single source of truth.
 */
class TestimonialSeeder extends Seeder
{
    private const LOCALES = ['fr', 'en', 'ar'];

    public function run(): void
    {
        $testimonials = [
            ['key' => 'testimonial_1', 'client_name' => 'Mohamed B.', 'client_location' => 'Paris, France', 'sort_order' => 1],
            ['key' => 'testimonial_2', 'client_name' => 'Sonia K.', 'client_location' => 'Montréal, Canada', 'sort_order' => 2],
            ['key' => 'testimonial_3', 'client_name' => 'Ahmed T.', 'client_location' => 'Berlin, Allemagne', 'sort_order' => 3],
        ];

        foreach ($testimonials as $testimonial) {
            $content = [];

            foreach (self::LOCALES as $locale) {
                $content[$locale] = (string) app('translator')->get("messages.{$testimonial['key']}", [], $locale);
            }

            Testimonial::updateOrCreate(
                ['client_name' => $testimonial['client_name']],
                [
                    'client_location' => $testimonial['client_location'],
                    'content' => $content,
                    'rating' => 5,
                    'is_active' => true,
                    'sort_order' => $testimonial['sort_order'],
                ]
            );
        }
    }
}
```

Register `TestimonialSeeder::class,` in `database/seeders/DatabaseSeeder.php`.

- [ ] **Step 4: Replace the hardcoded fallback**

In `resources/views/pages/home.blade.php`, replace everything between `@empty` and `@endforelse` in the testimonials section (the three hardcoded cards, currently lines 588-647) with:

```blade
                @empty
                    <div class="col-span-full py-10 text-center">
                        <p class="text-gray-500">{{ __('messages.testimonials_subtitle') }}</p>
                    </div>
                @endforelse
```

Read the `@forelse` opening line first and confirm the loop variable, then verify the surviving card markup uses `$testimonial->getTranslatedContent()`, `$testimonial->client_name` and `$testimonial->client_location`. If the loop body still references lang keys, replace those with the model accessors.

- [ ] **Step 5: Add the photo upload to `TestimonialResource`**

Read `app/Filament/Resources/TestimonialResource.php`. Ensure the form contains:

```php
                Forms\Components\FileUpload::make('client_photo')
                    ->label('Photo du client')
                    ->disk('uploads')
                    ->directory('testimonials')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    ->avatar()
                    ->imagePreviewHeight('120')
                    ->maxSize(3072),
```

and that the table has `->reorderable('sort_order')` with `->defaultSort('sort_order')`, plus:

```php
                Tables\Columns\ImageColumn::make('client_photo')
                    ->label('Photo')
                    ->circular()
                    ->getStateUsing(fn (\App\Models\Testimonial $record): ?string => $record->photoSrc()),
```

- [ ] **Step 6: Seed and verify**

```bash
php artisan db:seed --class=TestimonialSeeder --no-interaction
php artisan test --compact --filter=testimonial
```

Expected: PASS.

- [ ] **Step 7: Format and commit**

```bash
vendor/bin/pint --dirty
git add database/seeders resources/views/pages/home.blade.php app/Filament/Resources/TestimonialResource.php tests/Feature/PublicSiteFeatureTest.php
git commit -m "feat(content): move testimonials into the database and drop hardcoded reviews"
```

---

## Task 20: Stop overwriting FAQ #2

**Files:**
- Modify: `app/Http/Controllers/PageController.php` (`contact()`)
- Modify: `database/seeders/faqs.json` (only if #2's stored text differs from the lang file)
- Test: `tests/Feature/PublicSiteFeatureTest.php`

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/PublicSiteFeatureTest.php`, inside the class:

```php
    public function test_an_admin_edit_to_faq_two_is_shown_on_the_contact_page(): void
    {
        Faq::create([
            'question' => ['fr' => 'Question modifiée par admin', 'en' => 'Edited', 'ar' => 'سؤال'],
            'answer' => ['fr' => 'Réponse modifiée par admin', 'en' => 'Edited', 'ar' => 'جواب'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->withSession(['locale' => 'fr'])
            ->get(route('contact'))
            ->assertOk()
            ->assertSee('Question modifiée par admin')
            ->assertSee('Réponse modifiée par admin');
    }
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter=faq_two`
Expected: FAIL — the controller replaces the record's text with the lang-file version.

- [ ] **Step 3: Simplify the controller**

Replace `contact()` in `app/Http/Controllers/PageController.php` with:

```php
    public function contact()
    {
        $faqs = Faq::active()->ordered()->get();

        return view('pages.contact', compact('faqs'));
    }
```

Remove the now-unused `$translator` variable and, if nothing else in the file uses it, the `app('translator')` call.

- [ ] **Step 4: Make sure the seeded FAQ #2 matches what was being injected**

The override existed because the stored FAQ #2 text was stale. Compare:

```bash
php artisan tinker --execute="\$f = \App\Models\Faq::where('sort_order',2)->first(); echo json_encode(\$f?->question, JSON_UNESCAPED_UNICODE).PHP_EOL;"
grep -n "faq_q2" lang/fr/messages.php lang/en/messages.php lang/ar/messages.php
```

If they differ, update the matching entry in `database/seeders/faqs.json` to the lang-file wording (all three locales), then:

```bash
php artisan db:seed --class=FaqSeeder --no-interaction
```

Confirm the contact page still shows the intended question.

- [ ] **Step 5: Run the tests**

Run: `php artisan test --compact tests/Feature/PublicSiteFeatureTest.php`
Expected: PASS.

- [ ] **Step 6: Format and commit**

```bash
vendor/bin/pint --dirty
git add app/Http/Controllers/PageController.php database/seeders tests/Feature/PublicSiteFeatureTest.php
git commit -m "fix(content): stop overwriting FAQ #2 from lang files on every request"
```

---

# PHASE 7 — Verification

## Task 21: Full verification pass

- [ ] **Step 1: Fresh database rebuild**

```bash
php artisan migrate:fresh --seed
```

Expected: no errors. This proves the seeders and the `Schema::hasTable` guard in `ViewServiceProvider` cooperate on a cold cache.

- [ ] **Step 2: Re-import media and re-copy hero images**

```bash
php artisan media:import
mkdir -p public/uploads/hero && cp public/images/hero/slide-*.jpeg public/uploads/hero/
```

- [ ] **Step 3: Full test suite**

Run: `php artisan test --compact`
Expected: all green. Record the exact pass/fail counts.

- [ ] **Step 4: Lint**

```bash
vendor/bin/pint --dirty
```

Expected: no changes, or only formatting fixes — commit them if any.

- [ ] **Step 5: Production asset build**

```bash
npm run build
```

Expected: succeeds. Tailwind must have picked up the accent classes from the `$heroAccents` map in `home.blade.php`.

- [ ] **Step 6: Manual admin walkthrough**

```bash
php artisan serve
```

At `/admin` (`admin@aluminiumcraft.tn` / `password`), verify each of these end to end:

1. **Contenu → Services →** any service **→ Médias**: upload an image, see the preview, drag two gallery thumbnails to swap them, save. No validation error. Reload the public `/services` page and confirm the new order.
2. **Contenu → Services**: drag a service to a new position in the list; confirm the homepage card order changes.
3. **Contenu → Slides d'accueil**: change a slide's zoom to 150% and focal point to 30/70, save, reload `/`, confirm the framing changed. Set one slide to *Entier* and confirm the blurred backdrop appears.
4. **Contenu → Types de projet**: add a fourth type; confirm it appears in the Projects category dropdown.
5. **Contenu → Projets**: create a project with an uploaded image.
6. **Paramètres du site → Pages & visibilité**: enable Réalisations; confirm the nav link, footer link and `/portfolio` all appear and the new project shows with its real image. Disable it again; confirm 404 and no links anywhere.
7. **Contenu → Témoignages**: edit a testimonial's text; confirm the homepage updates.

Stop the server.

- [ ] **Step 7: Final commit**

```bash
git status
vendor/bin/pint --dirty
git add -A
git commit -m "chore: verification pass for admin-manageable content"
```

---

## Deferred (explicitly not built)

- Static UI strings in `lang/{fr,en,ar}` remain in code. Buttons, nav labels, section headings and SEO strings are not admin-editable.
- Homepage section on/off toggles (services preview, testimonials, the commented-out "Why Choose Us" and "TOP Produits" blocks) were offered and declined.
- `public/images/**` originals are left in place after `media:import`. Deleting them is a separate cleanup once the uploads disk is proven in production.

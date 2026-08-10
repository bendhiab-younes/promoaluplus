<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MediaImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Slugs are namespaced so the command writes into throwaway directories.
     * Deleting public/uploads/services wholesale would destroy real imported
     * media, since these tests run against the live public/ tree rather than
     * a faked disk (the command copies with File::copy, not Storage).
     */
    private const SLUG_PREFIX = 'mediaimporttest-';

    protected function tearDown(): void
    {
        File::deleteDirectory(public_path('images/mediaimporttest'));

        foreach (File::directories(public_path('uploads/services')) as $directory) {
            if (str_starts_with(basename($directory), self::SLUG_PREFIX)) {
                File::deleteDirectory($directory);
            }
        }

        parent::tearDown();
    }

    public function test_it_copies_legacy_local_images_and_rewrites_the_database(): void
    {
        $source = public_path('images/mediaimporttest');
        File::ensureDirectoryExists($source);
        File::put($source.'/pic.jpeg', 'image-bytes');
        File::put($source.'/pic-thumb.jpeg', 'thumb-bytes');

        $service = Service::create([
            'slug' => self::SLUG_PREFIX.'windows',
            'title' => ['fr' => 'Fenêtres'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'image' => '/images/mediaimporttest/pic.jpeg',
            'gallery' => ['/images/mediaimporttest/pic.jpeg'],
            'is_active' => true,
        ]);

        $this->artisan('media:import')->assertExitCode(0);

        $service->refresh();

        $this->assertSame('services/'.self::SLUG_PREFIX.'windows/pic.jpeg', $service->image);
        $this->assertSame(['services/'.self::SLUG_PREFIX.'windows/pic.jpeg'], $service->gallery);
        $this->assertFileExists(public_path('uploads/services/'.self::SLUG_PREFIX.'windows/pic.jpeg'));
        $this->assertFileExists(public_path('uploads/services/'.self::SLUG_PREFIX.'windows/pic-thumb.jpeg'));
    }

    public function test_it_is_idempotent(): void
    {
        $source = public_path('images/mediaimporttest');
        File::ensureDirectoryExists($source);
        File::put($source.'/pic.jpeg', 'image-bytes');

        $service = Service::create([
            'slug' => self::SLUG_PREFIX.'doors',
            'title' => ['fr' => 'Portes'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'image' => '/images/mediaimporttest/pic.jpeg',
            'is_active' => true,
        ]);

        $this->artisan('media:import')->assertExitCode(0);
        $this->artisan('media:import')->assertExitCode(0);

        $this->assertSame('services/'.self::SLUG_PREFIX.'doors/pic.jpeg', $service->refresh()->image);
    }

    public function test_it_leaves_unreachable_values_untouched(): void
    {
        $service = Service::create([
            'slug' => self::SLUG_PREFIX.'pergola',
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

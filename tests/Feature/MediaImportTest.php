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

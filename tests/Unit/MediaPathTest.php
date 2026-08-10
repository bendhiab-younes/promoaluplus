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

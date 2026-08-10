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

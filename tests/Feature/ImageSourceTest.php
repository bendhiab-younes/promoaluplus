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

    /**
     * The thumbnail (main image) and the gallery are independent admin
     * fields, so the thumbnail must win as the featured image — an admin
     * who sets both should not have the gallery silently override it.
     */
    public function test_featured_image_prefers_the_main_thumbnail_over_the_gallery(): void
    {
        $service = $this->service([
            'image' => 'services/windows/main.jpeg',
            'gallery' => ['services/windows/g1.jpeg', 'services/windows/g2.jpeg'],
        ]);

        $this->assertSame(asset('uploads/services/windows/main.jpeg'), $service->getFeaturedImage());
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

    /**
     * Regression: a thumbnail set independently of the gallery used to
     * disappear entirely whenever the gallery was non-empty. The thumbnail
     * must lead the list, and must not be duplicated if it also happens to
     * be present in the gallery array.
     */
    public function test_the_main_thumbnail_leads_the_gallery_when_both_are_set(): void
    {
        $service = $this->service([
            'image' => 'services/windows/main.jpeg',
            'gallery' => ['services/windows/g1.jpeg', 'services/windows/g2.jpeg'],
        ]);

        $this->assertSame([
            asset('uploads/services/windows/main.jpeg'),
            asset('uploads/services/windows/g1.jpeg'),
            asset('uploads/services/windows/g2.jpeg'),
        ], $service->getGalleryImages());
    }

    public function test_the_main_thumbnail_is_not_duplicated_when_it_is_also_in_the_gallery(): void
    {
        $service = $this->service([
            'image' => 'services/windows/main.jpeg',
            'gallery' => ['services/windows/main.jpeg', 'services/windows/g2.jpeg'],
        ]);

        $this->assertSame([
            asset('uploads/services/windows/main.jpeg'),
            asset('uploads/services/windows/g2.jpeg'),
        ], $service->getGalleryImages());
    }
}

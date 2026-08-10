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

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
}

<?php

namespace Tests\Feature;

use App\Filament\Resources\HeroSlideResource\Pages\CreateHeroSlide;
use App\Filament\Resources\HeroSlideResource\Pages\EditHeroSlide;
use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HeroSlideResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // App\Models\User does not implement FilamentUser, so Filament's
        // panel middleware 403s outside the local env.
        config(['app.env' => 'local']);
        $this->actingAs(User::factory()->create());
    }

    public function test_the_index_and_create_pages_render(): void
    {
        $this->get('/admin/hero-slides')->assertOk();
        $this->get('/admin/hero-slides/create')->assertOk();
    }

    public function test_creating_a_slide_persists_translated_fields(): void
    {
        Livewire::test(CreateHeroSlide::class)
            ->set('data.title.fr', 'Fenêtres sur mesure')
            ->set('data.title.en', 'Custom windows')
            ->set('data.title.ar', 'نوافذ مخصصة')
            ->set('data.cta_type', 'contact')
            ->call('create')
            ->assertHasNoFormErrors();

        $slide = HeroSlide::firstOrFail();

        $this->assertSame('Fenêtres sur mesure', $slide->title['fr']);
        $this->assertSame('Custom windows', $slide->title['en']);
        $this->assertSame('نوافذ مخصصة', $slide->title['ar']);
    }

    /**
     * title.fr lives in a non-default tab, so its requirement is enforced via
     * ->rules(['required', ...])->markAsRequired() rather than ->required() —
     * this proves that server-side rule actually fires.
     */
    public function test_a_blank_french_title_reports_a_form_error(): void
    {
        Livewire::test(CreateHeroSlide::class)
            ->set('data.title.fr', '')
            ->set('data.cta_type', 'contact')
            ->call('create')
            ->assertHasFormErrors(['title.fr']);

        $this->assertSame(0, HeroSlide::count());
    }

    /**
     * image_zoom, focal_x, and focal_y map to NOT NULL columns with no
     * ->required() on the field (they sit in a Section inside a non-default
     * tab). Without a server-side rule, blanking one in the admin would send
     * null straight to the database and raise a QueryException instead of a
     * validation message.
     */
    public function test_blanking_a_framing_field_reports_a_form_error_instead_of_crashing(): void
    {
        $slide = HeroSlide::create([
            'title' => ['fr' => 'Portes'],
            'cta_type' => 'contact',
            'image_zoom' => 150,
            'focal_x' => 30,
            'focal_y' => 70,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(EditHeroSlide::class, ['record' => $slide->getKey()])
            ->set('data.image_zoom', '')
            ->set('data.focal_x', '')
            ->call('save')
            ->assertHasFormErrors();

        $slide->refresh();
        $this->assertSame(150, $slide->image_zoom);
        $this->assertSame(30, $slide->focal_x);
    }

    public function test_framing_fields_persist_and_drive_image_style(): void
    {
        $slide = HeroSlide::create([
            'title' => ['fr' => 'Portes'],
            'cta_type' => 'contact',
            'image_fit' => 'cover',
            'image_zoom' => 100,
            'focal_x' => 50,
            'focal_y' => 50,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(EditHeroSlide::class, ['record' => $slide->getKey()])
            ->set('data.image_zoom', 150)
            ->set('data.focal_x', 30)
            ->set('data.focal_y', 70)
            ->call('save')
            ->assertHasNoFormErrors();

        $slide->refresh();

        $this->assertSame(150, $slide->image_zoom);
        $this->assertSame(30, $slide->focal_x);
        $this->assertSame(70, $slide->focal_y);

        $style = $slide->imageStyle();
        $this->assertStringContainsString('scale(1.5)', $style);
        $this->assertStringContainsString('object-position: 30% 70%', $style);
    }

    public function test_an_external_image_url_survives_an_unrelated_edit(): void
    {
        $slide = HeroSlide::create([
            'title' => ['fr' => 'Façades'],
            'cta_type' => 'contact',
            'image' => null,
            'image_url' => 'https://images.unsplash.com/photo-hero.jpg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(EditHeroSlide::class, ['record' => $slide->getKey()])
            ->set('data.title.fr', 'Façades Modifiées')
            ->call('save')
            ->assertHasNoFormErrors();

        $slide->refresh();

        $this->assertSame('Façades Modifiées', $slide->title['fr']);
        $this->assertSame('https://images.unsplash.com/photo-hero.jpg', $slide->image_url);
    }
}

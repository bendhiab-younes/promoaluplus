<?php

namespace Tests\Feature;

use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Filament\Resources\ServiceResource\Pages\ListServices;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    /**
     * FileUpload only keeps gallery entries that actually exist on disk when
     * it hydrates (BaseFileUpload::setUp() checks Disk::exists() per file) —
     * so the picker's tests need real files behind the fake paths, not just
     * strings in the 'gallery' column.
     */
    private function fakeGalleryFiles(array $relativePaths): void
    {
        Storage::fake('uploads');

        foreach ($relativePaths as $path) {
            Storage::disk('uploads')->put($path, 'fake-image-content');
        }
    }

    /**
     * The admin can designate any already-uploaded gallery photo as the
     * service's card thumbnail instead of uploading a duplicate file.
     */
    public function test_admin_can_pick_a_gallery_image_as_the_service_thumbnail(): void
    {
        $this->fakeGalleryFiles(['services/doors/a.jpeg', 'services/doors/b.jpeg']);

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
            ->assertFormFieldIsVisible('gallery_thumbnail_picker')
            ->set('data.gallery_thumbnail_picker', 'services/doors/b.jpeg')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('services/doors/b.jpeg', $service->refresh()->image);
    }

    public function test_the_gallery_thumbnail_picker_is_hidden_when_the_gallery_is_empty(): void
    {
        $service = Service::create([
            'slug' => 'doors',
            'title' => ['fr' => 'Portes', 'en' => 'Doors', 'ar' => 'أبواب'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(EditService::class, ['record' => $service->getKey()])
            ->assertFormFieldIsHidden('gallery_thumbnail_picker');
    }

    /**
     * The picker pre-selects whichever gallery image is already the current
     * thumbnail, so re-opening a service doesn't look like nothing was ever
     * chosen.
     */
    public function test_the_gallery_thumbnail_picker_preselects_the_current_image(): void
    {
        $this->fakeGalleryFiles(['services/doors/a.jpeg', 'services/doors/b.jpeg']);

        $service = Service::create([
            'slug' => 'doors',
            'title' => ['fr' => 'Portes', 'en' => 'Doors', 'ar' => 'أبواب'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'image' => 'services/doors/a.jpeg',
            'gallery' => ['services/doors/a.jpeg', 'services/doors/b.jpeg'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(EditService::class, ['record' => $service->getKey()])
            ->assertFormSet(['gallery_thumbnail_picker' => 'services/doors/a.jpeg']);
    }

    /**
     * Walkthrough item: "upload an image, confirm the preview renders" — the
     * preview render is a browser concern, but this proves the half a
     * Livewire test can: a real UploadedFile through the FileUpload field
     * lands on the 'uploads' disk and the path is persisted.
     */
    public function test_uploading_a_service_image_persists_it_on_the_uploads_disk(): void
    {
        Storage::fake('uploads');

        $service = Service::create([
            'slug' => 'windows',
            'title' => ['fr' => 'Fenêtres', 'en' => 'Windows', 'ar' => 'نوافذ'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(EditService::class, ['record' => $service->getKey()])
            ->set('data.image', UploadedFile::fake()->image('window.jpg'))
            ->call('save')
            ->assertHasNoFormErrors();

        $service->refresh();

        $this->assertNotNull($service->image);
        Storage::disk('uploads')->assertExists($service->image);
        $this->assertStringStartsWith('services/', $service->image);
    }

    /**
     * Walkthrough item: "reorder services and check the homepage" — proves
     * both halves: the admin table's drag-reorder action (Filament's
     * reorderTable Livewire method) persists sort_order, and the public
     * services page reflects the new order.
     */
    public function test_reordering_services_in_admin_changes_the_order_on_the_services_page(): void
    {
        $first = Service::create([
            'slug' => 'windows',
            'title' => ['fr' => 'Fenêtres'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $second = Service::create([
            'slug' => 'doors',
            'title' => ['fr' => 'Portes'],
            'short_description' => ['fr' => 'Courte'],
            'description' => ['fr' => 'Longue'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Livewire::test(ListServices::class)
            ->call('reorderTable', [$second->getKey(), $first->getKey()]);

        $this->assertSame(1, $second->refresh()->sort_order);
        $this->assertSame(2, $first->refresh()->sort_order);

        $html = $this->withSession(['locale' => 'fr'])->get(route('services'))->getContent();

        $this->assertLessThan(
            strpos($html, 'Fenêtres'),
            strpos($html, 'Portes'),
            'Portes should now render before Fenêtres on the public services page.'
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

    public function test_a_project_external_image_url_survives_an_unrelated_edit(): void
    {
        ProjectType::create(['name' => ['fr' => 'Fenêtres'], 'slug' => 'windows', 'order' => 1, 'is_active' => true]);

        $project = Project::create([
            'title' => ['fr' => 'Villa Test', 'en' => 'Test Villa', 'ar' => 'فيلا'],
            'description' => ['fr' => 'Desc'],
            'category' => 'windows',
            'image' => null,
            'image_url' => 'https://images.unsplash.com/photo-9.jpg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(EditProject::class, ['record' => $project->getKey()])
            ->set('data.title.fr', 'Villa Modifiée')
            ->call('save')
            ->assertHasNoFormErrors();

        $project->refresh();

        $this->assertSame('Villa Modifiée', $project->title['fr']);
        $this->assertSame('https://images.unsplash.com/photo-9.jpg', $project->image_url);
    }

    /**
     * The category Select must read its options from ProjectType records rather than a
     * hardcoded list: active types (including one that isn't one of the original
     * windows/doors/facades trio) appear, an inactive type does not, and a category with
     * no backing record ("facades", here deliberately absent) never leaks in.
     */
    public function test_the_project_category_options_come_from_project_types(): void
    {
        ProjectType::create(['name' => ['fr' => 'Fenêtres'], 'slug' => 'windows', 'order' => 1, 'is_active' => true]);
        ProjectType::create(['name' => ['fr' => 'Pergolas'], 'slug' => 'pergola', 'order' => 2, 'is_active' => true]);
        ProjectType::create(['name' => ['fr' => 'Masqué'], 'slug' => 'hidden', 'order' => 3, 'is_active' => false]);

        Livewire::test(CreateProject::class)
            ->assertFormFieldExists('category', function (Select $field): bool {
                $options = $field->getOptions();

                return array_key_exists('windows', $options)
                    && array_key_exists('pergola', $options)
                    && ! array_key_exists('hidden', $options)
                    && ! array_key_exists('facades', $options);
            });
    }

    /**
     * Walkthrough item: "create a project with an uploaded image" — proves
     * a project created through the admin form with a real uploaded file
     * (not an external URL) is both persisted with a resolvable image and
     * visible on the public portfolio page.
     */
    public function test_creating_a_project_with_an_uploaded_image_appears_on_the_portfolio_page(): void
    {
        Storage::fake('uploads');
        SiteSetting::set('portfolio_enabled', '1', 'boolean', 'pages');
        ProjectType::create(['name' => ['fr' => 'Fenêtres'], 'slug' => 'windows', 'order' => 1, 'is_active' => true]);

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => ['fr' => 'Villa Créée En Admin'],
                'category' => 'windows',
                'image' => UploadedFile::fake()->image('villa.jpg'),
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $project = Project::where('category', 'windows')->firstOrFail();

        Storage::disk('uploads')->assertExists($project->image);

        $this->withSession(['locale' => 'fr'])
            ->get(route('portfolio'))
            ->assertOk()
            ->assertSee('Villa Créée En Admin');
    }
}

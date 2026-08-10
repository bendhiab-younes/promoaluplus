<?php

namespace Tests\Feature;

use App\Models\ProjectType;
use Illuminate\Database\QueryException;
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

    public function test_the_slug_is_unique_at_the_database_level(): void
    {
        ProjectType::create(['name' => ['fr' => 'Fenêtres'], 'slug' => 'windows', 'order' => 1, 'is_active' => true]);

        $this->expectException(QueryException::class);

        ProjectType::create(['name' => ['fr' => 'Autres fenêtres'], 'slug' => 'windows', 'order' => 2, 'is_active' => true]);
    }
}

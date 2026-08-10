<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['slug' => 'windows', 'order' => 1, 'color' => 'info', 'icon' => 'app-window',
                'name' => ['fr' => 'Fenêtres', 'en' => 'Windows', 'ar' => 'نوافذ']],
            ['slug' => 'doors', 'order' => 2, 'color' => 'warning', 'icon' => 'door-open',
                'name' => ['fr' => 'Portes', 'en' => 'Doors', 'ar' => 'أبواب']],
            ['slug' => 'facades', 'order' => 3, 'color' => 'success', 'icon' => 'layout-grid',
                'name' => ['fr' => 'Façades', 'en' => 'Facades', 'ar' => 'واجهات']],
        ];

        foreach ($types as $type) {
            ProjectType::updateOrCreate(
                ['slug' => $type['slug']],
                $type + ['is_active' => true]
            );
        }
    }
}

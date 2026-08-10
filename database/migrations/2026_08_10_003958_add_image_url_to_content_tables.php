<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const AFTER_COLUMN_BY_TABLE = [
        'services' => 'image',
        'projects' => 'image',
        'testimonials' => 'client_photo',
    ];

    public function up(): void
    {
        foreach (self::AFTER_COLUMN_BY_TABLE as $table => $afterColumn) {
            Schema::table($table, function (Blueprint $blueprint) use ($afterColumn) {
                $blueprint->string('image_url')->nullable()->after($afterColumn);
            });
        }

        // Move any existing absolute URL out of the upload column so that a
        // FileUpload component can never fail to hydrate it and wipe it on save.
        foreach (['services', 'projects'] as $table) {
            DB::table($table)
                ->where('image', 'like', 'http%')
                ->update([
                    'image_url' => DB::raw('image'),
                    'image' => null,
                ]);
        }
    }

    public function down(): void
    {
        foreach (array_keys(self::AFTER_COLUMN_BY_TABLE) as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('image_url');
            });
        }
    }
};

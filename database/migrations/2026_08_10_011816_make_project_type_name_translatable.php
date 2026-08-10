<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The table is empty in every known environment, so a plain type
        // change is safe. Any stray row is converted to the fr-only shape.
        foreach (DB::table('project_types')->get() as $row) {
            if (! str_starts_with((string) $row->name, '{')) {
                DB::table('project_types')
                    ->where('id', $row->id)
                    ->update(['name' => json_encode(['fr' => $row->name], JSON_UNESCAPED_UNICODE)]);
            }
        }

        Schema::table('project_types', function (Blueprint $table) {
            $table->json('name')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_types', function (Blueprint $table) {
            $table->string('name')->change();
        });
    }
};

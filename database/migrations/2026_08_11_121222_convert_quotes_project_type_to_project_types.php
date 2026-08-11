<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A devis can now be requested for several project types at once (e.g. doors
 * + windows + pergola in the same request), so the single project_type
 * string becomes a project_types JSON array. Existing rows are backfilled
 * as a single-element array so no data is lost.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->json('project_types')->nullable()->after('project_type');
        });

        DB::table('quotes')->select('id', 'project_type')->orderBy('id')->each(function (object $row) {
            DB::table('quotes')->where('id', $row->id)->update([
                'project_types' => json_encode(array_values(array_filter([$row->project_type]))),
            ]);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropIndex('quotes_project_type_index');
            $table->dropColumn('project_type');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('project_type')->nullable()->after('description');
        });

        DB::table('quotes')->select('id', 'project_types')->orderBy('id')->each(function (object $row) {
            $types = json_decode((string) $row->project_types, true) ?? [];

            DB::table('quotes')->where('id', $row->id)->update([
                'project_type' => $types[0] ?? 'other',
            ]);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->string('project_type')->nullable(false)->change();
            $table->index('project_type');
            $table->dropColumn('project_types');
        });
    }
};

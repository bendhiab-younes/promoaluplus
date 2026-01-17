<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('svg_icon')->nullable()->after('icon'); // Full SVG markup for complex icons
            $table->json('gallery')->nullable()->after('image'); // Array of image URLs/paths
            $table->json('materials')->nullable()->after('features'); // Translatable materials array
            $table->json('specs')->nullable()->after('materials'); // Key-value specifications
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['svg_icon', 'gallery', 'materials', 'specs']);
        });
    }
};

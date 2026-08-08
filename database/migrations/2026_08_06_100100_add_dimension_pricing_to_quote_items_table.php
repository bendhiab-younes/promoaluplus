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
        Schema::table('quote_items', function (Blueprint $table) {
            // Joinery is quoted per m²: unit_price is derived from height × width
            // × the rate the line points at, and shutters are priced separately.
            $table->decimal('height', 8, 3)->nullable()->after('description');
            $table->decimal('width', 8, 3)->nullable()->after('height');
            $table->string('rate_label')->nullable()->after('unit');
            $table->string('shutter_rate_label')->nullable()->after('rate_label');
            $table->decimal('shutter_price', 12, 2)->default(0)->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            $table->dropColumn([
                'height',
                'width',
                'rate_label',
                'shutter_rate_label',
                'shutter_price',
            ]);
        });
    }
};

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
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('client_address')->nullable()->after('city');
            $table->date('devis_date')->nullable()->after('valid_until');
            $table->json('rates')->nullable()->after('devis_date');
            $table->text('product_notes')->nullable()->after('rates');
            $table->boolean('show_tax')->default(false)->after('tax_rate');
        });

        // Devis drafted at the counter have no email and no free-text project
        // description — both were only ever required by the public request form.
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'client_address',
                'devis_date',
                'rates',
                'product_notes',
                'show_tax',
            ]);
        });
    }
};

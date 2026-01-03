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
            $table->string('quote_number')->nullable()->unique()->after('id');
            $table->date('valid_until')->nullable()->after('admin_notes');
            $table->decimal('subtotal', 12, 2)->nullable()->after('valid_until');
            $table->decimal('tax_rate', 5, 2)->default(19)->after('subtotal');
            $table->decimal('tax_amount', 12, 2)->nullable()->after('tax_rate');
            $table->decimal('discount', 12, 2)->default(0)->after('tax_amount');
            $table->decimal('total', 12, 2)->nullable()->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'quote_number',
                'valid_until',
                'subtotal',
                'tax_rate',
                'tax_amount',
                'discount',
                'total',
            ]);
        });
    }
};

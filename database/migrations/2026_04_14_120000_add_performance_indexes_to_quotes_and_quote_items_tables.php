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
            $table->index('status');
            $table->index('project_type');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->index(['quote_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            $table->dropIndex('quote_items_quote_id_order_index');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropIndex('quotes_status_created_at_index');
            $table->dropIndex('quotes_created_at_index');
            $table->dropIndex('quotes_project_type_index');
            $table->dropIndex('quotes_status_index');
        });
    }
};

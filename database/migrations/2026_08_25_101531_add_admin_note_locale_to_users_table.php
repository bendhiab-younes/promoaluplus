<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Which language each admin reads the panel's help notes in. Per user
     * rather than per site: the workshop has French and Arabic readers sharing
     * the same panel, and the choice has to survive a logout.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('admin_note_locale', 2)->default('fr');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin_note_locale');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `svg_icon` was created as `string()` — VARCHAR(255) — with the comment "Full SVG
 * markup for complex icons", which no real SVG fits in. The shortest default in
 * `Service::DEFAULT_SVG_ICON_BY_SLUG` is 480 characters.
 *
 * SQLite does not enforce VARCHAR length, so this was invisible in development and
 * in the test suite. MySQL 8 runs `STRICT_TRANS_TABLES` by default and raises
 * `SQLSTATE[22001]: String data, right truncated` instead — so the first `db:seed`
 * of a production deploy would abort. Found on the server, during the real thing.
 *
 * Deliberately timestamped **before** `..._103910_backfill_default_service_svg_icons`:
 * that migration writes a 480-character default into every blank row, so on any
 * database that already has service rows it would hit the 255 limit and abort before
 * a later-dated widening could rescue it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('svg_icon')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('svg_icon')->nullable()->change();
        });
    }
};

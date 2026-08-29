<?php

use App\Models\Service;
use Illuminate\Database\Migrations\Migration;

/**
 * `ServiceSeeder` now fills `svg_icon` from `Service::DEFAULT_SVG_ICON_BY_SLUG`,
 * but only for rows it is creating — and on an existing install every service row
 * already exists, so the seeder would never reach them. This carries the defaults
 * across to sites that were installed before the constant existed.
 *
 * The blank-only guard is the point: a service whose SVG an admin has already set
 * is left exactly as they left it.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (Service::DEFAULT_SVG_ICON_BY_SLUG as $slug => $svg) {
            Service::query()
                ->where('slug', $slug)
                ->where(fn ($query) => $query->whereNull('svg_icon')->orWhere('svg_icon', ''))
                ->update(['svg_icon' => $svg]);
        }
    }

    public function down(): void
    {
        foreach (Service::DEFAULT_SVG_ICON_BY_SLUG as $slug => $svg) {
            Service::query()
                ->where('slug', $slug)
                ->where('svg_icon', $svg)
                ->update(['svg_icon' => null]);
        }
    }
};

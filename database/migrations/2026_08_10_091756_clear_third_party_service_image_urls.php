<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The "mosquito_nets" service pointed at a product photo hosted on an
     * Italian window retailer's catalogue. It arrived via the content import
     * (content_docs/json/services.json -> images[]) and was carried into
     * image_url by the add_image_url_to_content_tables migration.
     *
     * Clearing it here removes it from every environment rather than just the
     * local database. The seeder never writes image_url and buildGallery()
     * discards external URLs, so it will not come back on a reseed. The card
     * falls back to its gradient-and-icon state until the client uploads a
     * photo of their own work through the admin panel.
     */
    private const BLOCKED_IMAGE_HOSTS = [
        'windowo.com',
    ];

    public function up(): void
    {
        foreach (self::BLOCKED_IMAGE_HOSTS as $host) {
            DB::table('services')
                ->where('image_url', 'like', '%'.$host.'%')
                ->update(['image_url' => null]);
        }
    }

    /**
     * Intentionally irreversible: restoring a third-party image is not a
     * desirable rollback, and the original value is recoverable from the
     * content import if it is ever genuinely needed.
     */
    public function down(): void
    {
        // No-op.
    }
};

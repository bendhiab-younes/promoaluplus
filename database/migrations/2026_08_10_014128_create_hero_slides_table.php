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
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('image_url')->nullable();
            $table->json('alt_text')->nullable();
            $table->json('badge')->nullable();
            $table->string('badge_icon')->default('star');
            $table->json('title');
            $table->json('highlight')->nullable();
            $table->json('description')->nullable();
            $table->string('cta_type')->default('contact');
            $table->string('cta_url')->nullable();
            $table->json('cta_label')->nullable();
            $table->boolean('show_whatsapp')->default(false);
            $table->string('accent_color')->default('orange');
            $table->string('image_fit')->default('cover');
            $table->unsignedSmallInteger('image_zoom')->default(100);
            $table->unsignedTinyInteger('focal_x')->default(50);
            $table->unsignedTinyInteger('focal_y')->default(50);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};

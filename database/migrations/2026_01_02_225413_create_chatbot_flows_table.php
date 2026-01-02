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
        Schema::create('chatbot_flows', function (Blueprint $table) {
            $table->id();
            $table->string('trigger')->index(); // 'welcome', 'services', 'quote', keyword, etc.
            $table->string('trigger_type')->default('button'); // 'button', 'keyword', 'fallback'
            $table->json('keywords')->nullable(); // Keywords that trigger this flow
            $table->json('message'); // {fr: '', en: '', ar: ''}
            $table->json('quick_replies')->nullable(); // [{text: {fr, en, ar}, action: 'flow:services'}]
            $table->string('action')->nullable(); // 'url', 'whatsapp', 'contact', null
            $table->string('action_value')->nullable(); // URL or phone number
            $table->foreignId('parent_id')->nullable()->constrained('chatbot_flows')->nullOnDelete();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_flows');
    }
};

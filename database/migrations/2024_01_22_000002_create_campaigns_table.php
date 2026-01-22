<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('platform', ['TikTok', 'Facebook', 'Instagram', 'Google', 'Snapchat', 'X', 'YouTube', 'LinkedIn', 'Other']);
            $table->enum('ad_type', ['Video', 'Image', 'Carousel', 'Search', 'Story', 'Other']);
            $table->enum('source', ['Form', 'WhatsApp', 'Phone Call', 'Website', 'DM', 'Other']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};

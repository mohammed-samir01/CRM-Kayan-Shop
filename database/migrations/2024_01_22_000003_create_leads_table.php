<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_code')->nullable()->unique();
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('customer_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['New', 'Contacted', 'Interested', 'Confirmed', 'Shipped', 'Cancelled'])->default('New');
            $table->decimal('expected_value', 12, 2)->default(0);
            $table->date('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('phone');
            $table->index('status');
            $table->index('follow_up_date');
            $table->index('lead_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

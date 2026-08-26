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
        Schema::create('telegram_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('telegram_user_id')->unique();
            $table->bigInteger('telegram_chat_id')->unique();
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('language_code', 10)->nullable();
            $table->boolean('is_bot')->default(false);
            $table->unsignedInteger('default_branch_id')->nullable();
            $table->string('default_room_number', 50)->nullable();
            $table->boolean('is_profile_complete')->default(false);
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('contact_consent')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('last_interaction_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_profiles');
    }
};

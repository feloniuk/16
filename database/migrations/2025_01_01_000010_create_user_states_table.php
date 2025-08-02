<?php
// database/migrations/2025_01_01_000010_create_user_states_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_states', function (Blueprint $table) {
            $table->bigInteger('telegram_id')->primary();
            $table->string('current_state', 100)->nullable();
            $table->json('temp_data')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('current_state');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_states');
    }
};

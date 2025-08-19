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
        Schema::create('store_action_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('store_id')->index('store_id');
            $table->integer('user_id')->index('user_id');
            $table->string('action_type', 100);
            $table->string('target_table', 100)->nullable();
            $table->integer('target_id')->nullable();
            $table->text('detail')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_action_logs');
    }
};

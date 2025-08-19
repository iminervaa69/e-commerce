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
        Schema::create('stores', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name');
            $table->string('slug')->nullable()->index('idx_stores_slug');
            $table->string('email');
            $table->string('address', 500);
            $table->string('phone', 50);
            $table->text('description')->nullable();
            $table->string('status', 10);
            $table->integer('day_of_week')->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();

            $table->unique(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};

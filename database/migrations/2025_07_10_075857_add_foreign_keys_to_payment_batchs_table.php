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
        Schema::table('payment_batchs', function (Blueprint $table) {
            $table->foreign(['user_id'], 'payment_batchs_ibfk_1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_batchs', function (Blueprint $table) {
            $table->dropForeign('payment_batchs_ibfk_1');
        });
    }
};

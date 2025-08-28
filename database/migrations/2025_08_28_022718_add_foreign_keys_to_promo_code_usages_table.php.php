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
        Schema::table('promo_code_usages', function (Blueprint $table) {
            $table->foreign(['user_id'], 'promo_code_usages_ibfk_1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['promo_codes_id'], 'promo_code_usages_ibfk_2')->references(['id'])->on('promo_codes')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['transactions_id'], 'promo_code_usages_ibfk_3')->references(['id'])->on('transactions')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promo_code_usages', function (Blueprint $table) {
            $table->dropForeign('promo_code_usages_ibfk_1');
            $table->dropForeign('promo_code_usages_ibfk_2');
            $table->dropForeign('promo_code_usages_ibfk_3');
        });
    }
};

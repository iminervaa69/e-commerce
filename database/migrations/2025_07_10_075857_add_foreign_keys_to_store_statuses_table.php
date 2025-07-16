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
        Schema::table('store_statuses', function (Blueprint $table) {
            $table->foreign(['store_id'], 'store_statuses_ibfk_1')->references(['id'])->on('stores')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_statuses', function (Blueprint $table) {
            $table->dropForeign('store_statuses_ibfk_1');
        });
    }
};

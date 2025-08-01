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
        Schema::table('store_user_roles', function (Blueprint $table) {
            $table->foreign(['store_id'], 'store_user_roles_ibfk_1')->references(['id'])->on('stores')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['user_id'], 'store_user_roles_ibfk_2')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_user_roles', function (Blueprint $table) {
            $table->dropForeign('store_user_roles_ibfk_1');
            $table->dropForeign('store_user_roles_ibfk_2');
        });
    }
};

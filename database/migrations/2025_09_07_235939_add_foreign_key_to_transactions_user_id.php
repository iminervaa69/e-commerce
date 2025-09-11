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
        // First, fix the data type incompatibility
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->change();
        });

        // Then add the foreign key constraint
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign(['user_id'], 'transactions_ibfk_3')->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_ibfk_3');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->change();
        });
    }
};

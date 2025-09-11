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
        Schema::table('transactions', function (Blueprint $table) {
            // Add foreign key constraints following your naming pattern
            $table->foreign(['address_id'], 'transactions_ibfk_4')->references(['id'])->on('addresses')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['billing_information_id'], 'transactions_ibfk_5')->references(['id'])->on('billing_information')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_ibfk_4');
            $table->dropForeign('transactions_ibfk_5');
        });
    }
};

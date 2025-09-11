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
            // Add foreign key columns for address and billing information
            $table->integer('address_id')->nullable()->after('user_id');
            $table->integer('billing_information_id')->nullable()->after('address_id');

            // Add indexes for better performance
            $table->index('address_id', 'transactions_address_id_index');
            $table->index('billing_information_id', 'transactions_billing_information_id_index');
            $table->index(['user_id', 'status'], 'transactions_user_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('transactions_address_id_index');
            $table->dropIndex('transactions_billing_information_id_index');
            $table->dropIndex('transactions_user_status_index');

            // Drop columns
            $table->dropColumn(['address_id', 'billing_information_id']);
        });
    }
};

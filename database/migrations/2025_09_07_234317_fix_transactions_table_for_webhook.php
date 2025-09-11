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
            // Add user_id column matching users.id type (int)
            $table->integer('user_id')->nullable()->after('id');

            // Add missing columns for webhook functionality
            $table->decimal('total_amount', 15, 2)->nullable()->after('amount');
            $table->string('xendit_payment_id')->nullable()->after('xendit_id');
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->timestamp('failed_at')->nullable()->after('paid_at');
            $table->text('failure_reason')->nullable()->after('failed_at');
            $table->decimal('paid_amount', 15, 2)->nullable()->after('failure_reason');

            // Add indexes
            $table->index('xendit_payment_id', 'transactions_xendit_payment_id_index');
            $table->index('user_id', 'transactions_user_id_index');
        });

        // Add foreign key constraint in separate schema call
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
            // Drop foreign key first
            $table->dropForeign('transactions_ibfk_3');

            // Drop indexes
            $table->dropIndex('transactions_xendit_payment_id_index');
            $table->dropIndex('transactions_user_id_index');

            // Drop columns
            $table->dropColumn([
                'user_id',
                'total_amount',
                'xendit_payment_id',
                'paid_at',
                'failed_at',
                'failure_reason',
                'paid_amount'
            ]);
        });
    }
};

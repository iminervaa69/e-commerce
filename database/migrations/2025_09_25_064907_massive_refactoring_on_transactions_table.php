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
            // Core Payments API fields
            $table->string('payment_request_id')->nullable()->after('xendit_id');
            $table->string('payment_method_id')->nullable()->after('payment_request_id');
            $table->string('payment_method_type', 50)->nullable()->after('payment_method_id');

            // Enhanced status tracking
            $table->string('payment_status', 50)->nullable()->after('status');
            $table->json('payment_actions')->nullable()->after('payment_status');

            // Additional payment details
            $table->decimal('fees', 15, 2)->default(0.00)->after('paid_amount');
            $table->decimal('net_amount', 15, 2)->nullable()->after('fees');

            // Enhanced response tracking
            $table->json('payment_method_response')->nullable()->after('xendit_response');
            $table->json('payment_request_response')->nullable()->after('payment_method_response');

            // Timestamps for better tracking
            $table->timestamp('expires_at')->nullable()->after('failed_at');
            $table->timestamp('processed_at')->nullable()->after('expires_at');

            // API version tracking for migration
            $table->string('api_version', 20)->default('legacy')->after('processed_at');

            // Add indexes for performance
            $table->index('payment_request_id');
            $table->index('payment_method_id');
            $table->index('payment_method_type');
            $table->index('payment_status');
            $table->index('api_version');
            $table->index('expires_at');
        });

        // Update existing records to mark them as legacy
        DB::table('transactions')->whereNull('api_version')->update(['api_version' => 'legacy']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Remove indexes first
            $table->dropIndex(['payment_request_id']);
            $table->dropIndex(['payment_method_id']);
            $table->dropIndex(['payment_method_type']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['api_version']);
            $table->dropIndex(['expires_at']);

            // Remove columns
            $table->dropColumn([
                'payment_request_id',
                'payment_method_id',
                'payment_method_type',
                'payment_status',
                'payment_actions',
                'fees',
                'net_amount',
                'payment_method_response',
                'payment_request_response',
                'expires_at',
                'processed_at',
                'api_version',
            ]);
        });
    }
};

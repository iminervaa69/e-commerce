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
        Schema::create('shipments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('order_id')->index('order_id');
            $table->string('shipment_number', 100)->unique('shipment_number');
            $table->enum('status', ['pending', 'processing', 'shipped', 'in_transit', 'delivered', 'cancelled'])->default('pending');
            $table->string('tracking_number', 100)->nullable();
            $table->string('carrier', 50)->nullable();
            $table->string('shipping_method', 50)->nullable();
            $table->timestamp('shipped_date')->nullable();
            $table->timestamp('estimated_delivery_date')->nullable();
            $table->timestamp('actual_delivery_date')->nullable();
            $table->json('shipping_address')->nullable();
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->index(['status', 'created_at'], 'status_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
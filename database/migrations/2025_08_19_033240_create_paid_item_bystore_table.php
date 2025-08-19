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
        Schema::create('paid_item_bystore', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id')->index('product_id');
            $table->integer('paid_item_bystore_id')->index('paid_item_bystore_id');
            $table->integer('quantity');
            $table->integer('total_paid');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paid_item_bystore');
    }
};

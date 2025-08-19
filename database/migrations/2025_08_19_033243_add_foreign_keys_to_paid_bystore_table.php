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
        Schema::table('paid_bystore', function (Blueprint $table) {
            $table->foreign(['store_id'], 'paid_bystore_ibfk_1')->references(['id'])->on('stores')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['payment_batch_id'], 'paid_bystore_ibfk_2')->references(['id'])->on('payment_batchs')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paid_bystore', function (Blueprint $table) {
            $table->dropForeign('paid_bystore_ibfk_1');
            $table->dropForeign('paid_bystore_ibfk_2');
        });
    }
};

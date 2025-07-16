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
        Schema::table('paid_item_bystore', function (Blueprint $table) {
            $table->foreign(['product_id'], 'paid_item_bystore_ibfk_1')->references(['id'])->on('product_variants')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['paid_item_bystore_id'], 'paid_item_bystore_ibfk_2')->references(['id'])->on('paid_bystore')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paid_item_bystore', function (Blueprint $table) {
            $table->dropForeign('paid_item_bystore_ibfk_1');
            $table->dropForeign('paid_item_bystore_ibfk_2');
        });
    }
};

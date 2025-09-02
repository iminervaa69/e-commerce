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
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->foreign(['shipment_id'], 'shipment_items_ibfk_1')->references(['id'])->on('shipments')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['order_item_id'], 'shipment_items_ibfk_2')->references(['id'])->on('order_items')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->dropForeign('shipment_items_ibfk_1');
            $table->dropForeign('shipment_items_ibfk_2');
        });
    }
};
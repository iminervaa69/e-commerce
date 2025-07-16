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
        Schema::table('item_shipment_status', function (Blueprint $table) {
            $table->foreign(['product_variant_id'], 'item_shipment_status_ibfk_1')->references(['id'])->on('product_variants')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_shipment_status', function (Blueprint $table) {
            $table->dropForeign('item_shipment_status_ibfk_1');
        });
    }
};

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
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->foreign(['product_id'], 'product_reviews_ibfk_1')->references(['id'])->on('products')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_variants_id'], 'product_reviews_ibfk_2')->references(['id'])->on('product_variants')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['user_id'], 'product_reviews_ibfk_3')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['paid_item_bystore_id'], 'product_reviews_ibfk_4')->references(['id'])->on('paid_item_bystore')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropForeign('product_reviews_ibfk_1');
            $table->dropForeign('product_reviews_ibfk_2');
            $table->dropForeign('product_reviews_ibfk_3');
            $table->dropForeign('product_reviews_ibfk_4');
        });
    }
};

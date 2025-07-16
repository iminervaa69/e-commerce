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
        Schema::table('product_categories', function (Blueprint $table) {
            $table->foreign(['product_id'], 'product_categories_ibfk_1')->references(['id'])->on('products')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['category_id'], 'product_categories_ibfk_2')->references(['id'])->on('categories')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign('product_categories_ibfk_1');
            $table->dropForeign('product_categories_ibfk_2');
        });
    }
};

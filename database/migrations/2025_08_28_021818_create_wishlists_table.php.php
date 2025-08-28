<?php

// Migration for wishlists table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->index('user_id');
            $table->integer('product_variant_id')->index('product_variant_id');
            $table->timestamps();
            
            $table->unique(['user_id', 'product_variant_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wishlists');
    }
};
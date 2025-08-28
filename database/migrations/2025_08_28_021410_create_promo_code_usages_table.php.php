<?php

// Migration for promo_code_usages table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promo_code_usages', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->index('user_id');
            $table->integer('promo_codes_id')->index('promo_codes_id');
            $table->integer('transactions_id')->index('transactions_id');
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();
            
            $table->unique(['user_id', 'promo_codes_id', 'transactions_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('promo_code_usages');
    }
};

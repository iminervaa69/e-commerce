<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('cart_items', 'session_id')) {
                $table->dropColumn('session_id');
            }
            if (Schema::hasColumn('cart_items', 'price_when_added')) {
                $table->dropColumn('price_when_added');
            }
            if (Schema::hasColumn('cart_items', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }

    public function down()
    {
        // Nothing to do
    }
};
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('session_id')->nullable()->after('user_id');
            $table->decimal('price_when_added', 10, 2)->nullable()->after('quantity');
            $table->timestamp('expires_at')->nullable()->after('updated_at');
        });
    }

    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['session_id', 'price_when_added', 'expires_at']);
        });
    }
};
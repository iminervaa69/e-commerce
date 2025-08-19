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
        Schema::table('messages', function (Blueprint $table) {
            $table->foreign(['chat_id'], 'messages_ibfk_1')->references(['id'])->on('chat')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['sender_id'], 'messages_ibfk_2')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['recipient_id'], 'messages_ibfk_3')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign('messages_ibfk_1');
            $table->dropForeign('messages_ibfk_2');
            $table->dropForeign('messages_ibfk_3');
        });
    }
};

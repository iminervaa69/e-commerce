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
        Schema::create('messages', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('chat_id')->nullable()->index('chat_id');
            $table->integer('sender_id')->nullable()->index('sender_id');
            $table->integer('recipient_id')->nullable()->index('recipient_id');
            $table->binary('encrypted_message');
            $table->binary('encrypted_symmetric_key');
            $table->timestamp('sent_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

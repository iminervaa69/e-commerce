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
        Schema::table('attachments', function (Blueprint $table) {
            $table->foreign(['message_id'], 'attachments_ibfk_1')->references(['id'])->on('messages')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_variant_reference'], 'attachments_ibfk_2')->references(['id'])->on('product_variants')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropForeign('attachments_ibfk_1');
            $table->dropForeign('attachments_ibfk_2');
        });
    }
};

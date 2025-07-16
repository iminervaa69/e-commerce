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
        Schema::table('product_review_attachments', function (Blueprint $table) {
            $table->foreign(['review_id'], 'product_review_attachments_ibfk_1')->references(['id'])->on('product_reviews')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_review_attachments', function (Blueprint $table) {
            $table->dropForeign('product_review_attachments_ibfk_1');
        });
    }
};

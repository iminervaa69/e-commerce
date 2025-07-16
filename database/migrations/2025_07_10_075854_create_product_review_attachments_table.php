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
        Schema::create('product_review_attachments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('review_id')->index('review_id');
            $table->string('attachment_type', 50);
            $table->text('file_url');
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_review_attachments');
    }
};

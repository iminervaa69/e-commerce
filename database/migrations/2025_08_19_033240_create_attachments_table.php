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
        Schema::create('attachments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('message_id')->index('message_id');
            $table->string('attachment_type', 50);
            $table->text('file_url');
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->integer('product_variant_reference')->nullable()->index('product_variant_reference');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};

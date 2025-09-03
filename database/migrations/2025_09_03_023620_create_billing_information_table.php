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
        Schema::create('billing_information', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->index('user_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255)->index('idx_billing_email');
            $table->string('phone', 20);
            $table->tinyInteger('is_default')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
            
            // Additional indexes for better performance
            $table->index(['user_id', 'is_default'], 'idx_billing_user_default');
            $table->index(['user_id', 'email'], 'idx_billing_user_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_information');
    }
};
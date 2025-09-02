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
        Schema::create('addresses', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->index('user_id');
            $table->string('label', 10)->default('Home');
            $table->string('recipient_name', 100);
            $table->string('phone', 20);
            $table->string('province', 100);
            $table->string('city', 100);
            $table->string('district', 100);
            $table->string('postal_code', 5)->index('idx_addresses_postal_code');
            $table->text('street_address');
            $table->text('address_notes')->nullable();
            $table->tinyInteger('is_default')->default(0);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
            
            // Additional indexes for better performance
            $table->index(['user_id', 'is_default'], 'idx_addresses_user_default');
            $table->index(['user_id', 'label'], 'idx_addresses_user_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
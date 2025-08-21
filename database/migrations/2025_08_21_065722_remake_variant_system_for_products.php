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
        // Create variant templates table
        Schema::create('variant_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->json('template_json');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index('store_id');
        });
        
        // Add columns to existing products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('variant_template_id')
                  ->nullable()
                  ->after('store_id')
                  ->constrained('variant_templates')
                  ->onDelete('set null');
            $table->json('variant_attributes')->nullable()->after('important_info');
        });
        
        // Add columns to existing product_variants table
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('sku', 100)->unique()->nullable()->after('slug');
            $table->json('variant_combination')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove columns from product_variants table
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['sku', 'variant_combination']);
        });
        
        // Remove columns from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['variant_template_id']);
            $table->dropColumn(['variant_template_id', 'variant_attributes']);
        });
        
        // Drop variant templates table
        Schema::dropIfExists('variant_templates');
    }
};
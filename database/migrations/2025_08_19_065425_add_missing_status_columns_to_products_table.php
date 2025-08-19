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
        // Add status to products table if it doesn't exist
        if (!Schema::hasColumn('products', 'status')) {
            Schema::table('products', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            });
        }
        
        // Add status to product_reviews table if it doesn't exist
        if (Schema::hasTable('product_reviews') && !Schema::hasColumn('product_reviews', 'status')) {
            Schema::table('product_reviews', function (Blueprint $table) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            });
        }
        
        // Add status to stores table if it doesn't exist
        if (Schema::hasTable('stores') && !Schema::hasColumn('stores', 'status')) {
            Schema::table('stores', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            });
        }
        
        // product_variants already has status column, so skip it
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'status')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
        
        if (Schema::hasTable('product_reviews') && Schema::hasColumn('product_reviews', 'status')) {
            Schema::table('product_reviews', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
        
        if (Schema::hasTable('stores') && Schema::hasColumn('stores', 'status')) {
            Schema::table('stores', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
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
        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('reserved_stock')->default(0)->after('stock');
            $table->integer('sold_count')->default(0)->after('reserved_stock');

            // Add indexes for performance
            $table->index('reserved_stock');
            $table->index('sold_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['reserved_stock']);
            $table->dropIndex(['sold_count']);
            $table->dropColumn([
                'reserved_stock',
                'sold_count'
            ]);
        });
    }
};

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
        Schema::table('sales', function (Blueprint $table) {
            $table->index('branch_id');
            $table->index('customer_id');
            $table->index('created_at');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->index('branch_id');
            $table->index('supplier_id');
            $table->index('created_at');
        });

        Schema::table('inventory_adjustments', function (Blueprint $table) {
            $table->index('branch_id');
            $table->index('created_at');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('phone');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('inventory_adjustments', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['phone']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
        });
    }
};

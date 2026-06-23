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
        Schema::table('products', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('products', 'purchase_price')) {
                $columnsToDrop[] = 'purchase_price';
            }
            if (Schema::hasColumn('products', 'initial_stock')) {
                $columnsToDrop[] = 'initial_stock';
            }
            if (Schema::hasColumn('products', 'expiry_date')) {
                $columnsToDrop[] = 'expiry_date';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'purchase_price')) {
                $table->decimal('purchase_price', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'initial_stock')) {
                $table->decimal('initial_stock', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'expiry_date')) {
                $table->date('expiry_date')->nullable();
            }
        });
    }
};

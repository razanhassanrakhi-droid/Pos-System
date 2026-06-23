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
            $table->foreignId('branch_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // Migrate existing products to their respective branches
        $products = \DB::table('products')->get();
        foreach ($products as $product) {
            // Find the branch from batches or movements
            $branchId = \DB::table('batches')->where('product_id', $product->id)->value('branch_id');
            if (!$branchId) {
                $branchId = \DB::table('stock_movements')->where('product_id', $product->id)->value('branch_id');
            }
            if (!$branchId) {
                $branchId = \DB::table('branches')->value('id');
            }

            if ($branchId) {
                \DB::table('products')->where('id', $product->id)->update(['branch_id' => $branchId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};

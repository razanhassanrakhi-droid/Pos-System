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
        // Sync all batch sale prices with their product's current sale price
        DB::statement("UPDATE batches b JOIN products p ON b.product_id = p.id SET b.sale_price = p.sale_price");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to reverse this as we don't know the previous batch-specific prices
    }
};

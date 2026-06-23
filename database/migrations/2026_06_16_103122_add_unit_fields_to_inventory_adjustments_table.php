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
        Schema::table('inventory_adjustments', function (Blueprint $table) {
            $table->foreignId('product_unit_id')->nullable()->after('batch_id')->constrained('product_units')->onDelete('set null');
            $table->decimal('entered_quantity', 15, 2)->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_adjustments', function (Blueprint $table) {
            $table->dropForeign(['product_unit_id']);
            $table->dropColumn(['product_unit_id', 'entered_quantity']);
        });
    }
};

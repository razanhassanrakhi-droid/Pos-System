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
        Schema::table('purchase_items', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['batch_id']);
            
            // Re-add with correct reference
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['batch_id']);
            
            // Re-add with correct reference
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->foreign('batch_id')->references('id')->on('product_batches')->onDelete('set null');
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->foreign('batch_id')->references('id')->on('product_batches')->onDelete('set null');
        });
    }
};

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
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('sale_item_id')->constrained('sale_items')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('serial_number')->nullable();
            $table->date('warranty_start_date');
            $table->date('warranty_end_date');
            $table->integer('warranty_period_months');
            $table->enum('status', ['ACTIVE', 'EXPIRED'])->default('ACTIVE');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('serial_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};

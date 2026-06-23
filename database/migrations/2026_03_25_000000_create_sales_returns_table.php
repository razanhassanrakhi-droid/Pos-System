<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->decimal('quantity', 15, 2);
            $table->string('reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['sale_id', 'product_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};

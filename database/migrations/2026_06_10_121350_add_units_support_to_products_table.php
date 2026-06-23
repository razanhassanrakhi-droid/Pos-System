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
            $table->string('base_unit_name')->nullable()->after('barcode');
        });

        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('unit_name');
            $table->decimal('conversion_factor', 12, 4);
            $table->decimal('sale_price', 12, 2);
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();

            $table->unique(['branch_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
        Schema::dropIfExists('product_units');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('base_unit_name');
        });
    }
};

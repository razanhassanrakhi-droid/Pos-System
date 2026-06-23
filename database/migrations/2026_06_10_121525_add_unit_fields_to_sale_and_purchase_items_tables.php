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
        Schema::table('sale_items', function (Blueprint $table) {
            $table->string('unit_name')->nullable()->after('quantity');
            $table->decimal('conversion_factor', 12, 4)->nullable()->default(1.0000)->after('unit_name');
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->string('unit_name')->nullable()->after('quantity');
            $table->decimal('conversion_factor', 12, 4)->nullable()->default(1.0000)->after('unit_name');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['unit_name', 'conversion_factor']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['unit_name', 'conversion_factor']);
        });
    }
};

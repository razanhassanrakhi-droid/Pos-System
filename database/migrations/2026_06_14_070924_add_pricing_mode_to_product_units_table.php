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
        Schema::table('product_units', function (Blueprint $table) {
            if (!Schema::hasColumn('product_units', 'pricing_mode')) {
                $table->string('pricing_mode')->default('custom')->after('barcode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            if (Schema::hasColumn('product_units', 'pricing_mode')) {
                $table->dropColumn('pricing_mode');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->string('unit_name_ar')->nullable()->after('product_id');
            $table->string('unit_name_en')->nullable()->after('unit_name_ar');
        });

        // Copy existing data
        DB::statement('UPDATE product_units SET unit_name_en = unit_name');

        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn('unit_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->string('unit_name')->nullable()->after('product_id');
        });

        // Copy back data
        DB::statement('UPDATE product_units SET unit_name = COALESCE(unit_name_ar, unit_name_en)');

        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn(['unit_name_ar', 'unit_name_en']);
        });
    }
};

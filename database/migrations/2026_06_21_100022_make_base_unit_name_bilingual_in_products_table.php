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
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('base_unit_name_ar')->nullable()->after('sku');
            $table->string('base_unit_name_en')->nullable()->after('base_unit_name_ar');
        });

        // Copy existing data
        DB::statement('UPDATE products SET base_unit_name_en = base_unit_name');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('base_unit_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('base_unit_name')->nullable()->after('sku');
        });

        // Copy back data
        DB::statement('UPDATE products SET base_unit_name = COALESCE(base_unit_name_ar, base_unit_name_en)');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['base_unit_name_ar', 'base_unit_name_en']);
        });
    }
};

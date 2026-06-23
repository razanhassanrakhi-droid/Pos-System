<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $columns = ['name_ar', 'name_en', 'address_ar', 'address_en', 'city_ar', 'city_en'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('branches', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
            if (!Schema::hasColumn('branches', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('branches', 'address_ar')) {
                $table->string('address_ar')->nullable()->after('address');
            }
            if (!Schema::hasColumn('branches', 'address_en')) {
                $table->string('address_en')->nullable()->after('address_ar');
            }
            if (!Schema::hasColumn('branches', 'city_ar')) {
                $table->string('city_ar')->nullable()->after('city');
            }
            if (!Schema::hasColumn('branches', 'city_en')) {
                $table->string('city_en')->nullable()->after('city_ar');
            }
        });
    }
};


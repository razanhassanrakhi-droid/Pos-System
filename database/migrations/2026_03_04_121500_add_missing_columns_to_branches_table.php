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
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('branches', 'code')) {
                $table->string('code')->unique()->nullable()->after('name');
            }
            if (!Schema::hasColumn('branches', 'phone')) {
                $table->string('phone')->nullable()->after('code');
            }
            if (!Schema::hasColumn('branches', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('branches', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('branches', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('branches', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('branches', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('branches', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('branches', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
            if (Schema::hasColumn('branches', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};


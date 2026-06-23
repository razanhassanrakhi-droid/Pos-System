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
        Schema::table('expenses', function (Blueprint $table) {
            $table->text('description_ar')->nullable()->after('expense_date');
            $table->text('description_en')->nullable()->after('description_ar');
        });

        // Migrate existing description to description_ar
        \DB::statement('UPDATE expenses SET description_ar = description');

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->text('description')->nullable()->after('expense_date');
        });

        \DB::statement('UPDATE expenses SET description = description_ar');

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['description_ar', 'description_en']);
        });
    }
};

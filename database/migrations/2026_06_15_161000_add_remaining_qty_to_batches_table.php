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
        Schema::table('batches', function (Blueprint $table) {
            if (!Schema::hasColumn('batches', 'conversion_factor')) {
                $table->decimal('conversion_factor', 15, 4)->default(1);
            }
            if (!Schema::hasColumn('batches', 'remaining_quantity')) {
                $table->decimal('remaining_quantity', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('batches', 'status')) {
                $table->string('status')->default('Active');
            }
        });

        // Initialize remaining_quantity from quantity (which currently tracks remaining stock)
        DB::statement('UPDATE batches SET remaining_quantity = quantity');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['conversion_factor', 'remaining_quantity', 'status']);
        });
    }
};

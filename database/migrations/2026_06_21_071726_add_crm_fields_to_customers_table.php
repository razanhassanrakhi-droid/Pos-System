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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_type')->default('Regular');
            $table->string('status')->default('Active');
            $table->date('dob')->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'customer_type',
                'status',
                'dob',
                'loyalty_points',
                'credit_limit',
                'balance'
            ]);
        });
    }
};

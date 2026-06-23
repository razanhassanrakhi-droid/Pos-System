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
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->after('type');
            $table->unsignedBigInteger('batch_id')->nullable()->after('product_id');
            $table->timestamp('resolved_at')->nullable()->after('read_date');
            $table->boolean('created_by_system')->default(true)->after('resolved_at');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');
        });

        // Clean up existing notifications to start fresh
        \Illuminate\Support\Facades\DB::table('notifications')->truncate();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['batch_id']);
            $table->dropColumn(['product_id', 'batch_id', 'resolved_at', 'created_by_system']);
        });
    }
};

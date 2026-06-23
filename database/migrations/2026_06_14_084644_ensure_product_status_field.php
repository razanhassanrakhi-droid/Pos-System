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
        if (!Schema::hasColumn('products', 'status')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('status')->default('Active')->after('is_active');
            });
        }

        // Assign 'Active' to any products that don't have a status, or have invalid status
        \DB::table('products')
            ->whereNotIn('status', ['Active', 'Inactive'])
            ->orWhereNull('status')
            ->update(['status' => 'Active']);
    }

    public function down(): void
    {
        // No-op to preserve backward compatibility
    }
};

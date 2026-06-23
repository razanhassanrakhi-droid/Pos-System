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
        // Convert existing data to JSON format
        $suppliers = DB::table('suppliers')->get();
        foreach ($suppliers as $supplier) {
            $nameJson = json_encode(['ar' => $supplier->name, 'en' => $supplier->name]);
            $addressJson = $supplier->address ? json_encode(['ar' => $supplier->address, 'en' => $supplier->address]) : null;
            
            DB::table('suppliers')->where('id', $supplier->id)->update([
                'name' => $nameJson,
                'address' => $addressJson,
            ]);
        }

        Schema::table('suppliers', function (Blueprint $table) {
            $table->json('name')->change();
            $table->json('address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('name')->change();
            $table->text('address')->nullable()->change();
        });
    }
};

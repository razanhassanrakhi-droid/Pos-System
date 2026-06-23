<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_adjustments', function (Blueprint $row) {
            $row->id();
            $row->foreignId('product_id')->constrained()->onDelete('cascade');
            $row->foreignId('batch_id')->nullable()->constrained()->onDelete('set null');
            $row->foreignId('branch_id')->constrained()->onDelete('cascade');
            $row->foreignId('user_id')->constrained()->onDelete('cascade');
            $row->decimal('quantity', 15, 2);
            $row->string('adjustment_type'); // EXPIRED, DAMAGED, LOST, MANUAL_CORRECTION
            $row->string('reason')->nullable();
            $row->text('notes')->nullable();
            $row->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_adjustments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $col) {
            $col->id();
            $col->string('supplier_number')->unique();
            $col->string('name');
            $col->string('contact_person')->nullable();
            $col->string('email')->nullable();
            $col->string('phone');
            $col->text('address')->nullable();
            $col->foreignId('branch_id')->constrained()->onDelete('cascade');
            $col->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $col->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};

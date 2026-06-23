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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 150);
            $table->string('username', 50)->unique();
            $table->string('email', 150)->unique()->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('role', ['admin', 'employee'])->default('employee');
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();

            // Indexes are automatically created for unique columns, but adding explicit index if needed for non-unique or composite
            // $table->index('email'); // Already unique
            // $table->index('username'); // Already unique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

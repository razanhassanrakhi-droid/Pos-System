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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notification_number')->unique();
            $table->json('title');
            $table->json('message');
            $table->string('type'); // low_stock, out_of_stock, expiring_soon, expired, etc.
            $table->string('category'); // Inventory, Sales, Purchases, Returns, Customers, System, Security, Administration
            $table->string('priority')->default('Important'); // Critical, Important, Activity
            $table->string('reference_type')->nullable(); // Polymorphic type
            $table->unsignedBigInteger('reference_id')->nullable(); // Polymorphic ID
            $table->boolean('read_status')->default(false);
            $table->timestamp('read_date')->nullable();
            
            // Branch and user references
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for speed
            $table->index(['reference_type', 'reference_id']);
            $table->index('read_status');
            $table->index('branch_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

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
        Schema::table('products', function (Blueprint $table) {
            $table->string('warranty_type')->nullable()->after('has_warranty');
        });

        Schema::table('warranties', function (Blueprint $table) {
            $table->string('warranty_number')->nullable()->unique()->after('id');
            $table->string('warranty_type')->nullable()->after('warranty_number');
            $table->unsignedBigInteger('created_by')->nullable()->after('status');

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warranty_id');
            $table->date('claim_date');
            $table->text('issue_description');
            $table->text('technician_notes')->nullable();
            $table->text('resolution')->nullable();
            $table->json('attachments')->nullable();
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected, Completed
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('warranty_id')->references('id')->on('warranties')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');

        Schema::table('warranties', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['warranty_number', 'warranty_type', 'created_by']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('warranty_type');
        });
    }
};

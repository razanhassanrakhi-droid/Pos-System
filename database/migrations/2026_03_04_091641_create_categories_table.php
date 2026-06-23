<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branch_id');
            $table->json('name');
            $table->json('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // روابط الفروع والمستخدمين
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            // كل فرع الاسم فيه يكون فريد
            // Note: Unique JSON columns are not supported in all MySQL versions, 
            // but we'll stick to this for now as it matches the previous state.
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
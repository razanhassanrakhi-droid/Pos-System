<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المصروف
            $table->string('type'); // نوع المصروف
            $table->decimal('amount', 10, 2); // المبلغ
            $table->date('expense_date'); // تاريخ المصروف
            $table->text('description')->nullable(); // الوصف (اختياري)
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // المستخدم الذي سجل المصروف
            $table->foreignId('branch_id')->constrained()->onDelete('cascade'); // الفرع مرتبط بالمستخدم

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new columns
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('expense_number')->nullable()->unique()->after('id');
            $table->string('payment_method')->default('Cash')->after('amount');
            $table->string('attachment')->nullable()->after('description_en');
        });

        // Convert the tinyint status to a string status (Draft, Approved, Cancelled)
        // Since sqlite doesn't support modifying columns directly well sometimes, 
        // and Laravel change() needs doctrine/dbal, we will do a trick: 
        // 1. Rename old column
        // 2. Add new column
        // 3. Migrate data
        // 4. Drop old column
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->renameColumn('status', 'old_status');
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('status')->default('Approved')->after('old_status');
        });

        // Map data: old_status=1 -> Approved, 0 -> Draft
        DB::statement("UPDATE expenses SET status = CASE WHEN old_status = 1 THEN 'Approved' ELSE 'Draft' END");

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('old_status');
        });

        // Generate expense numbers for existing expenses
        $expenses = DB::table('expenses')->orderBy('id', 'asc')->get();
        foreach ($expenses as $index => $expense) {
            $date = date('Ymd', strtotime($expense->created_at ?? now()));
            $number = sprintf("EXP-%s-%05d", $date, $index + 1);
            DB::table('expenses')->where('id', $expense->id)->update(['expense_number' => $number]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['expense_number', 'payment_method', 'attachment']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->renameColumn('status', 'new_status');
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('new_status');
        });

        DB::statement("UPDATE expenses SET status = CASE WHEN new_status = 'Approved' THEN 1 ELSE 0 END");

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('new_status');
        });
    }
};

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
        $map = [
            'Salaries' => 'salaries',
            'Transportation Allowance' => 'transportation_allowance',
            'Electricity' => 'electricity',
            'Freelancer Fees' => 'freelancer_fees',
            'GOV Social Insurance' => 'gov_social_insurance',
            'Employee Bouns' => 'employee_bonus',
            'Maintenance' => 'maintenance',
            'Subscriptions' => 'subscriptions',
            'Administration Fees' => 'administration_fees',
            'Accounting & Audit Fees' => 'accounting_audit_fees',
            'Travel' => 'travel',
            'Cleaning Exp' => 'cleaning_exp',
            'Learning & Training' => 'learning_training',
            'Food & Beverages' => 'food_beverages',
            'Stationary' => 'stationary',
            'Difference of Exchange Rate' => 'difference_exchange_rate',
            'Tax Fees' => 'tax_fees',
            'Other Expenses' => 'other_expenses',
        ];

        foreach ($map as $old => $new) {
            \DB::table('expenses')->where('type', $old)->update(['type' => $new]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to reverse mixed data, but we can lowercase everything back if needed
    }
};

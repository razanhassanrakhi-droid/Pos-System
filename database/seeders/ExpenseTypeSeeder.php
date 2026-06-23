<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseType;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name_ar' => 'رواتب', 'name_en' => 'Salaries'],
            ['name_ar' => 'بدل سكن / نقل', 'name_en' => 'Transportation Allowance'],
            ['name_ar' => 'كهرباء', 'name_en' => 'Electricity'],
            ['name_ar' => 'أتعاب مستقلين', 'name_en' => 'Freelancer Fees'],
            ['name_ar' => 'تأمينات اجتماعية', 'name_en' => 'GOV Social Insurance'],
            ['name_ar' => 'مكافآت موظفين', 'name_en' => 'Employee Bonus'],
            ['name_ar' => 'صيانة', 'name_en' => 'Maintenance'],
            ['name_ar' => 'اشتراكات', 'name_en' => 'Subscriptions'],
            ['name_ar' => 'رسوم إدارية', 'name_en' => 'Administration Fees'],
            ['name_ar' => 'أتعاب محاسبية وتدقيق', 'name_en' => 'Accounting & Audit Fees'],
            ['name_ar' => 'سفر وانتقال', 'name_en' => 'Travel'],
            ['name_ar' => 'مصاريف نظافة', 'name_en' => 'Cleaning Exp'],
            ['name_ar' => 'تعليم وتدريب', 'name_en' => 'Learning & Training'],
            ['name_ar' => 'ضيافة مأكولات ومشروبات', 'name_en' => 'Food & Beverages'],
            ['name_ar' => 'أدوات مكتبية وقرطاسية', 'name_en' => 'Stationary'],
            ['name_ar' => 'فروق أسعار صرف', 'name_en' => 'Difference of Exchange Rate'],
            ['name_ar' => 'رسوم ضريبية', 'name_en' => 'Tax Fees'],
            ['name_ar' => 'مصاريف أخرى', 'name_en' => 'Other Expenses'],
        ];

        foreach ($types as $type) {
            ExpenseType::create($type);
        }
    }
}

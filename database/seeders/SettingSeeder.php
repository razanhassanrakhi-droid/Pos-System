<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Setting::updateOrCreate(['id' => 1], [
            'company_name' => [
                'en' => 'Digital Age',
                'ar' => 'ديجيتال أيج'
            ],
            'company_address' => [
                'en' => 'Main St, City',
                'ar' => 'شارع المطار، الخرطوم'
            ],
            'company_phone' => '+249 123 456 789',
            'company_email' => 'info@digitalage.com',
            'tax_number' => '300012345600003',
            'registration_number' => '123456',
            'footer_text' => [
                'en' => 'Thank you for choosing Digital Age',
                'ar' => 'شكراً لاختياركم ديجيتال أيج'
            ]
        ]);
    }
}

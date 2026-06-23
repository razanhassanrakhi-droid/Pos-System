<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RestorationSeeder extends Seeder
{
    public function run()
    {
        $files = [
            'users' => 'salvaged_users.json',
            'branches' => 'salvaged_branches.json',
            'categories' => 'salvaged_categories.json',
            'products' => 'salvaged_products.json',
            'branch_user' => 'salvaged_branch_user.json',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($files as $table => $file) {
            $path = base_path($file);
            if (!File::exists($path)) continue;

            $data = json_decode(File::get($path), true);
            echo "Restoring table: $table...\n";

            foreach ($data as $item) {
                // Flatten localized fields (arrays to single string)
                foreach ($item as $key => $value) {
                    if (is_array($value)) {
                        // If it's a localized field, prefer 'ar' then 'en'
                        if (isset($value['ar'])) {
                            $item[$key] = $value['ar'];
                        } elseif (isset($value['en'])) {
                            $item[$key] = $value['en'];
                        } else {
                            // Fallback to json string if it's not a standard localization array
                            // or just keep it as is if the schema expects JSON (like categories)
                            if ($table !== 'categories') {
                                $item[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                            }
                        }
                    }
                }

                // Fix date formats for MySQL
                foreach (['created_at', 'updated_at', 'expiry_date'] as $dateField) {
                    if (isset($item[$dateField]) && !empty($item[$dateField])) {
                        try {
                            $item[$dateField] = \Illuminate\Support\Carbon::parse($item[$dateField])->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            unset($item[$dateField]);
                        }
                    }
                }

                // Handle missing password for users
                if ($table === 'users' && (!isset($item['password']) || empty($item['password']))) {
                    $item['password'] = bcrypt('123456');
                }
                
                // Clean up any attributes that might not exist in the new schema
                if ($table === 'products') {
                    unset($item['stock_quantity']);
                }

                if ($table === 'branches') {
                    unset($item['created_by'], $item['updated_by']);
                }

                try {
                    if ($table === 'branch_user') {
                        DB::table($table)->insert($item);
                    } else {
                        DB::table($table)->updateOrInsert(['id' => $item['id']], $item);
                    }
                } catch (\Exception $e) {
                    echo "Error inserting into $table (ID: " . ($item['id'] ?? 'unknown') . "): " . $e->getMessage() . "\n";
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

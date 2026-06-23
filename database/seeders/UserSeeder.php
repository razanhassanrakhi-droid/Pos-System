<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure at least one branch exists for the employee
        $branch = Branch::firstOrCreate(
            ['code' => 'BR-001'],
            [
                'name' => 'Main Branch',
                'phone' => '123456789',
                'address' => 'Main Street',
                'city' => 'Riyadh',
                'is_active' => true,
            ]
        );

        // Create Admin User (root)
        User::updateOrCreate(
            ['username' => 'root'], // Check by username
            [
                'full_name' => 'Root Admin',
                'email' => 'root@pos.com',
                'role' => 'admin',
                'password' => '1234', // Mutator will hash this
                'is_active' => true,
            ]
        );

        // Create Employee User (emp)
        $employee = User::updateOrCreate(
            ['username' => 'emp'], // Check by username
            [
                'full_name' => 'Employee User',
                'email' => 'emp@pos.com',
                'role' => 'employee',
                'password' => '12345', // Mutator will hash this
                'is_active' => true,
            ]
        );

        // Assign branch to employee if not already assigned
        if (!$employee->branches()->where('branch_id', $branch->id)->exists()) {
            $employee->branches()->attach($branch->id);
        }
    }
}

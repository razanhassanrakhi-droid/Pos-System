<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Dashboard
            'view-dashboard',

            // Users
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Branches
            'view-branches',
            'create-branches',
            'edit-branches',
            'delete-branches',

            // Categories
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',

            // Products
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',

            // Suppliers
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'delete-suppliers',

            // Purchases
            'view-purchases',
            'create-purchases',
            'edit-purchases',
            'delete-purchases',

            // Sales
            'view-sales',
            'create-sales',
            'edit-sales',
            'delete-sales',

            // Customers
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',

            // Expenses
            'view-expenses',
            'create-expenses',
            'edit-expenses',
            'delete-expenses',
            'manage-expense-types',

            // Warranties
            'view-warranties',
            'create-warranties',
            'edit-warranties',
            'delete-warranties',
            'print-warranties',

            // Adjustments
            'view-adjustments',
            'create-adjustments',
            'edit-adjustments',
            'delete-adjustments',

            // Reports
            'view-reports',

            // Settings
            'manage-settings',

            // Notifications
            'view-notifications',

            // Permissions
            'manage-permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create Roles and assign permissions
        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo(Permission::all());

        $employeeRole = Role::findOrCreate('employee');
        $employeeRole->givePermissionTo([
            'view-dashboard',
            'view-categories',
            'view-products',
            'create-products',
            'view-suppliers',
            'view-purchases',
            'create-purchases',
            'view-sales',
            'create-sales',
            'view-customers',
            'create-customers',
            'view-expenses',
            'create-expenses',
            'manage-expense-types',
            'view-warranties',
            'create-warranties',
            'edit-warranties',
            'print-warranties',
            'view-adjustments',
            'create-adjustments',
        ]);

        // Assign roles to existing users based on their 'role' column
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role === 'admin') {
                $user->assignRole($adminRole);
            } elseif ($user->role === 'employee') {
                $user->assignRole($employeeRole);
            }
        }
    }
}

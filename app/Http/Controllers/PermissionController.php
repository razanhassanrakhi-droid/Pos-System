<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        $users = User::with('permissions', 'roles')->paginate(10);
        return view('permissions.index', compact('users'));
    }

    public function edit(User $user)
    {
        $permissions = Permission::all()->groupBy(function($item) {
            // Group permissions by the first part of their name (e.g. 'view-products' -> 'products')
            $parts = explode('-', $item->name);
            return count($parts) > 1 ? $parts[1] : 'other';
        });

        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        
        return view('permissions.edit', compact('user', 'permissions', 'userPermissions'));
    }

    public function update(Request $request, User $user)
    {
        $user->syncPermissions($request->permissions);

        // If not admin, ensure role permissions don't override direct permissions
        if (!$user->isAdmin()) {
            $user->syncRoles([]);
        }

        // Clear Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permissions.index')->with('success', __('pos.permissions_updated_successfully'));
    }
}

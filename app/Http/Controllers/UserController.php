<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('branches')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        return view('users.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name_ar' => 'required|string|max:150',
            'full_name_en' => 'required|string|max:150',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'nullable|email|max:150|unique:users',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,employee',
            'password' => 'required|string|min:6',
            'branches' => 'required_if:role,employee|array',
            'branches.*' => 'exists:branches,id',
        ]);

        $user = User::create([
            'full_name' => [
                'ar' => $request->full_name_ar,
                'en' => $request->full_name_en,
            ],
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => $request->password, // Mutator handles hashing
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->has('branches')) {
            $user->branches()->sync($request->branches);
        }

        // Sync Spatie Role
        $user->assignRole($request->role);

        // Dispatch User Created notification
        \App\Services\NotificationService::send(
            'Administration',
            'user_created',
            'Activity',
            'تم إنشاء مستخدم جديد',
            'User Created',
            'تم إنشاء حساب مستخدم جديد باسم "' . $user->full_name . '".',
            'A new user account "' . $user->full_name . '" was created.',
            User::class,
            $user->id
        );

        return redirect()->route('users.index')->with('success', __('pos.user_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $branches = Branch::where('is_active', true)->get();
        return view('users.edit', compact('user', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name_ar' => 'required|string|max:150',
            'full_name_en' => 'required|string|max:150',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,employee',
            'password' => 'nullable|string|min:6',
            'branches' => 'required_if:role,employee|array',
            'branches.*' => 'exists:branches,id',
        ]);

        $userData = [
            'full_name' => [
                'ar' => $request->full_name_ar,
                'en' => $request->full_name_en,
            ],
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $userData['password'] = $request->password; // Mutator handles hashing
        }

        $user->update($userData);

        if ($request->has('branches')) {
            $user->branches()->sync($request->branches);
        } else {
            // If no branches selected (e.g. unchecked all), sync empty array
            $user->branches()->sync([]);
        }

        // Sync Spatie Role
        $user->syncRoles([$request->role]);

        // Dispatch Role Updated notification
        \App\Services\NotificationService::send(
            'Administration',
            'role_updated',
            'Activity',
            'تم تعديل دور المستخدم',
            'User Role Updated',
            'تم تعديل دور/صلاحيات حساب المستخدم "' . $user->full_name . '" إلى ' . $request->role . '.',
            'User account role for "' . $user->full_name . '" was updated to ' . $request->role . '.',
            User::class,
            $user->id
        );

        return redirect()->route('users.index')->with('success', __('pos.user_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('pos.cannot_delete_yourself'));
        }
        
        $user->branches()->detach(); // Clean up pivot
        $user->delete();

        return redirect()->route('users.index')->with('success', __('pos.user_deleted_successfully'));
    }
}

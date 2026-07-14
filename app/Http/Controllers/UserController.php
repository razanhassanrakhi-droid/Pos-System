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
        session(['users_index_url' => request()->fullUrl()]);
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
            'full_name_ar' => 'required_without:full_name_en|string|max:150|nullable',
            'full_name_en' => 'required_without:full_name_ar|string|max:150|nullable',
            'username_ar' => 'required_without:username_en|string|max:50|nullable',
            'username_en' => 'required_without:username_ar|string|max:50|nullable',
            'email' => 'nullable|email|max:150|unique:users',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,employee',
            'password' => 'required|string|min:6',
            'branches' => 'required_if:role,employee|array',
            'branches.*' => 'exists:branches,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $usernameAr = $request->username_ar ?? $request->username_en;
        $usernameEn = $request->username_en ?? $request->username_ar;

        // Custom validation for unique username in json/string
        $existing = User::where('username->ar', $usernameAr)
            ->orWhere('username->en', $usernameEn)
            ->orWhere('username', $usernameAr)
            ->orWhere('username', $usernameEn)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->withErrors(['username_ar' => __('validation.unique', ['attribute' => 'username'])]);
        }

        $user = User::create([
            'full_name' => [
                'ar' => $request->full_name_ar ?: $request->full_name_en,
                'en' => $request->full_name_en ?: $request->full_name_ar,
            ],
            'username' => [
                'ar' => $usernameAr,
                'en' => $usernameEn,
            ],
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => $request->password, // Mutator handles hashing
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->has('branches')) {
            $user->branches()->sync($request->branches);
        }

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $avatarPath]);
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
            'full_name_ar' => 'required_without:full_name_en|string|max:150|nullable',
            'full_name_en' => 'required_without:full_name_ar|string|max:150|nullable',
            'username_ar' => 'required_without:username_en|string|max:50|nullable',
            'username_en' => 'required_without:username_ar|string|max:50|nullable',
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,employee',
            'password' => 'nullable|string|min:6',
            'branches' => 'required_if:role,employee|array',
            'branches.*' => 'exists:branches,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $usernameAr = $request->username_ar ?? $request->username_en;
        $usernameEn = $request->username_en ?? $request->username_ar;

        // Custom validation for unique username in json/string (excluding current user)
        $existing = User::where('id', '!=', $user->id)
            ->where(function($query) use ($usernameAr, $usernameEn) {
                $query->where('username->ar', $usernameAr)
                      ->orWhere('username->en', $usernameEn)
                      ->orWhere('username', $usernameAr)
                      ->orWhere('username', $usernameEn);
            })
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->withErrors(['username_ar' => __('validation.unique', ['attribute' => 'username'])]);
        }

        $userData = [
            'full_name' => [
                'ar' => $request->full_name_ar ?: $request->full_name_en,
                'en' => $request->full_name_en ?: $request->full_name_ar,
            ],
            'username' => [
                'ar' => $usernameAr,
                'en' => $usernameEn,
            ],
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $userData['password'] = $request->password; // Mutator handles hashing
        }

        $user->fill($userData);

        $avatarChanged = false;
        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
            $avatarChanged = true;
        }

        $isDirty = $user->isDirty() || $avatarChanged;

        if ($isDirty) {
            $user->save();
        }

        if ($request->has('branches')) {
            $branchesSync = $user->branches()->sync($request->branches);
        } else {
            $branchesSync = $user->branches()->sync([]);
        }
        $branchesChanged = !empty($branchesSync['attached']) || !empty($branchesSync['detached']) || !empty($branchesSync['updated']);

        $hasRole = $user->hasRole($request->role);
        $user->syncRoles([$request->role]);
        $roleChanged = !$hasRole;

        if ($isDirty || $branchesChanged || $roleChanged) {
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

            $indexUrl = session('users_index_url', route('users.index'));
            return redirect()->to($indexUrl)->with('success', __('pos.user_updated_successfully'));
        }

        $indexUrl = session('users_index_url', route('users.index'));
        return redirect()->to($indexUrl)->with('info', __('pos.no_changes_made'));
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

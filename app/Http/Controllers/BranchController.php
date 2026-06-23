<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $branches = Branch::all(); // جلب كل الفروع
    return view('branches.index', compact('branches'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    // Validation
    $request->validate([
        'name_ar' => 'required|string|max:255',
        'name_en' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:branches,code',
        'phone' => 'nullable|string|max:20',
        'address_ar' => 'nullable|string|max:255',
        'address_en' => 'nullable|string|max:255',
        'city_ar' => 'nullable|string|max:100',
        'city_en' => 'nullable|string|max:100',
        'is_active' => 'nullable',
    ]);

    Branch::create([
        'name' => ['ar' => $request->name_ar, 'en' => $request->name_en],
        'code' => $request->code,
        'phone' => $request->phone,
        'address' => ['ar' => $request->address_ar, 'en' => $request->address_en],
        'city' => ['ar' => $request->city_ar, 'en' => $request->city_en],
        'is_active' => $request->has('is_active'),
    ]);

    // إعادة توجيه مع رسالة نجاح
    return redirect()->route('branches.index')->with('success', __('pos.branch_created_successfully'));
}
    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
{
    return view('branches.edit', compact('branch'));
}

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, Branch $branch)
{
    $request->validate([
        'name_ar' => 'required|string|max:255',
        'name_en' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:branches,code,'.$branch->id,
        'phone' => 'nullable|string|max:20',
        'address_ar' => 'nullable|string|max:255',
        'address_en' => 'nullable|string|max:255',
        'city_ar' => 'nullable|string|max:100',
        'city_en' => 'nullable|string|max:100',
        'is_active' => 'nullable',
    ]);

    $branch->update([
        'name' => ['ar' => $request->name_ar, 'en' => $request->name_en],
        'code' => $request->code,
        'phone' => $request->phone,
        'address' => ['ar' => $request->address_ar, 'en' => $request->address_en],
        'city' => ['ar' => $request->city_ar, 'en' => $request->city_en],
        'is_active' => $request->has('is_active'),
    ]);

    return redirect()->route('branches.index')->with('success', __('pos.branch_updated_successfully'));
}

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Branch $branch)
{
    $branch->delete();

    return redirect()
        ->route('branches.index')
        ->with('success', __('pos.branch_deleted_successfully'));
}

    /**
     * Switch the current active branch in session.
     */
    public function switch($id)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        // Handle "All Branches" switch (ID 0 or 'all')
        if ($id == 0 || $id === 'all') {
            if ($user->isAdmin()) {
                session(['branch_id' => null]);
                return redirect()->back()->with('success', __('pos.switched_to_all_branches') ?? 'Switched to all branches view.');
            }
            return redirect()->back()->with('error', 'Only admins can view all branches.');
        }

        // Verify access - if not admin, check if branch is assigned
        $branch = $user->accessibleBranches()->where('id', $id)->first();

        if ($branch) {
            session(['branch_id' => $branch->id]);
            return redirect()->back()->with('success', __('pos.branch_switched_successfully') ?? 'Switched branch successfully.');
        }

        return redirect()->back()->with('error', 'You do not have access to this branch.');
    }
}

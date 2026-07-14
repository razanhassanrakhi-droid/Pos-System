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
    session(['branches_index_url' => request()->fullUrl()]);
    $branches = Branch::all(); // جلب كل الفروع
    return view('branches.index', compact('branches'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nextId = (\App\Models\Branch::max('id') ?? 0) + 1;
        $nextCode = 'BR-' . sprintf('%04d', $nextId);
        return view('branches.create', compact('nextCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    // Validation
    $request->validate([
        'name_ar' => 'required_without:name_en|string|max:255|nullable',
        'name_en' => 'required_without:name_ar|string|max:255|nullable',
        'code' => 'required|string|max:50|unique:branches,code',
        'phone' => 'nullable|string|max:20',
        'address_ar' => 'nullable|string|max:255',
        'address_en' => 'nullable|string|max:255',
        'city_ar' => 'nullable|string|max:100',
        'city_en' => 'nullable|string|max:100',
        'manager_ar' => 'nullable|string|max:255',
        'manager_en' => 'nullable|string|max:255',
        'is_active' => 'nullable',
    ]);

    $nameAr = $request->name_ar ?: $request->name_en;
    $nameEn = $request->name_en ?: $request->name_ar;
    $managerAr = $request->manager_ar ?: $request->manager_en;
    $managerEn = $request->manager_en ?: $request->manager_ar;
    $addressAr = $request->address_ar ?: $request->address_en;
    $addressEn = $request->address_en ?: $request->address_ar;
    $cityAr = $request->city_ar ?: $request->city_en;
    $cityEn = $request->city_en ?: $request->city_ar;

    Branch::create([
        'name' => ['ar' => $nameAr, 'en' => $nameEn],
        'code' => $request->code,
        'phone' => $request->phone,
        'manager' => ['ar' => $managerAr, 'en' => $managerEn],
        'address' => ['ar' => $addressAr, 'en' => $addressEn],
        'city' => ['ar' => $cityAr, 'en' => $cityEn],
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
        'name_ar' => 'required_without:name_en|string|max:255|nullable',
        'name_en' => 'required_without:name_ar|string|max:255|nullable',
        'code' => 'required|string|max:50|unique:branches,code,'.$branch->id,
        'phone' => 'nullable|string|max:20',
        'address_ar' => 'nullable|string|max:255',
        'address_en' => 'nullable|string|max:255',
        'city_ar' => 'nullable|string|max:100',
        'city_en' => 'nullable|string|max:100',
        'manager_ar' => 'nullable|string|max:255',
        'manager_en' => 'nullable|string|max:255',
        'is_active' => 'nullable',
    ]);

    $nameAr = $request->name_ar ?: $request->name_en;
    $nameEn = $request->name_en ?: $request->name_ar;
    $managerAr = $request->manager_ar ?: $request->manager_en;
    $managerEn = $request->manager_en ?: $request->manager_ar;
    $addressAr = $request->address_ar ?: $request->address_en;
    $addressEn = $request->address_en ?: $request->address_ar;
    $cityAr = $request->city_ar ?: $request->city_en;
    $cityEn = $request->city_en ?: $request->city_ar;

    $branch->fill([
        'name' => ['ar' => $nameAr, 'en' => $nameEn],
        'code' => $request->code,
        'phone' => $request->phone,
        'manager' => ['ar' => $managerAr, 'en' => $managerEn],
        'address' => ['ar' => $addressAr, 'en' => $addressEn],
        'city' => ['ar' => $cityAr, 'en' => $cityEn],
        'is_active' => $request->has('is_active'),
    ]);

    if ($branch->isDirty()) {
        $branch->save();
        $indexUrl = session('branches_index_url', route('branches.index'));
        return redirect()->to($indexUrl)->with('success', __('pos.branch_updated_successfully'));
    }

    $indexUrl = session('branches_index_url', route('branches.index'));
    return redirect()->to($indexUrl)->with('info', __('pos.no_changes_made'));
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

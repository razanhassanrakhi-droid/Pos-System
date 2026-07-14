<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Branch;

class CategoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $branchId = session('branch_id');

        // Admin sees all categories, Employee only sees branch-related categories
        if ($user && $user->isAdmin()) {
            $categories = Category::all();
        } else {
            $categories = $branchId
                ? Category::where('branch_id', $branchId)->get()
                : collect(); // Return empty if no branch selected for non-admin
        }

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $branchId = session('branch_id') ?: ($user->branches()->first()?->id ?: \App\Models\Branch::value('id'));

        if (!$branchId) {
            return redirect()->back()->with('error', 'No branch selected.');
        }

        $request->validate([
            'name_ar' => 'required_without:name_en|string|max:150|nullable',
            'name_en' => 'required_without:name_ar|string|max:150|nullable',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->has('is_active') ? (bool) $request->is_active : true;

        $nameAr = $request->name_ar ?: $request->name_en;
        $nameEn = $request->name_en ?: $request->name_ar;

        Category::create([
            'branch_id' => $branchId,
            'name' => ['ar' => $nameAr, 'en' => $nameEn],
            'is_active' => $isActive,
            'created_by' => $user->id,
        ]);

        return redirect()->route('categories.index')->with('success', __('pos.category_created_successfully'));
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $user = auth()->user();
        $branchId = session('branch_id') ?: ($user->branches()->first()?->id ?: \App\Models\Branch::value('id'));

        $request->validate([
            'name_ar' => 'required_without:name_en|string|max:150|nullable',
            'name_en' => 'required_without:name_ar|string|max:150|nullable',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->has('is_active') ? (bool) $request->is_active : true;

        $nameAr = $request->name_ar ?: $request->name_en;
        $nameEn = $request->name_en ?: $request->name_ar;

        $category->fill([
            'name' => ['ar' => $nameAr, 'en' => $nameEn],
            'is_active' => $isActive,
            'updated_by' => $user->id,
        ]);

        if ($category->isDirty()) {
            $category->save();
            return redirect()->route('categories.index')->with('success', __('pos.category_updated_successfully'));
        }

        return redirect()->route('categories.index')->with('info', __('pos.no_changes_made'));
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('success', __('pos.category_deleted_successfully'));
    }
}

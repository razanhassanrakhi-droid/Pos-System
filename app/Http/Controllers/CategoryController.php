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
            'name_ar' => 'required|string|max:150',
            'name_en' => 'required|string|max:150',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->has('is_active') ? (bool) $request->is_active : true;

        Category::create([
            'branch_id' => $branchId,
            'name' => ['ar' => $request->name_ar, 'en' => $request->name_en],
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
            'name_ar' => 'required|string|max:150',
            'name_en' => 'required|string|max:150',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->has('is_active') ? (bool) $request->is_active : true;

        $category->update([
            'name' => ['ar' => $request->name_ar, 'en' => $request->name_en],
            'is_active' => $isActive,
            'updated_by' => $user->id,
        ]);

        return redirect()->route('categories.index')->with('success', __('pos.category_updated_successfully'));
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('success', __('pos.category_deleted_successfully'));
    }
}

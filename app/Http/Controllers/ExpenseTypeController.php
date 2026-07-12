<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
{
    /**
     * Store a newly created expense type in storage via AJAX.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required_without:name_en|string|max:255|nullable',
            'name_en' => 'required_without:name_ar|string|max:255|nullable',
        ]);

        $nameAr = $request->name_ar ?: $request->name_en;
        $nameEn = $request->name_en ?: $request->name_ar;

        $type = ExpenseType::create([
            'name_ar' => $nameAr,
            'name_en' => $nameEn,
        ]);

        return response()->json([
            'success' => true,
            'id' => $type->id,
            'name' => $type->getTranslation('name'),
            'message' => 'Expense type added successfully.'
        ]);
    }

    /**
     * Remove the specified expense type from storage via AJAX.
     */
    public function destroy($id)
    {
        $type = ExpenseType::findOrFail($id);
        $type->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense type deleted successfully.'
        ]);
    }
}

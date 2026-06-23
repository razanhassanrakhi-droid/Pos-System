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
            'name_ar' => 'required|string|max:255|unique:expense_types,name_ar',
            'name_en' => 'required|string|max:255|unique:expense_types,name_en',
        ]);

        $type = ExpenseType::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
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

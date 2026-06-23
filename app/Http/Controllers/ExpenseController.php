<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // عرض كل المصروفات للفرع الحالي
    public function index()
    {
        $branchId = session('branch_id');
        $expenses = Expense::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                           ->with('user') // Load the creator
                           ->orderBy('expense_date', 'desc')
                           ->get();

        return view('expenses.index', compact('expenses'));
    }

    // عرض نموذج إضافة مصروف جديد
    public function create()
    {
        $types = ExpenseType::all();
        return view('expenses.create', compact('types'));
    }

    // حفظ مصروف جديد
    public function store(Request $request)
    {
        $branchId = session('branch_id');

        $request->validate([
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string', // Added validation for bilingual descriptions
        ]);

        Expense::create([
            'type' => $request->type,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'status' => $request->has('status'), // true إذا مفعل، false إذا غير مفعل
            'user_id' => auth()->id(),
            'branch_id' => $branchId,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    // عرض نموذج تعديل مصروف
    public function edit(Expense $expense)
    {
        $user = auth()->user();
        $branchId = session('branch_id');

        // Allow admin to edit any expense, others only within their branch
        if (!$user->isAdmin() && $expense->branch_id != $branchId) {
            abort(403, 'Unauthorized access to this expense.');
        }

        $types = ExpenseType::all();
        return view('expenses.edit', compact('expense', 'types'));
    }

    // تحديث مصروف
    public function update(Request $request, Expense $expense)
    {
        $user = auth()->user();
        $branchId = session('branch_id');

        if (!$user->isAdmin() && $expense->branch_id != $branchId) {
            abort(403, 'Unauthorized access to this expense.');
        }

        $request->validate([
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        $expense->update([
            'type' => $request->type,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    // حذف مصروف
    public function destroy(Expense $expense)
    {
        $user = auth()->user();
        $branchId = session('branch_id');

        if (!$user->isAdmin() && $expense->branch_id != $branchId) {
            abort(403, 'Unauthorized access to this expense.');
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index()
    {
        $branchId = session('branch_id');
        $query = Expense::when($branchId, fn($q) => $q->where('branch_id', $branchId));
        
        $expenses = $query->with('user')
                           ->orderBy('expense_date', 'desc')
                           ->get();

        $today = Carbon::today();
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisMonthStart = Carbon::now()->startOfMonth();
        $thisYearStart = Carbon::now()->startOfYear();

        $stats = [
            'today' => (clone $query)->whereDate('expense_date', $today)->where('status', 'Approved')->sum('amount'),
            'week' => (clone $query)->where('expense_date', '>=', $thisWeekStart)->where('status', 'Approved')->sum('amount'),
            'month' => (clone $query)->where('expense_date', '>=', $thisMonthStart)->where('status', 'Approved')->sum('amount'),
            'year' => (clone $query)->where('expense_date', '>=', $thisYearStart)->where('status', 'Approved')->sum('amount'),
            'count' => (clone $query)->count(),
        ];

        return view('expenses.index', compact('expenses', 'stats'));
    }

    public function create()
    {
        $types = ExpenseType::all();
        return view('expenses.create', compact('types'));
    }

    public function store(Request $request)
    {
        $branchId = session('branch_id');

        $request->validate([
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'status' => 'required|string|in:Draft,Approved,Cancelled',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $descAr = $request->description_ar;
        $descEn = $request->description_en;
        if (empty($descAr) && !empty($descEn)) $descAr = $descEn;
        if (empty($descEn) && !empty($descAr)) $descEn = $descAr;

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('expenses', 'public');
        }

        // Generate Expense Number
        $lastExpense = Expense::orderBy('id', 'desc')->first();
        $sequence = $lastExpense ? $lastExpense->id + 1 : 1;
        $expenseNumber = '#' . $sequence;

        Expense::create([
            'expense_number' => $expenseNumber,
            'type' => $request->type,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'expense_date' => $request->expense_date,
            'description_ar' => $descAr,
            'description_en' => $descEn,
            'attachment' => $attachmentPath,
            'status' => $request->status,
            'user_id' => auth()->id(),
            'branch_id' => $branchId ?? 1,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    public function show(Expense $expense)
    {
        $user = auth()->user();
        $branchId = session('branch_id');

        if (!$user->isAdmin() && $expense->branch_id != $branchId) {
            abort(403, 'Unauthorized action.');
        }

        $expense->attachment_url = $expense->attachment_url; // append attribute

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($expense);
        }

        return abort(404);
    }

    public function edit(Expense $expense)
    {
        $user = auth()->user();
        $branchId = session('branch_id');

        if (!$user->isAdmin() && $expense->branch_id != $branchId) {
            abort(403, 'Unauthorized access to this expense.');
        }

        $types = ExpenseType::all();
        return view('expenses.edit', compact('expense', 'types'));
    }

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
            'payment_method' => 'required|string|max:255',
            'status' => 'required|string|in:Draft,Approved,Cancelled',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $descAr = $request->description_ar;
        $descEn = $request->description_en;
        if (empty($descAr) && !empty($descEn)) $descAr = $descEn;
        if (empty($descEn) && !empty($descAr)) $descEn = $descAr;

        if ($request->hasFile('attachment')) {
            if ($expense->attachment) {
                Storage::disk('public')->delete($expense->attachment);
            }
            $expense->attachment = $request->file('attachment')->store('expenses', 'public');
        }

        $expense->fill([
            'type' => $request->type,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'expense_date' => $request->expense_date,
            'description_ar' => $descAr,
            'description_en' => $descEn,
            'status' => $request->status,
        ]);

        if ($expense->isDirty()) {
            $expense->save();
            return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
        }

        return redirect()->route('expenses.index')->with('info', __('pos.no_changes_made'));
    }

    public function destroy(Expense $expense)
    {
        $user = auth()->user();
        $branchId = session('branch_id');

        if (!$user->isAdmin() && $expense->branch_id != $branchId) {
            abort(403, 'Unauthorized access to this expense.');
        }

        if ($expense->attachment) {
            Storage::disk('public')->delete($expense->attachment);
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }

    public function print(Expense $expense)
    {
        $user = auth()->user();
        $branchId = session('branch_id');

        if (!$user->isAdmin() && $expense->branch_id != $branchId) {
            abort(403, 'Unauthorized access to this expense.');
        }

        $setting = \App\Models\Setting::first();
        return view('expenses.print', compact('expense', 'setting'));
    }
}
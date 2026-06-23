<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Branch;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $branchId = session('branch_id');

        $query = Supplier::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with(['branch', 'createdBy'])
            ->withCount(['purchases' => fn($q) => $q->when($branchId, fn($q2) => $q2->where('branch_id', $branchId))])
            ->withMax(['purchases' => fn($q) => $q->when($branchId, fn($q2) => $q2->where('branch_id', $branchId))], 'created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('supplier_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('alternative_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $suppliers = $query->latest()->paginate(15);
        $statuses = ['active', 'inactive', 'blocked'];

        $kpis = [
            'total_suppliers' => Supplier::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count(),
            'active_suppliers' => Supplier::when($branchId, fn($q) => $q->where('branch_id', $branchId))->where('status', 'active')->count(),
            'total_purchases' => \App\Models\Purchase::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count(),
            'month_purchases' => \App\Models\Purchase::when($branchId, fn($q) => $q->where('branch_id', $branchId))->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        return view('suppliers.index', compact('suppliers', 'statuses', 'kpis'));
    }

    public function create()
    {
        return redirect()->route('suppliers.index')->with('info', 'Please use the Add Supplier button on this page.');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $branchId = session('branch_id') ?: ($user->branches()->first()?->id ?: Branch::value('id'));

        if (!$branchId) {
            return redirect()->back()->with('error', __('pos.no_branch_selected'));
        }

        $request->validate([
            'name_ar' => 'required_without:name_en|string|max:255|nullable',
            'name_en' => 'required_without:name_ar|string|max:255|nullable',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'alternative_phone' => 'nullable|string|max:20',
            'address_ar' => 'nullable|string',
            'address_en' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,blocked',
        ]);

        Supplier::create([
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'contact_person' => [
                'ar' => $request->contact_person_ar,
                'en' => $request->contact_person_en,
            ],
            'email' => $request->email,
            'phone' => $request->phone,
            'alternative_phone' => $request->alternative_phone,
            'address' => [
                'ar' => $request->address_ar,
                'en' => $request->address_en,
            ],
            'notes' => $request->notes,
            'status' => $request->status ?? 'active',
            'branch_id' => $branchId,
            'created_by' => $user->id,
        ]);

        return redirect()->route('suppliers.index')->with('success', __('pos.supplier_created_successfully'));
    }

    public function quickStore(Request $request)
    {
        $user = auth()->user();
        $branchId = session('branch_id') ?: ($user->branches()->first()?->id ?: Branch::value('id'));

        if (!$branchId) {
            return response()->json(['success' => false, 'message' => __('pos.no_branch_selected')], 422);
        }

        $request->validate([
            'name_ar' => 'required_without:name_en|string|max:255|nullable',
            'name_en' => 'required_without:name_ar|string|max:255|nullable',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person_ar' => 'nullable|string|max:255',
            'contact_person_en' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,blocked',
        ]);

        $supplier = Supplier::create([
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'contact_person' => [
                'ar' => $request->contact_person_ar,
                'en' => $request->contact_person_en,
            ],
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status ?? 'active',
            'branch_id' => $branchId,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'supplier' => $supplier,
            'name' => $supplier->getTranslation('name'),
            'supplier_number' => $supplier->supplier_number,
        ]);
    }

    public function show(Request $request, $id)
    {
        $supplier = Supplier::with(['branch', 'createdBy'])->findOrFail($id);
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'id' => $supplier->id,
                'name_ar' => $supplier->getTranslation('name', 'ar'),
                'name_en' => $supplier->getTranslation('name', 'en'),
                'contact_person_ar' => $supplier->getTranslation('contact_person', 'ar'),
                'contact_person_en' => $supplier->getTranslation('contact_person', 'en'),
                'phone' => $supplier->phone,
                'alternative_phone' => $supplier->alternative_phone,
                'email' => $supplier->email,
                'address_ar' => $supplier->getTranslation('address', 'ar'),
                'address_en' => $supplier->getTranslation('address', 'en'),
                'notes' => $supplier->notes,
                'status' => $supplier->status ?? 'active',
                'supplier_number' => $supplier->supplier_number,
                'created_at' => $supplier->created_at->format('Y-m-d H:i:s')
            ]);
        }
        
        $totalPurchases = $supplier->purchases()->count();
        $totalPurchaseValue = $supplier->purchases()->sum('total_amount');
        $outstandingBalance = $supplier->purchases()->sum('remaining_amount');
        $lastPurchaseDate = $supplier->purchases()->max('created_at');
        
        $purchases = $supplier->purchases()->latest()->paginate(15);

        return view('suppliers.show', compact(
            'supplier', 
            'totalPurchases', 
            'totalPurchaseValue', 
            'outstandingBalance', 
            'lastPurchaseDate',
            'purchases'
        ));
    }

    public function edit($id)
    {
        return redirect()->route('suppliers.index')->with('info', 'Please use the edit button on the supplier list to edit this supplier.');
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $user = auth()->user();

        $request->validate([
            'name_ar' => 'required_without:name_en|string|max:255|nullable',
            'name_en' => 'required_without:name_ar|string|max:255|nullable',
            'contact_person_ar' => 'nullable|string|max:255',
            'contact_person_en' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'alternative_phone' => 'nullable|string|max:20',
            'address_ar' => 'nullable|string',
            'address_en' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,blocked',
        ]);

        $supplier->update([
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'contact_person' => [
                'ar' => $request->contact_person_ar,
                'en' => $request->contact_person_en,
            ],
            'email' => $request->email,
            'phone' => $request->phone,
            'alternative_phone' => $request->alternative_phone,
            'address' => [
                'ar' => $request->address_ar,
                'en' => $request->address_en,
            ],
            'notes' => $request->notes,
            'status' => $request->status ?? 'active',
            'updated_by' => $user->id,
        ]);

        return redirect()->route('suppliers.index')->with('success', __('pos.supplier_updated_successfully'));
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', __('pos.supplier_deleted_successfully'));
    }
}

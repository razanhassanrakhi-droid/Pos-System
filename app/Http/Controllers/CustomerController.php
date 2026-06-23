<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('branch_id');
        $user = auth()->user();

        $query = Customer::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with('branch')
            ->withCount(['sales' => fn($q) => $q->when($branchId, fn($q2) => $q2->where('branch_id', $branchId))])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name->ar', 'like', "%{$search}%")
                  ->orWhere('name->en', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('customer_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('customer_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $totalCustomers = $query->count();
        $customers = $query->paginate(20)->appends($request->all());

        $customerTypes = ['Walk-in', 'Regular', 'Wholesale', 'VIP'];
        $statuses = ['Active', 'Inactive', 'Blocked'];

        // KPIs
        $kpis = [
            'total_customers' => \App\Models\Customer::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count(),
            'active_customers' => \App\Models\Customer::when($branchId, fn($q) => $q->where('branch_id', $branchId))->where('status', 'Active')->count(),
            'total_visits' => \App\Models\Sale::when($branchId, fn($q) => $q->where('branch_id', $branchId))->whereNotNull('customer_id')->count(),
            'month_purchases' => \App\Models\Sale::when($branchId, fn($q) => $q->where('branch_id', $branchId))->whereNotNull('customer_id')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        return view('customers.index', compact('customers', 'totalCustomers', 'customerTypes', 'statuses', 'kpis'));
    }

    public function store(Request $request)
    {
        $branchId = session('branch_id');

        $request->validate([
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'nullable|string|max:255',
            'phone'         => 'required|string|max:20|unique:customers,phone',
            'email'         => 'nullable|email|max:255',
            'address'       => 'nullable|string|max:500',
            'notes'         => 'nullable|string',
            'customer_type' => 'required|string|in:Walk-in,Regular,Wholesale,VIP',
            'status'        => 'required|string|in:Active,Inactive,Blocked',
            'dob'           => 'nullable|date',
        ]);

        Customer::create([
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'phone'         => $request->phone,
            'email'         => $request->email,
            'address'       => $request->address,
            'notes'         => $request->notes,
            'branch_id'     => $branchId,
            'customer_type' => $request->customer_type,
            'status'        => $request->status,
            'dob'           => $request->dob,
        ]);

        return redirect()->route('customers.index')
            ->with('success', __('pos.customer_added_successfully'));
    }

    public function show(Request $request, Customer $customer)
    {
        $customer->load('branch');
        
        if ($request->expectsJson()) {
            return response()->json([
                'id'            => $customer->id,
                'name'          => $customer->name,
                'name_ar'       => $customer->getTranslation('name', 'ar'),
                'name_en'       => $customer->getTranslation('name', 'en'),
                'phone'         => $customer->phone,
                'email'         => $customer->email,
                'address'       => $customer->address,
                'notes'         => $customer->notes,
                'customer_type' => $customer->customer_type,
                'status'        => $customer->status,
                'dob'           => $customer->dob ? $customer->dob->format('Y-m-d') : null,
                'visit_count'   => $customer->visit_count,
                'branch'        => $customer->branch?->name ?? '-',
                'created_at'    => $customer->created_at->format('Y-m-d H:i'),
                'customer_number' => $customer->customer_number,
            ]);
        }
        
        return $this->profile($customer);
    }

    public function profile(Customer $customer)
    {
        // Load sales history
        $sales = \App\Models\Sale::where('customer_id', $customer->id)->latest()->get();
        
        $totalOrders = $sales->count();
        $totalPurchases = $sales->sum('total');
        $avgOrder = $totalOrders > 0 ? $totalPurchases / $totalOrders : 0;
        $lastPurchase = $sales->first() ? $sales->first()->created_at->format('d M Y') : null;
        
        // Load returns history if model exists (using empty collection if not)
        $returns = class_exists(\App\Models\SaleReturn::class) 
            ? \App\Models\SaleReturn::where('customer_id', $customer->id)->latest()->get()
            : collect([]);

        return view('customers.profile', compact(
            'customer', 'sales', 'returns', 'totalOrders', 'totalPurchases', 'avgOrder', 'lastPurchase'
        ));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'nullable|string|max:255',
            'phone'         => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'email'         => 'nullable|email|max:255',
            'address'       => 'nullable|string|max:500',
            'notes'         => 'nullable|string',
            'customer_type' => 'required|string|in:Walk-in,Regular,Wholesale,VIP',
            'status'        => 'required|string|in:Active,Inactive,Blocked',
            'dob'           => 'nullable|date',
        ]);

        $customer->update([
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'phone'         => $request->phone,
            'email'         => $request->email,
            'address'       => $request->address,
            'notes'         => $request->notes,
            'customer_type' => $request->customer_type,
            'status'        => $request->status,
            'dob'           => $request->dob,
        ]);

        return redirect()->route('customers.index')
            ->with('success', __('pos.customer_updated_successfully'));
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')
            ->with('success', __('pos.customer_deleted_successfully'));
    }
}

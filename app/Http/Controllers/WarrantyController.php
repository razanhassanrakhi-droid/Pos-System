<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('branch_id');
        $search = $request->input('search');
        $status = $request->input('status');
        $warrantyType = $request->input('warranty_type');
        $customerId = $request->input('customer_id');

        $query = Warranty::with(['product', 'customer', 'sale', 'branch'])
            ->where('branch_id', $branchId);

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('warranty_number', 'LIKE', "%{$search}%")
                  ->orWhere('serial_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('sale', function($sq) use ($search) {
                      $sq->where('invoice_number', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($warrantyType) {
            $query->where('warranty_type', $warrantyType);
        }

        if ($customerId) {
            if ($customerId === 'walk_in') {
                $query->whereNull('customer_id');
            } else {
                $query->where('customer_id', $customerId);
            }
        }

        // We fetch all to calculate dynamic status for KPIs, then filter for pagination
        // For very large datasets, a DB-level status calculation is better, but since it depends on relations (claims), we do it via collection for now, or we can filter in PHP.
        // Actually, to keep pagination efficient, let's filter what we can in DB, but status is dynamic.
        // The user requested standard statuses. We can filter the collection after getting it, or paginate first?
        // Since status is an accessor, filtering by it in DB is impossible. We must fetch all, or filter based on dates/claims.
        // Let's optimize: 'Expired' is `warranty_end_date < now()`. 'Expiring Soon' is `between now and +30 days`.
        
        if ($status) {
            if ($status === 'Expired') {
                $query->where('warranty_end_date', '<', now()->startOfDay())
                      ->whereNotIn('status', ['Cancelled', 'Completed'])
                      ->whereDoesntHave('claims', function($q) {
                          $q->whereIn('status', ['Pending', 'Approved']);
                      });
            } elseif ($status === 'Expiring Soon') {
                $query->whereBetween('warranty_end_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
                      ->whereNotIn('status', ['Cancelled', 'Completed'])
                      ->whereDoesntHave('claims', function($q) {
                          $q->whereIn('status', ['Pending', 'Approved']);
                      });
            } elseif ($status === 'Active') {
                $query->where('warranty_end_date', '>', now()->addDays(30)->endOfDay())
                      ->whereNotIn('status', ['Cancelled', 'Completed'])
                      ->whereDoesntHave('claims', function($q) {
                          $q->whereIn('status', ['Pending', 'Approved']);
                      });
            } elseif ($status === 'Claim Submitted') {
                $query->whereHas('claims', function($q) {
                    $q->where('status', 'Pending');
                });
            } elseif ($status === 'Claim Approved') {
                $query->whereHas('claims', function($q) {
                    $q->where('status', 'Approved');
                });
            } elseif ($status === 'Cancelled' || $status === 'Completed') {
                $query->where('status', $status);
            }
        }

        $warranties = $query->latest()->paginate(20)->withQueryString();

        // Calculate KPIs (Quick aggregate without full hydration)
        $kpiQuery = Warranty::where('branch_id', $branchId);
        
        $kpis = [
            'active' => (clone $kpiQuery)->where('warranty_end_date', '>=', now()->startOfDay())->whereNotIn('status', ['Cancelled', 'Completed'])->count(),
            'expiring_soon' => (clone $kpiQuery)->whereBetween('warranty_end_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])->count(),
            'expired' => (clone $kpiQuery)->where('warranty_end_date', '<', now()->startOfDay())->count(),
            'claims' => \App\Models\WarrantyClaim::whereHas('warranty', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->count(),
        ];
        
        $customers = Customer::all();

        return view('warranties.index', compact('warranties', 'search', 'status', 'kpis', 'warrantyType', 'customers', 'customerId'));
    }

    public function show($id)
    {
        $warranty = Warranty::with(['product', 'customer', 'sale', 'branch', 'saleItem'])->findOrFail($id);
        return view('warranties.show', compact('warranty'));
    }

    public function print($id)
    {
        $warranty = Warranty::with(['product', 'customer', 'sale', 'branch'])->findOrFail($id);
        return view('warranties.print', compact('warranty'));
    }

    public function upsert(Request $request)
    {
        $request->validate([
            'sale_item_id' => 'required|exists:sale_items,id',
            'serial_number' => 'nullable|string|max:255',
        ]);

        $saleItem = \App\Models\SaleItem::with(['product', 'sale'])->findOrFail($request->sale_item_id);
        
        $warranty = Warranty::updateOrCreate(
            ['sale_item_id' => $saleItem->id],
            [
                'sale_id' => $saleItem->sale_id,
                'product_id' => $saleItem->product_id,
                'customer_id' => $saleItem->sale->customer_id,
                'branch_id' => $saleItem->sale->branch_id,
                'serial_number' => $request->serial_number,
                'warranty_start_date' => $saleItem->sale->created_at,
                'warranty_end_date' => $saleItem->sale->created_at->addMonths((int)$saleItem->product->warranty_period_months),
                'warranty_period_months' => $saleItem->product->warranty_period_months,
                'status' => 'ACTIVE',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => __('product.updated_successfully'),
            'warranty' => $warranty
        ]);
    }
    public function storeClaim(Request $request, $id)
    {
        $request->validate([
            'claim_date' => 'required|date',
            'issue_description' => 'required|array',
        ]);

        $warranty = Warranty::findOrFail($id);

        $warranty->claims()->create([
            'claim_date' => $request->claim_date,
            'issue_description' => $request->issue_description,
            'status' => 'Pending',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('warranties.show', $id)->with('success', 'Warranty claim filed successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'serial_number' => 'nullable|string|max:255',
            'warranty_type' => 'nullable|string|max:255',
        ]);

        $warranty = Warranty::findOrFail($id);
        $warranty->update([
            'serial_number' => $request->serial_number,
            'warranty_type' => $request->warranty_type,
        ]);

        return redirect()->route('warranties.show', $id)->with('success', __('product.updated_successfully') ?? 'تم تحديث الضمان بنجاح');
    }

    public function updateClaim(Request $request, $claim_id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Approved,Rejected,Completed',
            'resolution' => 'nullable|array',
            'issue_description' => 'required|array',
        ]);

        $claim = \App\Models\WarrantyClaim::findOrFail($claim_id);
        $claim->update([
            'status' => $request->status,
            'resolution' => $request->resolution,
            'issue_description' => $request->issue_description,
        ]);

        return redirect()->route('warranties.show', $claim->warranty_id)->with('success', 'تم تحديث المطالبة بنجاح');
    }

    public function destroyClaim($claim_id)
    {
        $claim = \App\Models\WarrantyClaim::findOrFail($claim_id);
        $warrantyId = $claim->warranty_id;
        $claim->delete();

        return redirect()->route('warranties.show', $warrantyId)->with('success', 'تم حذف المطالبة بنجاح');
    }
}

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

        $query = Warranty::with(['product', 'customer', 'sale', 'branch'])
            ->where('branch_id', $branchId);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $warranties = $query->latest()->paginate(20)->withQueryString();

        return view('warranties.index', compact('warranties', 'search', 'status'));
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
}

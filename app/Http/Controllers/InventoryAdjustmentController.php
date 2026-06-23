<?php

namespace App\Http\Controllers;

use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\Batch;
use App\Models\StockMovement;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryAdjustmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $branchId = session('branch_id');

        $query = InventoryAdjustment::with(['product', 'batch', 'branch', 'user', 'productUnit'])
            ->when($branchId, function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->whereHas('product', function($q2) use ($branchId) {
                      $q2->where('branch_id', $branchId);
                  });
            })
            ->latest();

        $adjustments = $query->paginate(20);
        
        $products = Product::where('is_active', true)
            ->when($branchId, function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->get();

        // Calculate Dashboard Stats for Today
        $todayStr = now()->toDateString();
        
        $expiredToday = (float) abs(
            InventoryAdjustment::when($branchId, function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->whereHas('product', function($q2) use ($branchId) {
                      $q2->where('branch_id', $branchId);
                  });
            })
            ->where('adjustment_type', 'EXPIRED')
            ->whereDate('created_at', $todayStr)
            ->sum('quantity')
        );

        $damagedToday = (float) abs(
            InventoryAdjustment::when($branchId, function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->whereHas('product', function($q2) use ($branchId) {
                      $q2->where('branch_id', $branchId);
                  });
            })
            ->where('adjustment_type', 'DAMAGED')
            ->whereDate('created_at', $todayStr)
            ->sum('quantity')
        );

        $adjustmentsToday = InventoryAdjustment::when($branchId, function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->whereHas('product', function($q2) use ($branchId) {
                      $q2->where('branch_id', $branchId);
                  });
            })
            ->whereDate('created_at', $todayStr)
            ->count();

        // Loss value today (sum of absolute quantity * purchase_price)
        $lossValueToday = 0;
        $todaysAdjustments = InventoryAdjustment::when($branchId, function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->whereHas('product', function($q2) use ($branchId) {
                      $q2->where('branch_id', $branchId);
                  });
            })
            ->whereDate('created_at', $todayStr)
            ->with(['batch', 'product'])
            ->get();

        foreach ($todaysAdjustments as $adj) {
            $price = $adj->batch->purchase_price ?? ($adj->product->batches()->latest()->first()->purchase_price ?? 0);
            $lossValueToday += abs($adj->quantity) * $price;
        }

        return view('adjustments.index', compact(
            'adjustments', 
            'products', 
            'expiredToday', 
            'damagedToday', 
            'adjustmentsToday', 
            'lossValueToday'
        ));
    }

    public function store(Request $request)
    {
        $branchId = session('branch_id');

        $request->validate([
            'product_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('products', 'id')->where(function ($query) use ($branchId) {
                    if ($branchId) {
                        $query->where('branch_id', $branchId);
                    }
                }),
            ],
            'batch_id' => 'nullable|exists:batches,id',
            'product_unit_id' => 'nullable|exists:product_units,id',
            'quantity' => 'required|numeric',
            'adjustment_type' => 'required|in:EXPIRED,DAMAGED,LOST,STOCK_COUNT_ADJUSTMENT,OTHER',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $user = auth()->user();

        if (!$branchId) {
            return back()->with('error', __('purchases.no_branch_selected') ?? 'No branch selected.');
        }

        DB::beginTransaction();
        try {
            $qty = (float) $request->quantity;
            $factor = 1.0;
            if ($request->product_unit_id) {
                $unit = ProductUnit::findOrFail($request->product_unit_id);
                $factor = (float) $unit->conversion_factor ?: 1.0;
            }
            $baseQty = $qty * $factor;

            // Expired, Damaged, Lost are always stock reductions (negative)
            if (in_array($request->adjustment_type, ['EXPIRED', 'DAMAGED', 'LOST'])) {
                $baseQty = -abs($baseQty);
                $qty = -abs($qty);
            }

            $adjustment = InventoryAdjustment::create([
                'product_id' => $request->product_id,
                'batch_id' => $request->batch_id,
                'product_unit_id' => $request->product_unit_id,
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'quantity' => $baseQty,
                'entered_quantity' => $qty,
                'adjustment_type' => $request->adjustment_type,
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            // Update batch stock if selected
            if ($request->batch_id) {
                $batch = Batch::findOrFail($request->batch_id);
                // Prevent going below 0 for deductions
                if ($baseQty < 0 && abs($baseQty) > $batch->remaining_quantity) {
                    throw new \Exception('Quantity exceeds available batch stock (' . $batch->remaining_quantity . ').');
                }
                $batch->quantity += $baseQty;
                $batch->remaining_quantity += $baseQty;
                $batch->save();
            }

            // Log stock movement
            StockMovement::create([
                'product_id' => $request->product_id,
                'branch_id' => $branchId,
                'batch_id' => $request->batch_id,
                'type' => $baseQty < 0 ? 'out' : 'in',
                'quantity' => abs($baseQty),
                'reference_id' => $adjustment->id,
                'reference_type' => InventoryAdjustment::class,
                'note' => $request->adjustment_type . ': ' . ($request->reason ?: $request->notes),
                'created_by' => $user->id,
            ]);

            DB::commit();
            return redirect()->route('adjustments.index')->with('success', __('purchases.adjustment_saved_successfully') ?? 'Adjustment saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', (__('pos.error') ?? 'Error') . ': ' . $e->getMessage());
        }
    }

    public function show(InventoryAdjustment $adjustment)
    {
        $adjustment->load(['product', 'batch', 'user', 'branch', 'productUnit']);

        // Find associated Stock Movement
        $movement = StockMovement::where('reference_type', InventoryAdjustment::class)
            ->where('reference_id', $adjustment->id)
            ->first();

        $price = $adjustment->batch->purchase_price ?? ($adjustment->product->batches()->latest()->first()->purchase_price ?? 0);
        $lossValue = abs($adjustment->quantity) * $price;

        return response()->json([
            'id'                  => $adjustment->id,
            'adjustment_number'   => $adjustment->adjustment_number,
            'short_number'        => $adjustment->short_number,
            'quantity'            => (float) $adjustment->quantity,
            'entered_quantity'    => (float) ($adjustment->entered_quantity ?? $adjustment->quantity),
            'product_unit'        => $adjustment->productUnit ? $adjustment->productUnit->unit_name : ($adjustment->product->base_unit_name ?? 'Piece'),
            'adjustment_type'     => $adjustment->adjustment_type,
            'reason'              => $adjustment->reason,
            'notes'               => $adjustment->notes,
            'created_at'          => $adjustment->created_at?->format('d M Y h:i A'),
            'loss_value'          => number_format($lossValue, 2),
            'movement_reference'  => $movement ? $movement->movement_number : 'N/A',
            'product' => [
                'name'    => $adjustment->product?->name,
                'barcode' => $adjustment->product?->barcode,
            ],
            'batch' => $adjustment->batch ? [
                'batch_number' => $adjustment->batch->batch_number,
                'expiry_date'  => $adjustment->batch->expiry_date?->format('d-M-Y'),
            ] : null,
            'user' => [
                'name' => $adjustment->user?->full_name ?? $adjustment->user?->name,
            ],
            'branch' => [
                'name' => $adjustment->branch?->name,
            ]
        ]);
    }

    public function destroy(InventoryAdjustment $adjustment)
    {
        return back()->with('error', __('purchases.delete_adjustment_not_permitted') ?? 'Deleting adjustments is not permitted for audit integrity.');
    }
}

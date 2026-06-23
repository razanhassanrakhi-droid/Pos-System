<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesReturn;
use App\Models\Batch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesReturnController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('branch_id');
        
        $returns = SalesReturn::with(['sale', 'product', 'creator', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->paginate(20);

        return view('sales_returns.index', compact('returns'));
    }

    public function create()
    {
        return view('sales_returns.create');
    }

    public function searchSale(Request $request)
    {
        $searchQuery = trim($request->query('invoice_number'));
        $branchId = session('branch_id');

        // Clean query to extract raw digits or search term
        $cleanQuery = preg_replace('/^(invoice|فاتورة|purchase|مشتريات|sale|مبيعات|return|مرتجع|\s|#)+/iu', '', $searchQuery);
        $cleanQuery = trim($cleanQuery);

        $sale = Sale::with(['items.product', 'customer'])
            ->where(function($query) use ($searchQuery, $cleanQuery) {
                $query->where('invoice_number', $searchQuery)
                      ->orWhere('invoice_number', $cleanQuery)
                      ->when($cleanQuery, function($q) use ($cleanQuery) {
                          $q->orWhere('invoice_number', 'LIKE', '%' . $cleanQuery);
                          if (is_numeric($cleanQuery)) {
                              $padded = str_pad($cleanQuery, 5, '0', STR_PAD_LEFT);
                              $q->orWhere('invoice_number', 'LIKE', '%-' . $padded);
                          }
                      });
            })
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->first();

        if (!$sale) {
            return response()->json(['success' => false, 'message' => __('pos.not_found')], 404);
        }

        // Calculate already returned quantities for each item
        $returnedQuantities = SalesReturn::where('sale_id', $sale->id)
            ->groupBy('product_id', 'batch_id')
            ->select('product_id', 'batch_id', DB::raw('SUM(quantity) as total_returned'))
            ->get()
            ->keyBy(function($item) {
                return $item->product_id . '-' . $item->batch_id;
            });

        $distributedReturns = [];

        $saleItems = $sale->items->map(function($item) use ($returnedQuantities, &$distributedReturns) {
            $key = $item->product_id . '-' . $item->batch_id;
            $totalReturned = $returnedQuantities[$key]->total_returned ?? 0;
            
            if (!isset($distributedReturns[$key])) {
                $distributedReturns[$key] = 0;
            }
            
            $remainingReturnToDistribute = $totalReturned - $distributedReturns[$key];
            
            if ($remainingReturnToDistribute > 0) {
                $deducted = min($item->quantity, $remainingReturnToDistribute);
                $distributedReturns[$key] += $deducted;
                $item->available_to_return = max(0, $item->quantity - $deducted);
            } else {
                $item->available_to_return = $item->quantity;
            }
            return $item;
        });

        return response()->json([
            'success' => true,
            'sale' => $sale,
            'items' => $saleItems,
            'customer_name' => $sale->customer ? $sale->customer->name : __('pos.walk_in_customer')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.reason' => 'nullable|string',
        ]);

        $branchId = session('branch_id');
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            foreach ($request->items as $itemData) {
                $productId = $itemData['product_id'];
                $returnQty = $itemData['quantity'];
                $batchId = $itemData['batch_id'] ?? null;

                // Validate against sold quantity
                $soldQty = SaleItem::where('sale_id', $request->sale_id)
                    ->where('product_id', $productId)
                    ->where('batch_id', $batchId)
                    ->sum('quantity');

                $alreadyReturned = SalesReturn::where('sale_id', $request->sale_id)
                    ->where('product_id', $productId)
                    ->where('batch_id', $batchId)
                    ->sum('quantity');

                if ($returnQty > ($soldQty - $alreadyReturned)) {
                    throw new \Exception(__('pos.returned_quantity_exceeds_sold') . " (Product ID: $productId)");
                }

                // 1. Create Return Record
                SalesReturn::create([
                    'sale_id' => $request->sale_id,
                    'product_id' => $productId,
                    'batch_id' => $batchId,
                    'branch_id' => $branchId,
                    'quantity' => $returnQty,
                    'reason' => $itemData['reason'] ?? '',
                    'created_by' => $userId,
                ]);

                // 2. Increase Stock in Batch
                if ($batchId) {
                    $batch = Batch::findOrFail($batchId);
                    $batch->increment('quantity', $returnQty);
                    $batch->increment('remaining_quantity', $returnQty);
                }

                // 3. Log Stock Movement
                StockMovement::create([
                    'product_id' => $productId,
                    'branch_id' => $branchId,
                    'batch_id' => $batchId,
                    'type' => 'return',
                    'quantity' => $returnQty,
                    'reference_id' => $request->sale_id,
                    'reference_type' => Sale::class,
                    'note' => 'Sales Return for Sale ID: ' . $request->sale_id,
                    'created_by' => $userId,
                ]);
            }

            // Calculate total return value and dispatch notifications
            $totalReturnValue = 0;
            $sale = Sale::find($request->sale_id);
            foreach ($request->items as $itemData) {
                $soldItem = SaleItem::where('sale_id', $request->sale_id)
                    ->where('product_id', $itemData['product_id'])
                    ->where('batch_id', $itemData['batch_id'] ?? null)
                    ->first();
                if ($soldItem) {
                    $totalReturnValue += $itemData['quantity'] * ($soldItem->price / ($soldItem->conversion_factor ?? 1));
                }
            }

            $returnNum = $sale ? $sale->invoice_number : $request->sale_id;
            \App\Services\NotificationService::send(
                'Returns',
                'return_completed',
                'Important',
                'تمت عملية إرجاع مبيعات',
                'Return Completed',
                'تم إكمال مرتجع مبيعات للفاتورة رقم "' . $returnNum . '" بقيمة إجمالية ' . number_format($totalReturnValue, 2) . '.',
                'Sales return completed for invoice "' . $returnNum . '" with total value of ' . number_format($totalReturnValue, 2) . '.',
                Sale::class,
                $request->sale_id,
                $branchId,
                $userId
            );

            if ($totalReturnValue >= 1000) {
                \App\Services\NotificationService::send(
                    'Returns',
                    'large_return',
                    'Important',
                    'إرجاع مبيعات بقيمة عالية',
                    'Large Return Processed',
                    'تمت معالجة مرتجع مبيعات ذو قيمة عالية للفاتورة رقم "' . $returnNum . '" بقيمة ' . number_format($totalReturnValue, 2) . '.',
                    'A large sales return of ' . number_format($totalReturnValue, 2) . ' was processed for invoice "' . $returnNum . '".',
                    Sale::class,
                    $request->sale_id,
                    $branchId,
                    $userId
                );
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => __('pos.sales_return_created_successfully')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sales Return Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        $return = SalesReturn::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Reverse stock if needed, but usually returns are final.
            // If we delete a return, we might need to deduct stock again.
            if ($return->batch_id) {
                $batch = Batch::find($return->batch_id);
                if ($batch) {
                    $batch->decrement('quantity', $return->quantity);
                    $batch->decrement('remaining_quantity', $return->quantity);
                }
            }

            $return->delete();
            DB::commit();

            return redirect()->back()->with('success', __('pos.delete_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

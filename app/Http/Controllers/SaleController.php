<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Warranty;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('branch_id');
        $search = $request->input('search');
        $user = auth()->user();

        $query = Sale::with(['customer', 'user', 'items', 'returns'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        if ($search) {
            $cleanQuery = preg_replace('/^(invoice|فاتورة|purchase|مشتريات|sale|مبيعات|return|مرتجع|\s|#)+/iu', '', $search);
            $cleanQuery = trim($cleanQuery);

            $query->where(function($q) use ($search, $cleanQuery) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhere('invoice_number', $cleanQuery)
                  ->when($cleanQuery, function($sq) use ($cleanQuery) {
                      $sq->orWhere('invoice_number', 'LIKE', '%' . $cleanQuery);
                      if (is_numeric($cleanQuery)) {
                          $padded = str_pad($cleanQuery, 3, '0', STR_PAD_LEFT);
                          $sq->orWhere('invoice_number', 'LIKE', '%-' . $padded);
                      }
                  })
                  ->orWhereHas('customer', function($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('username', 'LIKE', "%{$search}%")
                         ->orWhere('full_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $sales = $query->latest()
            ->paginate(20)
            ->withQueryString();

        return view('sales.index', compact('sales', 'search'));
    }

    public function create()
    {
        $branchId = session('branch_id');
        $customers = Customer::all();
        // We'll load products via AJAX for better performance, but let's provide initial few
        $products = Product::where('branch_id', $branchId)->where('is_active', true)->where('status', 'Active')->limit(10)->get();
        $setting = \App\Models\Setting::first();
        
        return view('sales.create', compact('customers', 'products', 'setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_name' => 'nullable|string|max:255',
            'items.*.conversion_factor' => 'nullable|numeric|min:0.0001',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $branchId = session('branch_id');
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            // 1. Create Sale Record
            $sale = Sale::create([
                'invoice_number' => \App\Services\DocumentNumberService::generateContinuousNumber('INV', Sale::class, 'invoice_number', $branchId),
                'customer_id' => $request->customer_id,
                'branch_id' => $branchId,
                'user_id' => $userId,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'total' => $request->total,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $request->change_amount ?? 0,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'status' => $request->paid_amount >= $request->total ? 'paid' : ($request->paid_amount > 0 ? 'partial' : 'pending'),
            ]);

            // 2. Process Items with FEFO Logic
            foreach ($request->items as $itemData) {
                $productId = $itemData['product_id'];
                $product = Product::findOrFail($productId);
                if (!$product->is_active) {
                    throw new \Exception(app()->getLocale() == 'ar' ? "المنتج '{$product->name}' غير نشط ولا يمكن بيعه." : "Product '{$product->name}' is inactive and cannot be sold.");
                }
                $quantityToSell = $itemData['quantity'];
                $price = $itemData['price'];
                $conversionFactor = (float)($itemData['conversion_factor'] ?? 1);
                $unitName = $itemData['unit_name'] ?? null;
                $quantityToSellBase = $quantityToSell * $conversionFactor;

                // Find available batches for this product in FEFO order (closest expiry first)
                $batches = Batch::where('product_id', $productId)
                    ->where('branch_id', $branchId)
                    ->where('remaining_quantity', '>', 0)
                    ->orderByRaw('expiry_date IS NULL ASC')
                    ->orderBy('expiry_date', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->get();

                $remainingToSell = $quantityToSellBase;

                foreach ($batches as $batch) {
                    if ($remainingToSell <= 0) break;

                    $sellFromBatch = min($batch->remaining_quantity, $remainingToSell);
                    
                    // Always use centralized product sale price
                    if (isset($itemData['is_manual_price']) && $itemData['is_manual_price']) {
                        $unitPrice = $price;
                    } else {
                        // Load product to get its current standardized price
                        $product = Product::findOrFail($productId);
                        if ($conversionFactor != 1 && $unitName) {
                            $productUnit = $product->units()->where('unit_name', $unitName)->first();
                            $unitPrice = $productUnit ? $productUnit->sale_price : ($product->sale_price * $conversionFactor);
                        } else {
                            $unitPrice = $product->sale_price;
                        }
                    }
                    $batchPrice = $unitPrice / $conversionFactor;
                    
                    // Deduct from batch
                    $batch->decrement('quantity', $sellFromBatch);
                    $batch->decrement('remaining_quantity', $sellFromBatch);

                    // Create SaleItem
                    $saleItem = SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $productId,
                        'batch_id' => $batch->id,
                        'quantity' => $sellFromBatch,
                        'unit_name' => $unitName,
                        'conversion_factor' => $conversionFactor,
                        'price' => $batchPrice,
                        'total' => $sellFromBatch * $batchPrice,
                    ]);

                    // Generate warranty if applicable
                    $product = Product::findOrFail($productId);
                    if ($product->has_warranty) {
                        Warranty::create([
                            'sale_id' => $sale->id,
                            'sale_item_id' => $saleItem->id,
                            'product_id' => $productId,
                            'customer_id' => $sale->customer_id,
                            'branch_id' => $branchId,
                            'serial_number' => $itemData['serial_number'] ?? null,
                            'warranty_start_date' => now(),
                            'warranty_end_date' => now()->addMonths((int)$product->warranty_period_months),
                            'warranty_period_months' => $product->warranty_period_months,
                            'status' => 'ACTIVE',
                        ]);
                    }

                    // Log Stock Movement
                    StockMovement::create([
                        'product_id' => $productId,
                        'branch_id' => $branchId,
                        'batch_id' => $batch->id,
                        'type' => 'out',
                        'quantity' => $sellFromBatch,
                        'reference_id' => $sale->id,
                        'reference_type' => Sale::class,
                        'note' => 'Sale: ' . $sale->invoice_number,
                        'created_by' => $userId,
                    ]);

                    $remainingToSell -= $sellFromBatch;
                }

                if ($remainingToSell > 0) {
                    $prodBarcode = $product->barcode ?: 'N/A';
                    $errMsg = app()->getLocale() == 'ar' 
                        ? "مخزون غير كاف للمنتج: '{$product->name}' (الرمز: {$prodBarcode}). العجز: {$remainingToSell}"
                        : "Insufficient stock for product: '{$product->name}' (Barcode: {$prodBarcode}). Shortfall: {$remainingToSell}";
                    throw new \Exception($errMsg);
                }
            }

            // 3. Update Sale Totals based on actual batch prices
            $actualSubtotal = $sale->items()->sum('total');
            $actualTax = $actualSubtotal * ($request->tax / ($request->subtotal ?: 1)); // Pro-rate tax
            $sale->update([
                'subtotal' => $actualSubtotal,
                'tax' => $actualTax,
                'total' => $actualSubtotal + $actualTax - $sale->discount
            ]);

            // 4. Create Payment Record
            SalePayment::create([
                'sale_id' => $sale->id,
                'payment_method' => $request->payment_method,
                'amount' => $request->paid_amount,
                'reference_number' => $request->payment_reference,
            ]);

            // 4. Increment Customer Visit Count
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    $customer->incrementVisit();
                }
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => __('pos.sale_created_successfully'),
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function show($id)
    {
        $sale = Sale::with(['customer', 'user', 'branch', 'items.product', 'payments', 'returns.creator'])
            ->findOrFail($id);
            
        $sale = $this->calculateReturnData($sale);
            
        return view('sales.show', compact('sale'));
    }

    public function print($id)
    {
        $sale = Sale::with(['customer', 'user', 'branch', 'items.product', 'payments', 'returns.creator'])
            ->findOrFail($id);
            
        $sale = $this->calculateReturnData($sale);
            
        return view('sales.print', compact('sale'));
    }

    public function downloadPdf($id)
    {
        $sale = Sale::with(['customer', 'user', 'branch', 'items.product', 'payments', 'returns.creator'])
            ->findOrFail($id);
            
        $sale = $this->calculateReturnData($sale);
        $setting = \App\Models\Setting::first();

        $pdf = \PDF::loadView('sales.pdf', compact('sale', 'setting'));

        return $pdf->download('invoice-' . $sale->invoice_number . '.pdf');
    }

    private function calculateReturnData($sale)
    {
        $totalReturnedSubtotal = 0;
        $totalSoldQty = 0;
        $totalReturnedQty = 0;

        foreach ($sale->items as $item) {
            $item->returned_qty = $sale->returns
                ->where('product_id', $item->product_id)
                ->where('batch_id', $item->batch_id)
                ->sum('quantity');
            
            $item->net_qty = $item->quantity - $item->returned_qty;
            $item->returned_total = $item->returned_qty * $item->price;
            $item->net_total = $item->net_qty * $item->price;
            
            $totalReturnedSubtotal += $item->returned_total;
            $totalSoldQty += $item->quantity;
            $totalReturnedQty += $item->returned_qty;
        }

        $sale->returned_subtotal = $totalReturnedSubtotal;
        $sale->net_subtotal = $sale->subtotal - $totalReturnedSubtotal;
        
        // Dynamic tax calculation on net subtotal if needed, 
        // but it's better to keep original proportional tax
        $taxRate = $sale->subtotal > 0 ? ($sale->tax / $sale->subtotal) : 0.15;
        $sale->returned_tax = $totalReturnedSubtotal * $taxRate;
        $sale->net_tax = $sale->net_subtotal * $taxRate;
        
        $sale->net_total = $sale->net_subtotal + $sale->net_tax - $sale->discount;

        if ($totalReturnedQty <= 0) {
            $sale->return_status = 'completed';
        } elseif ($totalReturnedQty >= $totalSoldQty) {
            $sale->return_status = 'fully_returned';
        } else {
            $sale->return_status = 'partially_returned';
        }

        return $sale;
    }

    public function addPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
        ]);

        $sale = Sale::findOrFail($id);
        $remaining = round($sale->remaining, 2);
        $amount = round((float)$request->amount, 2);

        if ($amount > ($remaining + 0.01)) { // Small buffer for precision
            return response()->json([
                'success' => false,
                'message' => 'Payment amount (' . number_format($amount, 2) . ') cannot exceed remaining balance (' . number_format($remaining, 2) . ')'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create Payment
            SalePayment::create([
                'sale_id' => $sale->id,
                'payment_method' => $request->payment_method,
                'amount' => $request->amount,
                'reference_number' => $request->reference_number,
            ]);

            // Update Sale
            $sale->paid_amount += $request->amount;
            $sale->status = $sale->paid_amount >= $sale->total ? 'paid' : 'partial';
            $sale->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('pos.payment_recorded_successfully')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $sale = Sale::with('items')->findOrFail($id);
        $branchId = session('branch_id');
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            // Restore stock for each item
            foreach ($sale->items as $item) {
                if ($item->batch_id) {
                    $batch = Batch::find($item->batch_id);
                    if ($batch) {
                        $batch->increment('quantity', $item->quantity);
                        $batch->increment('remaining_quantity', $item->quantity);
                        
                        // Log Stock Movement (In)
                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'branch_id' => $branchId,
                            'batch_id' => $item->batch_id,
                            'type' => 'in',
                            'quantity' => $item->quantity,
                            'reference_id' => $sale->id,
                            'reference_type' => Sale::class,
                            'note' => 'Sale Deleted: ' . $sale->invoice_number,
                            'created_by' => $userId,
                        ]);
                    }
                }
            }

            // Related records will be deleted via cascading (if configured in DB)
            // But let's be explicit for models that don't have DB-level cascade
            $sale->delete();

            DB::commit();

            return redirect()->route('sales.index')->with('success', __('pos.sale_deleted_successfully') ?? 'Sale deleted successfully and stock restored.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale Deletion Error: ' . $e->getMessage());
            return redirect()->route('sales.index')->with('error', 'Error deleting sale: ' . $e->getMessage());
        }
    }
}

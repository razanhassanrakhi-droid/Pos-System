<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = session('branch_id');
        $search = $request->input('search');

        $query = Purchase::with(['supplier', 'branch', 'user']);

        // Filter by branch if selected
        $query->when($branchId, fn($q) => $q->where('branch_id', $branchId));

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
                  ->orWhere('created_at', 'LIKE', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $purchases = $query->latest()->paginate(10)->withQueryString();

        return view('purchases.index', compact('purchases', 'search'));
    }

    public function create()
    {
        $user = Auth::user();
        $branchId = session('branch_id');

        if (!$branchId) {
            return redirect()->back()->with('error', __('pos.no_branch_selected'));
        }

        $suppliers = Supplier::where('branch_id', $branchId)->get();

        $products = Product::with('units')->where('branch_id', $branchId)->where('is_active', true)->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'nullable|string|unique:purchases,invoice_number',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_name' => 'nullable|string|max:255',
            'items.*.conversion_factor' => 'nullable|numeric|min:0.0001',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.expiry_date' => 'nullable|date',
            'discount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user();
        $branchId = session('branch_id') ?: ($user->branches()->first()?->id ?: Branch::value('id'));

        if (!$branchId) {
            return redirect()->back()->with('error', __('pos.no_branch_selected'));
        }

        try {
            DB::beginTransaction();

            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['purchase_price'];
            }

            $discount = $request->discount ?? 0;
            $shippingCost = $request->shipping_cost ?? 0;
            $taxRate = $request->tax_rate ?? 0;
            $taxAmount = $request->tax_amount ?? 0;
            $totalAmount = $subtotal - $discount + $shippingCost + $taxAmount;
            $paidAmount = $request->paid_amount;
            $remainingAmount = $totalAmount - $paidAmount;

            $purchase = Purchase::create([
                'invoice_number' => $request->invoice_number,
                'supplier_id' => $request->supplier_id,
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'created_at' => $request->date,
            ]);

            if ($paidAmount > 0) {
                \App\Models\PurchasePayment::create([
                    'purchase_id' => $purchase->id,
                    'payment_method' => $request->payment_method,
                    'amount' => $paidAmount,
                    'reference_number' => null,
                    'created_at' => $request->date,
                ]);
            }

            foreach ($request->items as $itemData) {
                $itemTotal = $itemData['quantity'] * $itemData['purchase_price'];
                $conversionFactor = (float)($itemData['conversion_factor'] ?? 1);
                $unitName = $itemData['unit_name'] ?? null;
                $quantityBase = $itemData['quantity'] * $conversionFactor;
                $basePurchasePrice = $itemData['purchase_price'] / $conversionFactor;
                
                // Create new batch automatically (always create a new batch, never merge)
                $product = Product::findOrFail($itemData['product_id']);
                $batch = Batch::create([
                    'product_id' => $itemData['product_id'],
                    'branch_id' => $branchId,
                    'batch_number' => (!empty($itemData['batch_number'])) ? $itemData['batch_number'] : Batch::generateBatchNumber(),
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'quantity' => $quantityBase,
                    'purchase_unit' => $unitName,
                    'purchased_quantity' => $itemData['quantity'],
                    'conversion_factor' => $conversionFactor,
                    'converted_quantity' => $quantityBase,
                    'remaining_quantity' => $quantityBase,
                    'purchase_price' => $basePurchasePrice,
                    'sale_price' => $product->sale_price, // Use centralized product sale price
                    'status' => 'Active',
                ]);

                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $itemData['product_id'],
                    'batch_id' => $batch->id,
                    'quantity' => $quantityBase,
                    'unit_name' => $unitName,
                    'conversion_factor' => $conversionFactor,
                    'purchase_price' => $basePurchasePrice,
                    'expiry_date' => $itemData['expiry_date'] ?? $batch->expiry_date,
                    'total' => $itemTotal,
                ]);

                $batch->update(['purchase_item_id' => $purchaseItem->id]);

                // Record Stock Movement
                StockMovement::create([
                    'product_id' => $itemData['product_id'],
                    'branch_id' => $branchId,
                    'batch_id' => $batch->id,
                    'type' => 'in',
                    'quantity' => $quantityBase,
                    'reference_id' => $purchase->id,
                    'reference_type' => Purchase::class,
                    'note' => __('purchases.purchase_invoice') . ': ' . $purchase->invoice_number,
                    'created_by' => $user->id,
                ]);

                // (Logic removed: no longer overriding global product price to allow batch variance)
                // $product = Product::find($itemData['product_id']);
                // $updateData = ['purchase_price' => $itemData['purchase_price']];
                // if ($itemData['expiry_date']) {
                //     $updateData['expiry_date'] = $itemData['expiry_date'];
                // }
                // $product->update($updateData);
            }

            // Dispatch purchase notifications
            \App\Services\NotificationService::send(
                'Purchases',
                'purchase_created',
                'Activity',
                'تم إنشاء فاتورة شراء',
                'Purchase Created',
                'تم إنشاء فاتورة الشراء رقم "' . $purchase->invoice_number . '".',
                'Purchase invoice "' . $purchase->invoice_number . '" was created.',
                Purchase::class,
                $purchase->id,
                $branchId,
                $user->id
            );

            \App\Services\NotificationService::send(
                'Purchases',
                'purchase_received',
                'Important',
                'تم استلام المشتريات',
                'Purchase Received',
                'تم استلام الدفعة الخاصة بفاتورة الشراء رقم "' . $purchase->invoice_number . '" وإدخالها للمستودع.',
                'Items for purchase invoice "' . $purchase->invoice_number . '" have been received into inventory.',
                Purchase::class,
                $purchase->id,
                $branchId,
                $user->id
            );

            if ($purchase->remaining_amount > 0) {
                \App\Services\NotificationService::send(
                    'Purchases',
                    'supplier_balance_due',
                    'Important',
                    'مستحقات للمورد معلقة',
                    'Supplier Balance Due',
                    'مستحقات معلقة للمورد "' . ($purchase->supplier->name ?? '') . '" بقيمة ' . number_format($purchase->remaining_amount, 2) . '.',
                    'Outstanding balance of ' . number_format($purchase->remaining_amount, 2) . ' is due for supplier "' . ($purchase->supplier->name ?? '') . '".',
                    Purchase::class,
                    $purchase->id,
                    $branchId,
                    $user->id
                );
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', __('purchases.purchase_created_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'branch', 'user', 'items.product'])->findOrFail($id);
        
        // Authorization check
        if (!Auth::user()->isAdmin() && $purchase->branch_id != session('branch_id')) {
            abort(403);
        }

        return view('purchases.show', compact('purchase'));
    }

    public function destroy($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        // Authorization check
        if (!Auth::user()->isAdmin() && $purchase->branch_id != session('branch_id')) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            foreach ($purchase->items as $item) {
                if ($item->batch_id) {
                    $batch = Batch::find($item->batch_id);
                    if ($batch) {
                        $batch->decrement('quantity', $item->quantity);
                        $batch->decrement('remaining_quantity', $item->quantity);
                    }
                }
            }

            // Delete associated stock movements
            StockMovement::where('reference_id', $purchase->id)
                ->where('reference_type', Purchase::class)
                ->delete();

            // Delete purchase items (assuming database cascade is not set up or to be safe)
            $purchase->items()->delete();

            // Delete the purchase itself
            $purchase->delete();

            DB::commit();
            return redirect()->route('purchases.index')->with('success', __('pos.deleted_successfully') ?? 'Purchase deleted and stock reversed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting purchase: ' . $e->getMessage());
        }
    }

    public function downloadPdf($id)
    {
        $purchase = Purchase::with(['supplier', 'branch', 'user', 'items.product'])->findOrFail($id);

        // Authorization check
        if (!Auth::user()->isAdmin() && $purchase->branch_id != session('branch_id')) {
            abort(403);
        }

        $setting = \App\Models\Setting::first();

        $pdf = \PDF::loadView('purchases.pdf', compact('purchase', 'setting'));

        return $pdf->download('purchase-invoice-' . $purchase->invoice_number . '.pdf');
    }

    public function print($id)
    {
        $purchase = Purchase::with(['supplier', 'branch', 'user', 'items.product'])->findOrFail($id);

        // Authorization check
        if (!Auth::user()->isAdmin() && $purchase->branch_id != session('branch_id')) {
            abort(403);
        }

        return view('purchases.print', compact('purchase'));
    }

    public function addPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
        ]);

        $purchase = Purchase::findOrFail($id);
        $remaining = round($purchase->remaining_amount, 2);
        $amount = round((float)$request->amount, 2);

        if ($amount > ($remaining + 0.01)) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() == 'ar' 
                    ? 'قيمة الدفعة (' . number_format($amount, 2) . ') لا يمكن أن تتجاوز الرصيد المتبقي (' . number_format($remaining, 2) . ')' 
                    : 'Payment amount (' . number_format($amount, 2) . ') cannot exceed remaining balance (' . number_format($remaining, 2) . ')'
            ], 422);
        }

        DB::beginTransaction();
        try {
            \App\Models\PurchasePayment::create([
                'purchase_id' => $purchase->id,
                'payment_method' => $request->payment_method,
                'amount' => $request->amount,
                'reference_number' => $request->reference_number,
            ]);

            $purchase->paid_amount += $request->amount;
            $purchase->remaining_amount = $purchase->total_amount - $purchase->paid_amount;
            $purchase->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() == 'ar' ? 'تم تسجيل الدفعة بنجاح.' : 'Payment recorded successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

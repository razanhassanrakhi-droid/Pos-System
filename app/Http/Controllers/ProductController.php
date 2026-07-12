<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\StockMovement;
use App\Models\ProductBatch;
use App\Models\Batch;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        // Get current branch from session (set by BranchMiddleware)
        $branchId = session('branch_id');
        $statusFilter = $request->input('status');

        // Always filter products strictly by the current branch_id, unless it's NULL (All Branches view for Admin)
        $products = Product::with(['units', 'creator', 'updater', 'batches'])->when($branchId, function($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })->when($statusFilter, function($query) use ($statusFilter) {
            return $query->where('status', $statusFilter);
        })
        ->withSum(['batches as current_stock' => function($query) use ($branchId) {
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
        }], 'remaining_quantity')
        ->latest()->get();

        // Attach current branch stock to each product
        foreach ($products as $product) {
            $product->branch_id = $branchId;
        }

        $categories = \App\Models\Category::all();
        $setting = \App\Models\Setting::first();
        $units = \App\Models\Unit::where('branch_id', $branchId)->pluck('name')->unique()->values();

        return view('products.index', compact('products', 'branchId', 'categories', 'setting', 'units', 'statusFilter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required_without:name_en|string|max:255|nullable',
            'name_en' => 'required_without:name_ar|string|max:255|nullable',
            'brand_ar' => 'nullable|string|max:255',
            'brand_en' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'barcode' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Active,Inactive',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'sale_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'base_unit_name_ar' => 'nullable|string|max:255',
            'base_unit_name_en' => 'nullable|string|max:255',
            'additional_units' => 'nullable|array',
            'additional_units.*.unit_name_ar' => 'nullable|string|max:255',
            'additional_units.*.unit_name_en' => 'nullable|string|max:255',
            'additional_units.*.barcode' => 'nullable|string|max:255',
            'additional_units.*.conversion_factor' => 'required|numeric|min:0.0001',
            'additional_units.*.sale_price' => 'required|numeric|min:0',
            'additional_units.*.pricing_mode' => 'nullable|string|in:automatic,custom',
        ]);

        $user = auth()->user();
        $branchId = session('branch_id') ?: ($user->branches()->first()?->id ?: \App\Models\Branch::value('id'));

        if (!$branchId) {
            return redirect()->back()->with('error', __('pos.no_branch_assigned'));
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $productStatus = $request->status ?? 'Active';

        $nameAr = $request->name_ar ?? $request->name_en;
        $nameEn = $request->name_en ?? $request->name_ar;
        $brandAr = $request->brand_ar ?? $request->brand_en;
        $brandEn = $request->brand_en ?? $request->brand_ar;
        $descAr = $request->description_ar ?? $request->description_en;
        $descEn = $request->description_en ?? $request->description_ar;
        $baseUnitAr = trim($request->base_unit_name_ar ?? '');
        $baseUnitEn = trim($request->base_unit_name_en ?? '');
        if (empty($baseUnitAr) && !empty($baseUnitEn)) {
            $baseUnitAr = $baseUnitEn;
        }
        if (!empty($baseUnitAr) && empty($baseUnitEn)) {
            $baseUnitEn = $baseUnitAr;
        }

        $product = Product::create([
            'branch_id' => $branchId,
            'name' => ['ar' => $nameAr, 'en' => $nameEn],
            'brand' => ['ar' => $brandAr, 'en' => $brandEn],
            'description' => ['ar' => $descAr, 'en' => $descEn],
            'category_id' => $request->category_id,
            'barcode' => $request->barcode,
            'sku' => $request->sku,
            'base_unit_name_ar' => $baseUnitAr,
            'base_unit_name_en' => $baseUnitEn,
            'image' => $imagePath,
            'sale_price' => $request->sale_price,
            'minimum_stock' => $request->minimum_stock,
            'status' => $productStatus,
            'is_active' => $productStatus === 'Active',
            'has_warranty' => $request->has('has_warranty'),
            'warranty_period_months' => $request->warranty_period_months ?? 0,
            'warranty_type' => $request->warranty_type,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Auto create base unit suggestion
        if ($baseUnitAr) {
            \App\Models\Unit::firstOrCreate([
                'branch_id' => $branchId,
                'name' => trim($baseUnitAr),
            ]);
        }
        if ($baseUnitEn) {
            \App\Models\Unit::firstOrCreate([
                'branch_id' => $branchId,
                'name' => trim($baseUnitEn),
            ]);
        }

        // Additional selling units
        if ($request->has('additional_units')) {
            foreach ($request->additional_units as $unitData) {
                $unitNameAr = trim($unitData['unit_name_ar'] ?? '');
                $unitNameEn = trim($unitData['unit_name_en'] ?? '');
                
                if (empty($unitNameAr) && !empty($unitNameEn)) {
                    $unitNameAr = $unitNameEn;
                }
                if (!empty($unitNameAr) && empty($unitNameEn)) {
                    $unitNameEn = $unitNameAr;
                }
                
                if (empty($unitNameAr) && empty($unitNameEn)) {
                    continue; // Skip if no name provided
                }
                
                $baseUnitArLower = strtolower(trim($baseUnitAr));
                $baseUnitEnLower = strtolower(trim($baseUnitEn));
                if (($baseUnitArLower && strtolower($unitNameAr) === $baseUnitArLower) || 
                    ($baseUnitEnLower && strtolower($unitNameEn) === $baseUnitEnLower)) {
                    continue; // Skip duplicate of base unit
                }
                
                $pricingMode = $unitData['pricing_mode'] ?? 'custom';
                $salePrice = $unitData['sale_price'];
                if ($pricingMode === 'automatic') {
                    $salePrice = $request->sale_price * $unitData['conversion_factor'];
                }
                $product->units()->create([
                    'unit_name_ar' => $unitNameAr ?: null,
                    'unit_name_en' => $unitNameEn ?: null,
                    'barcode' => isset($unitData['barcode']) ? trim($unitData['barcode']) : null,
                    'conversion_factor' => $unitData['conversion_factor'],
                    'sale_price' => $salePrice,
                    'pricing_mode' => $pricingMode,
                ]);
                
                if ($unitNameAr) \App\Models\Unit::firstOrCreate(['branch_id' => $branchId, 'name' => $unitNameAr]);
                if ($unitNameEn) \App\Models\Unit::firstOrCreate(['branch_id' => $branchId, 'name' => $unitNameEn]);
            }
        }

        return redirect()->route('products.index')->with('success', trans('product.added_successfully'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name_ar' => 'required_without:name_en|string|max:255|nullable',
            'name_en' => 'required_without:name_ar|string|max:255|nullable',
            'brand_ar' => 'nullable|string|max:255',
            'brand_en' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'barcode' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Active,Inactive',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sale_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'has_warranty' => 'boolean',
            'warranty_period_months' => 'nullable|integer|min:0',
            'warranty_type' => 'nullable|string|max:255',
            'base_unit_name_ar' => 'nullable|string|max:255',
            'base_unit_name_en' => 'nullable|string|max:255',
            'additional_units' => 'nullable|array',
            'additional_units.*.unit_name_ar' => 'nullable|string|max:255',
            'additional_units.*.unit_name_en' => 'nullable|string|max:255',
            'additional_units.*.barcode' => 'nullable|string|max:255',
            'additional_units.*.conversion_factor' => 'required|numeric|min:0.0001',
            'additional_units.*.sale_price' => 'required|numeric|min:0',
            'additional_units.*.pricing_mode' => 'nullable|string|in:automatic,custom',
        ]);

        $nameAr = $request->name_ar ?? $request->name_en;
        $nameEn = $request->name_en ?? $request->name_ar;
        $brandAr = $request->brand_ar ?? $request->brand_en;
        $brandEn = $request->brand_en ?? $request->brand_ar;
        $descAr = $request->description_ar ?? $request->description_en;
        $descEn = $request->description_en ?? $request->description_ar;
        $baseUnitAr = trim($request->base_unit_name_ar ?? '');
        $baseUnitEn = trim($request->base_unit_name_en ?? '');
        if (empty($baseUnitAr) && !empty($baseUnitEn)) {
            $baseUnitAr = $baseUnitEn;
        }
        if (!empty($baseUnitAr) && empty($baseUnitEn)) {
            $baseUnitEn = $baseUnitAr;
        }

        $data = [
            'name' => ['ar' => $nameAr, 'en' => $nameEn],
            'brand' => ['ar' => $brandAr, 'en' => $brandEn],
            'description' => ['ar' => $descAr, 'en' => $descEn],
            'category_id' => $request->category_id,
            'barcode' => $request->barcode,
            'sku' => $request->sku,
            'status' => $request->status ?? 'Active',
            'is_active' => ($request->status ?? 'Active') === 'Active',
            'sale_price' => $request->sale_price,
            'minimum_stock' => $request->minimum_stock,
            'base_unit_name_ar' => $baseUnitAr,
            'base_unit_name_en' => $baseUnitEn,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['has_warranty'] = $request->has('has_warranty');
        $data['warranty_period_months'] = $request->warranty_period_months ?? 0;
        $data['warranty_type'] = $request->warranty_type;

        $product->update($data);

        $branchId = session('branch_id') ?: auth()->user()->branches()->first()?->id;

        // Auto create base unit suggestion
        if ($baseUnitAr) {
            \App\Models\Unit::firstOrCreate([
                'branch_id' => $branchId,
                'name' => trim($baseUnitAr),
            ]);
        }
        if ($baseUnitEn) {
            \App\Models\Unit::firstOrCreate([
                'branch_id' => $branchId,
                'name' => trim($baseUnitEn),
            ]);
        }

        // Recreate additional selling units
        $product->units()->delete();
        if ($request->has('additional_units')) {
            foreach ($request->additional_units as $unitData) {
                $unitNameAr = trim($unitData['unit_name_ar'] ?? '');
                $unitNameEn = trim($unitData['unit_name_en'] ?? '');
                
                if (empty($unitNameAr) && !empty($unitNameEn)) {
                    $unitNameAr = $unitNameEn;
                }
                if (!empty($unitNameAr) && empty($unitNameEn)) {
                    $unitNameEn = $unitNameAr;
                }
                
                if (empty($unitNameAr) && empty($unitNameEn)) {
                    continue; // Skip if no name provided
                }

                $baseUnitArLower = strtolower(trim($baseUnitAr));
                $baseUnitEnLower = strtolower(trim($baseUnitEn));
                if (($baseUnitArLower && strtolower($unitNameAr) === $baseUnitArLower) || 
                    ($baseUnitEnLower && strtolower($unitNameEn) === $baseUnitEnLower)) {
                    continue; // Skip duplicate of base unit
                }
                
                $pricingMode = $unitData['pricing_mode'] ?? 'custom';
                $salePrice = $unitData['sale_price'];
                if ($pricingMode === 'automatic') {
                    $salePrice = $request->sale_price * $unitData['conversion_factor'];
                }
                $product->units()->create([
                    'unit_name_ar' => $unitNameAr ?: null,
                    'unit_name_en' => $unitNameEn ?: null,
                    'barcode' => isset($unitData['barcode']) ? trim($unitData['barcode']) : null,
                    'conversion_factor' => $unitData['conversion_factor'],
                    'sale_price' => $salePrice,
                    'pricing_mode' => $pricingMode,
                ]);
                
                if ($unitNameAr) \App\Models\Unit::firstOrCreate(['branch_id' => $branchId, 'name' => $unitNameAr]);
                if ($unitNameEn) \App\Models\Unit::firstOrCreate(['branch_id' => $branchId, 'name' => $unitNameEn]);
            }
        }

        return redirect()->route('products.index')->with('success', trans('product.updated_successfully'));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', trans('product.deleted_successfully'));
    }

    public function movements(Product $product)
    {
        try {
            $user = auth()->user();
            $branchId = session('branch_id');
            
            $movements = $product->movements()
                ->when($branchId, function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                })
                ->with(['creator', 'branch'])
                ->latest()
                ->get();

            return response()->json([
                'movements' => $movements,
                'metadata' => [
                    'created_at' => $product->created_at->toDateTimeString(),
                    'created_by' => $product->creator ? ($product->creator->full_name ?: $product->creator->username) : 'Unknown',
                    'updated_at' => $product->updated_at->toDateTimeString(),
                    'updated_by' => $product->updater ? ($product->updater->full_name ?: $product->updater->username) : 'Unknown',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['movements' => [], 'metadata' => []], 200);
        }
    }
    public function getByBarcode($barcode)
    {
        $branchId = session('branch_id');
        $barcode = trim($barcode);
        
        // 1. Try EXACT match first (Barcode, SKU, or Unit Barcode)
        $product = Product::with(['category', 'units'])
            ->where('branch_id', $branchId)
            ->where(function($q) use ($barcode) {
                $q->where('barcode', $barcode)
                  ->orWhere('sku', $barcode)
                  ->orWhereHas('units', function($uq) use ($barcode) {
                      $uq->where('barcode', $barcode);
                  });
            })
            ->first();



        if (!$product) {
            return response()->json(['message' => __('product.not_found')], 404);
        }

        // Determine if specific unit was scanned
        $scannedUnitId = 'base';
        if (trim($product->barcode) !== trim($barcode)) {
            $matchedUnit = $product->units->first(function($u) use ($barcode) {
                return $u->barcode && strcasecmp(trim($u->barcode), trim($barcode)) === 0;
            });
            if ($matchedUnit) {
                $scannedUnitId = $matchedUnit->id;
            }
        }

        $batches = Batch::where('product_id', $product->id)
            ->where('branch_id', $branchId)
            ->where('quantity', '>', 0)
            ->orderByRaw('expiry_date IS NULL ASC')
            ->orderBy('expiry_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($batch) use ($product) {
                return [
                    'id'    => $batch->id,
                    'qty'   => (float) $batch->quantity,
                    'price' => (float) ($product->sale_price ?? 0),
                ];
            });

        return response()->json([
            'product' => $product,
            'batches' => $batches,
            'name' => (string) $product->name,
            'base_unit_name' => $product->base_unit_name ?: 'Piece',
            'additional_units' => $product->units,
            'scanned_unit_id' => $scannedUnitId,
        ]);
    }

    public function getBatches($productId)
    {
        $branchId = session('branch_id');
        $product = Product::find($productId);
        $batches = Batch::with('purchaseItem')->where('product_id', $productId)
            ->when($branchId, function($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->where('quantity', '>', 0)
            ->orderByRaw('expiry_date IS NULL ASC')
            ->orderBy('expiry_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($batch) use ($product) {
                // Ensure prices reflect centralized product prices if not set on batch (effectively always using product sale price now)
                $batch->purchase_price = $batch->purchase_price ?? ($product->purchase_price ?? 0);
                $batch->sale_price = (float)($product->sale_price ?? 0);
                
                if ($batch->purchaseItem) {
                    $factor = (float)($batch->purchaseItem->conversion_factor ?: 1);
                    $batch->original_quantity_display = (float)($batch->purchaseItem->quantity / $factor);
                    $batch->purchase_unit_name = $batch->purchaseItem->unit_name ?: ($product->base_unit_name ?: 'Piece');
                } else {
                    $batch->original_quantity_display = (float)$batch->quantity;
                    $batch->purchase_unit_name = $product->base_unit_name ?: 'Piece';
                    $factor = 1.0;
                }
                $batch->base_unit_name = $product->base_unit_name ?: 'Piece';
                $batch->conversion_factor = $factor;
                return $batch;
            });

        return response()->json($batches);
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'name_ar' => 'required_without:name_en|string|max:255|nullable',
            'name_en' => 'required_without:name_ar|string|max:255|nullable',
            'barcode' => 'required|string|max:255|unique:products,barcode',
            'category_id' => 'required|exists:categories,id',
            'sale_price' => 'required|numeric|min:0',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'minimum_stock' => 'nullable|numeric|min:0',
            'base_unit_name_ar' => 'nullable|string|max:255',
            'base_unit_name_en' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $branchId = session('branch_id');

        if (!$branchId) {
            return response()->json(['success' => false, 'message' => __('pos.no_branch_selected')], 422);
        }

        $product = Product::create([
            'branch_id' => $branchId,
            'name' => ['ar' => $request->name_ar, 'en' => $request->name_en],
            'description' => ['ar' => $request->description_ar, 'en' => $request->description_en],
            'category_id' => $request->category_id,
            'barcode' => $request->barcode,
            'sale_price' => $request->sale_price,
            'minimum_stock' => $request->minimum_stock ?? 0,
            'base_unit_name_ar' => $request->base_unit_name_ar ?: null,
            'base_unit_name_en' => $request->base_unit_name_en ?: null,
            'status' => 'Active',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        if ($product->base_unit_name_ar) {
            \App\Models\Unit::firstOrCreate([
                'branch_id' => $branchId,
                'name' => trim($product->base_unit_name_ar),
            ]);
        }
        if ($product->base_unit_name_en) {
            \App\Models\Unit::firstOrCreate([
                'branch_id' => $branchId,
                'name' => trim($product->base_unit_name_en),
            ]);
        }

        return response()->json([
            'success' => true,
            'product' => $product,
            'name' => $product->name,
            'price' => (float)$product->sale_price,
            'batches' => [],
            'has_warranty' => (bool)$product->has_warranty,
            'warranty_months' => (int)$product->warranty_period_months,
            'base_unit_name' => $product->base_unit_name ?: 'Piece',
            'additional_units' => [],
        ]);
    }

    public function search(Request $request)
    {
        $term = $request->term;
        $branchId = session('branch_id');
        $forPurchase = $request->boolean('for_purchase');

        $query = Product::where('branch_id', $branchId);
        
        if (!$forPurchase) {
            $query->where('is_active', true)->where('status', 'Active');
        }

        $products = $query->where(function($q) use ($term) {
                $q->where('barcode', 'LIKE', "%$term%")
                    ->orWhere('sku', 'LIKE', "%$term%")
                    ->orWhere('name->ar', 'LIKE', "%$term%")
                    ->orWhere('name->en', 'LIKE', "%$term%")
                    ->orWhere('brand->ar', 'LIKE', "%$term%")
                    ->orWhere('brand->en', 'LIKE', "%$term%")
                    ->orWhereHas('units', function($uq) use ($term) {
                        $uq->where('barcode', 'LIKE', "%$term%");
                    });
            })
            ->with('units')
            ->orderByRaw('barcode = ? DESC', [$term])
            ->limit(10)
            ->get();

        $results = $products->map(function($product) use ($branchId, $term) {
                $name = $product->name;
                if (empty($name)) {
                    $name = json_decode($product->getRawOriginal('name'), true);
                    $name = $name['en'] ?? $name['ar'] ?? "Product #{$product->id}";
                }
                
                $matchedUnitId = 'base';
                $matchedUnitName = '';
                $overridePrice = null;

                if (trim($product->barcode) !== trim($term)) {
                    $matchedUnit = $product->units->first(function($u) use ($term) {
                        return $u->barcode && strcasecmp(trim($u->barcode), trim($term)) === 0;
                    });
                    
                    if ($matchedUnit) {
                        $matchedUnitId = $matchedUnit->id;
                        $matchedUnitName = $matchedUnit->unit_name;
                        $overridePrice = $matchedUnit->sale_price;
                    }
                }

                // Fetch available batches for LIFO price check (latest first) for this branch
                $batches = $product->batches()
                    ->where('branch_id', $branchId)
                    ->where('quantity', '>', 0)
                    ->orderByRaw('expiry_date IS NULL ASC')
                    ->orderBy('expiry_date', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(fn($b) => [
                        'id' => $b->id,
                        'qty' => (float)$b->quantity,
                        'price' => (float)($overridePrice ?? $product->sale_price) // Always use product sale price or overridden unit price
                    ]);

                return [
                    'id' => $product->id,
                    'text' => $name . ($product->barcode ? " ({$product->barcode})" : ""),
                    'barcode' => $product->barcode,
                    'price' => $overridePrice ?? (count($batches) > 0 ? $batches[0]['price'] : (float) ($product->sale_price ?? 0)),
                    'stock' => (float) ($product->currentBranchStock($branchId) ?? 0),
                    'batches' => $batches,
                    'has_warranty' => (bool)$product->has_warranty,
                    'warranty_period_months' => (int)$product->warranty_period_months,
                    'warranty_months' => (int)$product->warranty_period_months,
                    'base_unit_name' => $product->base_unit_name ?: 'Piece',
                    'additional_units' => $product->units,
                    'image' => $product->image,
                    'matched_unit_id' => $matchedUnitId,
                    'matched_unit_name' => $matchedUnitName,
                    'purchase_price' => (float) ($product->purchase_price ?? 0),
                ];
            });

        return response()->json($results);
    }

    public function updateBatch(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'batch_number' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        $batch->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('product.updated_successfully'),
            'batch' => $batch
        ]);
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $status = $request->status;
        $isActive = ($status === 'Active');

        Product::whereIn('id', $request->ids)->update([
            'status' => $status,
            'is_active' => $isActive,
        ]);

        return response()->json([
            'success' => true,
            'message' => trans('product.updated_successfully'),
        ]);
    }

    public function getUnits($productId)
    {
        $product = Product::findOrFail($productId);
        $units = $product->units;
        return response()->json([
            'base_unit_name' => $product->base_unit_name ?: 'Piece',
            'units' => $units
        ]);
    }
}

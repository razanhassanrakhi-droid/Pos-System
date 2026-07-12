<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Dispatch a notification.
     *
     * @param string $category Inventory, Sales, Purchases, Returns, Customers, System, Security, Administration
     * @param string $type e.g., low_stock, out_of_stock, purchase_received, etc.
     * @param string $priority Critical, Important, Activity
     * @param string $titleAr Arabic Title
     * @param string $titleEn English Title
     * @param string $messageAr Arabic Message
     * @param string $messageEn English Message
     * @param string|null $referenceType Class name of referenced model
     * @param int|null $referenceId ID of referenced model
     * @param int|null $branchId Branch ID
     * @param int|null $createdBy User ID
     */
    public static function send($category, $type, $priority, $titleAr, $titleEn, $messageAr, $messageEn, $referenceType = null, $referenceId = null, $branchId = null, $createdBy = null, $productId = null, $batchId = null)
    {
        // 1. Enforce inventory alerts only!
        if (strtolower($category) !== 'inventory') {
            return null;
        }

        // 2. Auto-detect product_id and batch_id from polymorphic reference if not explicitly passed
        if (!$productId && $referenceType === \App\Models\Product::class) {
            $productId = $referenceId;
        }
        if (!$batchId && $referenceType === \App\Models\Batch::class) {
            $batchId = $referenceId;
        }
        if ($batchId && !$productId) {
            $batch = \App\Models\Batch::find($batchId);
            if ($batch) {
                $productId = $batch->product_id;
            }
        }

        // 3. Smart Deduplication: check for existing unresolved notification of the same type/product/batch
        $query = Notification::active()
            ->where('category', 'Inventory')
            ->where('type', $type)
            ->where('branch_id', $branchId);

        if ($batchId) {
            $query->where('batch_id', $batchId);
        } else {
            $query->where('product_id', $productId);
        }

        $existing = $query->first();

        if ($existing) {
            // Update the existing active notification message and reset read status
            $existing->update([
                'title' => [
                    'ar' => $titleAr,
                    'en' => $titleEn
                ],
                'message' => [
                    'ar' => $messageAr,
                    'en' => $messageEn
                ],
                'priority' => $priority,
                'read_status' => false,
                'read_date' => null
            ]);
            return $existing;
        }

        // 4. Create new notification
        return Notification::create([
            'title' => [
                'ar' => $titleAr,
                'en' => $titleEn
            ],
            'message' => [
                'ar' => $messageAr,
                'en' => $messageEn
            ],
            'category' => 'Inventory',
            'type' => $type,
            'priority' => $priority,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'read_status' => false,
            'branch_id' => $branchId,
            'created_by' => $createdBy ?? auth()->id(),
            'product_id' => $productId,
            'batch_id' => $batchId,
            'created_by_system' => true
        ]);
    }

    /**
     * Check active batches for expired or expiring soon statuses and generate notifications.
     */
    public static function checkExpiries($branchId = null)
    {
        $now = \Carbon\Carbon::now()->startOfDay();
        
        // Auto-resolve notifications for batches that no longer have remaining quantity
        $emptyBatchIds = \App\Models\Batch::where('remaining_quantity', '<=', 0)->pluck('id');
        if ($emptyBatchIds->isNotEmpty()) {
            Notification::where('category', 'Inventory')
                ->whereIn('type', ['expired', 'expiring_soon'])
                ->whereIn('batch_id', $emptyBatchIds)
                ->delete();
        }
        
        // 1. Check Expired Batches
        $expiredBatches = \App\Models\Batch::with('product')->where('remaining_quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', $now)
            ->when($branchId, function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->whereHas('product', fn($pq) => $pq->where('branch_id', $branchId));
            })
            ->get();
            
        $expiredBatchIds = $expiredBatches->pluck('id');
        if ($expiredBatchIds->isNotEmpty()) {
            // Delete 'expiring_soon' since they are now 'expired'
            Notification::where('category', 'Inventory')
                ->where('type', 'expiring_soon')
                ->whereIn('batch_id', $expiredBatchIds)
                ->delete();
        }
            
        foreach ($expiredBatches as $batch) {
            $exists = Notification::where('category', 'Inventory')
                ->where('type', 'expired')
                ->where('batch_id', $batch->id)
                ->exists();
                
            if (!$exists && $batch->product) {
                self::send(
                    'Inventory',
                    'expired',
                    'Critical',
                    'منتهي الصلاحية',
                    'Product Expired',
                    'انتهت صلاحية المنتج "' . $batch->product->name . '" (الدفعة: ' . $batch->batch_number . ') بتاريخ ' . $batch->expiry_date->format('Y-m-d') . '.',
                    'Product "' . $batch->product->name . '" (Batch: ' . $batch->batch_number . ') expired on ' . $batch->expiry_date->format('Y-m-d') . '.',
                    \App\Models\Batch::class,
                    $batch->id,
                    $batch->branch_id,
                    null,
                    $batch->product_id,
                    $batch->id
                );
            }
        }
        
        // 2. Fetch configurable warning threshold from the admin settings (Default: 30 days)
        $admin = \App\Models\User::where('role', 'admin')->first();
        $settings = $admin ? ($admin->notification_settings ?? []) : [];
        $days = (int) ($settings['inventory']['expiry_warning_period'] ?? 30);
        
        $targetDate = $now->copy()->addDays($days)->endOfDay();
        $expiringBatches = \App\Models\Batch::with('product')->where('remaining_quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', $now)
            ->whereDate('expiry_date', '<=', $targetDate)
            ->when($branchId, function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->whereHas('product', fn($pq) => $pq->where('branch_id', $branchId));
            })
            ->get();
            
        foreach ($expiringBatches as $batch) {
            $daysLeft = (int) round($now->diffInDays($batch->expiry_date, false));
            if ($daysLeft <= 0 || $daysLeft > $days) continue;
            
            $exists = Notification::where('category', 'Inventory')
                ->where('type', 'expiring_soon')
                ->where('batch_id', $batch->id)
                ->exists();
                
            if (!$exists && $batch->product) {
                self::send(
                    'Inventory',
                    'expiring_soon',
                    'Important', // Warning level
                    'يقترب من الانتهاء',
                    'Expiring Soon',
                    'المنتج "' . $batch->product->name . '" (الدفعة: ' . $batch->batch_number . ') ينتهي خلال ' . $daysLeft . ' يومًا.',
                    'Product "' . $batch->product->name . '" (Batch: ' . $batch->batch_number . ') expires in ' . $daysLeft . ' days.',
                    \App\Models\Batch::class,
                    $batch->id,
                    $batch->branch_id,
                    null,
                    $batch->product_id,
                    $batch->id
                );
            }
        }
    }

    /**
     * Check active products for low stock or out of stock statuses dynamically.
     */
    public static function checkStockLevels($branchId = null)
    {
        $products = \App\Models\Product::with('batches')->where('is_active', true)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();
        
        foreach ($products as $product) {
            $currentStock = $branchId ? $product->currentBranchStock($branchId) : $product->totalStock();
            $minStock = $product->minimum_stock ?? 0;
            $notifBranchId = $branchId ?? $product->branch_id;
            
            // Check out of stock
            if ($currentStock <= 0) {
                $exists = Notification::where('category', 'Inventory')
                    ->where('type', 'out_of_stock')
                    ->where('product_id', $product->id)
                    ->where('branch_id', $notifBranchId)
                    ->exists();
                    
                if (!$exists) {
                    self::send(
                        'Inventory',
                        'out_of_stock',
                        'Critical',
                        'نفاد المخزون',
                        'Out of Stock',
                        'المنتج "' . $product->name . '" نفد تماماً من المخزون.',
                        'Product "' . $product->name . '" is completely out of stock.',
                        \App\Models\Product::class,
                        $product->id,
                        $notifBranchId,
                        null,
                        $product->id
                    );
                }
                
                // Auto-delete low stock if it's now out of stock
                Notification::where('category', 'Inventory')
                    ->where('type', 'low_stock')
                    ->where('product_id', $product->id)
                    ->where('branch_id', $notifBranchId)
                    ->delete();
            } 
            // Check low stock
            elseif ($currentStock <= $minStock) {
                $exists = Notification::where('category', 'Inventory')
                    ->where('type', 'low_stock')
                    ->where('product_id', $product->id)
                    ->where('branch_id', $notifBranchId)
                    ->exists();
                    
                if (!$exists) {
                    self::send(
                        'Inventory',
                        'low_stock',
                        'Important',
                        'انخفاض المخزون',
                        'Low Stock',
                        'الكمية الحالية للمنتج "' . $product->name . '" هي ' . $currentStock . '، وهي أقل من الحد الأدنى (' . $minStock . ').',
                        'Current quantity for "' . $product->name . '" is ' . $currentStock . ', which is below the minimum (' . $minStock . ').',
                        \App\Models\Product::class,
                        $product->id,
                        $notifBranchId,
                        null,
                        $product->id
                    );
                }
                
                // Auto-delete out of stock if it's now low stock
                Notification::where('category', 'Inventory')
                    ->where('type', 'out_of_stock')
                    ->where('product_id', $product->id)
                    ->where('branch_id', $notifBranchId)
                    ->delete();
            } else {
                // Stock is healthy, delete both low stock and out of stock
                Notification::where('category', 'Inventory')
                    ->whereIn('type', ['low_stock', 'out_of_stock'])
                    ->where('product_id', $product->id)
                    ->where('branch_id', $notifBranchId)
                    ->delete();
            }
        }
    }

    public static function checkWarranties($branchId = null)
    {
        $now = \Carbon\Carbon::now()->startOfDay();
        
        // 1. Check Expired Warranties
        $expiredWarranties = \App\Models\Warranty::with(['product', 'customer'])->where('status', '!=', 'Expired')
            ->where('warranty_end_date', '<', $now)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();
            
        foreach ($expiredWarranties as $warranty) {
            $exists = Notification::active()
                ->where('category', 'System')
                ->where('type', 'warranty_expired')
                ->where('reference_id', $warranty->id)
                ->where('reference_type', \App\Models\Warranty::class)
                ->exists();
                
            if (!$exists) {
                self::send(
                    'System',
                    'warranty_expired',
                    'Important',
                    'ضمان منتهي',
                    'Warranty Expired',
                    'انتهت فترة الضمان للمنتج "' . ($warranty->product->name ?? '') . '" للعميل ' . ($warranty->customer->name ?? ''),
                    'Warranty for product "' . ($warranty->product->name ?? '') . '" expired for customer ' . ($warranty->customer->name ?? ''),
                    \App\Models\Warranty::class,
                    $warranty->id,
                    $warranty->branch_id
                );
            }
        }
        
        // 2. Check Expiring Soon (30 days)
        $targetDate = $now->copy()->addDays(30)->endOfDay();
        $expiringWarranties = \App\Models\Warranty::with(['product', 'customer'])->whereNotIn('status', ['Expired', 'Cancelled', 'Completed'])
            ->whereBetween('warranty_end_date', [$now, $targetDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();
            
        foreach ($expiringWarranties as $warranty) {
            $exists = Notification::active()
                ->where('category', 'System')
                ->where('type', 'warranty_expiring_soon')
                ->where('reference_id', $warranty->id)
                ->where('reference_type', \App\Models\Warranty::class)
                ->exists();
                
            if (!$exists) {
                $daysLeft = (int) round($now->diffInDays($warranty->warranty_end_date, false));
                self::send(
                    'System',
                    'warranty_expiring_soon',
                    'Activity',
                    'ضمان يقترب من الانتهاء',
                    'Warranty Expiring Soon',
                    'ينتهي ضمان المنتج "' . ($warranty->product->name ?? '') . '" للعميل ' . ($warranty->customer->name ?? '') . ' خلال ' . $daysLeft . ' أيام.',
                    'Warranty for product "' . ($warranty->product->name ?? '') . '" expires in ' . $daysLeft . ' days for customer ' . ($warranty->customer->name ?? ''),
                    \App\Models\Warranty::class,
                    $warranty->id,
                    $warranty->branch_id
                );
            }
        }
    }
}

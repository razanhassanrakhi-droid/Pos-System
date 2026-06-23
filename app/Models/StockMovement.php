<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($movement) {
            $prefix = $movement->type === 'transfer' ? 'TRF' : 'MOV';
            $movement->movement_number = $movement->movement_number ?: \App\Services\DocumentNumberService::generateDailyNumber($prefix, self::class, 'created_at', 'movement_number', $movement->branch_id);
        });

        static::created(function ($movement) {
            $product = $movement->product;
            if ($product) {
                $branchId = $movement->branch_id;
                $newStock = $product->currentBranchStock($branchId);
                
                if ($newStock > $product->minimum_stock) {
                    // Auto-resolve both low stock and out of stock alerts
                    \App\Models\Notification::active()
                        ->where('product_id', $product->id)
                        ->where('branch_id', $branchId)
                        ->whereIn('type', ['low_stock', 'out_of_stock'])
                        ->update(['resolved_at' => now()]);
                } elseif ($newStock > 0) {
                    // Auto-resolve out of stock alert only
                    \App\Models\Notification::active()
                        ->where('product_id', $product->id)
                        ->where('branch_id', $branchId)
                        ->where('type', 'out_of_stock')
                        ->update(['resolved_at' => now()]);

                    // Generate or update low stock alert
                    $unit = $product->base_unit_name ?? 'pcs';
                    \App\Services\NotificationService::send(
                        'Inventory',
                        'low_stock',
                        'Important',
                        'تنبيه نقص المخزون',
                        'Low Stock Alert',
                        'المنتج "' . $product->name . '" منخفض المخزون. المتبقي: ' . (int)$newStock . ' ' . $unit . '.',
                        'Product "' . $product->name . '" is low on stock. Remaining: ' . (int)$newStock . ' ' . $unit . '.',
                        Product::class,
                        $product->id,
                        $branchId,
                        $movement->created_by
                    );
                } else {
                    // Generate or update out of stock alert
                    \App\Services\NotificationService::send(
                        'Inventory',
                        'out_of_stock',
                        'Critical',
                        'نفد من المخزون',
                        'Out of Stock',
                        'المنتج "' . $product->name . '" نفد بالكامل من المخزون.',
                        'Product "' . $product->name . '" is completely out of stock.',
                        Product::class,
                        $product->id,
                        $branchId,
                        $movement->created_by
                    );
                }
            }
        });
    }

    protected $fillable = [
        'movement_number',
        'product_id',
        'branch_id',
        'type',
        'quantity',
        'reference_id',
        'reference_type',
        'note',
        'created_by',
        'batch_id',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the parent reference model (e.g. Sale, Purchase, Adjustment).
     */
    public function reference()
    {
        return $this->morphTo();
    }
}

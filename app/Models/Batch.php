<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'product_id',
        'batch_number',
        'expiry_date',
        'quantity', // Maps to remaining_quantity for legacy support
        'purchase_price',
        'sale_price',
        'purchase_item_id',
        'branch_id',
        'purchase_unit',
        'purchased_quantity',
        'converted_quantity',
        'conversion_factor',
        'remaining_quantity',
        'status'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'purchased_quantity' => 'decimal:2',
        'converted_quantity' => 'decimal:2',
        'conversion_factor' => 'decimal:4',
        'remaining_quantity' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($batch) {
            $batch->batch_number = $batch->batch_number ?: \App\Services\DocumentNumberService::generateStaticNumber('BAT', self::class, 'batch_number', $batch->branch_id);
        });

        static::updated(function ($batch) {
            if ($batch->remaining_quantity <= 0) {
                \App\Models\Notification::active()
                    ->where('batch_id', $batch->id)
                    ->whereIn('type', ['expiring_soon', 'expired'])
                    ->update(['resolved_at' => now()]);
            } else {
                $now = \Carbon\Carbon::now()->startOfDay();
                $admin = \App\Models\User::where('role', 'admin')->first();
                $settings = $admin ? ($admin->notification_settings ?? []) : [];
                $days = (int) ($settings['inventory']['expiry_warning_period'] ?? 30);
                
                if ($batch->expiry_date && $batch->expiry_date->greaterThan($now->copy()->addDays($days))) {
                    \App\Models\Notification::active()
                        ->where('batch_id', $batch->id)
                        ->whereIn('type', ['expiring_soon', 'expired'])
                        ->update(['resolved_at' => now()]);
                }
            }
        });

        static::deleted(function ($batch) {
            \App\Models\Notification::active()
                ->where('batch_id', $batch->id)
                ->update(['resolved_at' => now()]);
        });
    }

    public function getShortNumberAttribute()
    {
        $parts = explode('-', $this->batch_number);
        $seq = end($parts);
        $label = app()->getLocale() == 'ar' ? 'تشغيلة' : 'Batch';
        return $label . ' #' . $seq;
    }

    public static function generateBatchNumber()
    {
        return \App\Services\DocumentNumberService::generateStaticNumber('BAT', self::class, 'batch_number', session('branch_id'));
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function adjustments()
    {
        return $this->hasMany(InventoryAdjustment::class);
    }

    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }
}

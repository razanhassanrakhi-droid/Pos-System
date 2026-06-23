<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryAdjustment extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($adj) {
            $adj->adjustment_number = $adj->adjustment_number ?: \App\Services\DocumentNumberService::generateDailyNumber('ADJ', self::class, 'created_at', 'adjustment_number', $adj->branch_id);
        });
    }

    public function getShortNumberAttribute()
    {
        if (empty($this->adjustment_number)) {
            return '';
        }
        $parts = explode('-', $this->adjustment_number);
        $seq = end($parts);
        $label = app()->getLocale() == 'ar' ? 'تعديل' : 'Adjustment';
        return $label . ' #' . $seq;
    }

    protected $appends = ['short_number'];

    protected $fillable = [
        'adjustment_number',
        'product_id',
        'batch_id',
        'product_unit_id',
        'branch_id',
        'user_id',
        'quantity',
        'entered_quantity',
        'adjustment_type',
        'reason',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

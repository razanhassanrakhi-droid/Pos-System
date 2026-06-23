<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($return) {
            $return->return_number = $return->return_number ?: \App\Services\DocumentNumberService::generateDailyNumber('RET', self::class, 'created_at', 'return_number', $return->branch_id);
        });
    }

    public function getShortNumberAttribute()
    {
        if (empty($this->return_number)) {
            return '';
        }
        $parts = explode('-', $this->return_number);
        $seq = end($parts);
        $label = app()->getLocale() == 'ar' ? 'مرتجع' : 'Return';
        return $label . ' #' . $seq;
    }

    public function getTranslatedReasonAttribute()
    {
        if (app()->getLocale() != 'ar') {
            return $this->reason;
        }

        $reasons = [
            'Customer Request' => 'رغبة العميل',
            'Damaged Item' => 'منتج تالف',
            'Wrong Item' => 'منتج خاطئ',
            'Expired Product' => 'منتج منتهي الصلاحية',
            'Incorrect Sale' => 'بيعة خاطئة',
            'Other' => 'أخرى',
        ];

        return $reasons[$this->reason] ?? $this->reason;
    }

    protected $fillable = [
        'return_number',
        'sale_id',
        'product_id',
        'batch_id',
        'branch_id',
        'quantity',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

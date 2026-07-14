<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($purchase) {
            $purchase->invoice_number = $purchase->invoice_number ?: \App\Services\DocumentNumberService::generateContinuousNumber('PUR', self::class, 'invoice_number', $purchase->branch_id);
        });
    }

    protected $appends = ['short_number', 'status'];

    public function getShortNumberAttribute()
    {
        $num = preg_replace('/^PUR(-[a-zA-Z]{2}\d{2})?-?/i', '', $this->invoice_number);
        $label = app()->getLocale() == 'ar' ? 'مشتريات' : 'Purchase';
        return $label . ' #' . $num;
    }

    public function getStatusAttribute()
    {
        $paid = (float) $this->paid_amount;
        $total = (float) $this->total_amount;
        
        if ($paid >= $total) {
            return 'paid';
        } elseif ($paid > 0) {
            return 'partial';
        }
        return 'pending';
    }

    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'branch_id',
        'user_id',
        'subtotal',
        'discount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_method',
        'notes',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }
}

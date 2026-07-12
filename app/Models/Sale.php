<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;

class Sale extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($sale) {
            $sale->invoice_number = $sale->invoice_number ?: \App\Services\DocumentNumberService::generateContinuousNumber('INV', self::class, 'invoice_number', $sale->branch_id);
        });
    }

    public function getShortNumberAttribute()
    {
        $num = preg_replace('/^INV(-[a-zA-Z]{2}\d{2})?-?/i', '', $this->invoice_number ?? '');
        $label = app()->getLocale() == 'ar' ? 'فاتورة' : 'Invoice';
        return $label . ' #' . $num;
    }

    protected $appends = ['short_number'];

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'branch_id',
        'user_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid_amount',
        'change_amount',
        'payment_method',
        'notes',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function getRemainingAttribute()
    {
        return $this->total - $this->paid_amount;
    }

    public function getZatcaQrAttribute()
    {
        $setting = Setting::first();
        if (!$setting) return '';

        $sellerName = $setting->getTranslation('company_name', 'ar') ?: $setting->getTranslation('company_name', 'en');
        $vatNumber = $setting->tax_number ?? '';
        $timestamp = $this->created_at->toIso8601String();
        $total = number_format($this->total, 2, '.', '');
        $tax = number_format($this->tax, 2, '.', '');

        return $this->generateZatcaTlv($sellerName, $vatNumber, $timestamp, $total, $tax);
    }

    private function generateZatcaTlv($sellerName, $vatNumber, $timestamp, $total, $tax)
    {
        $data = [
            1 => $sellerName,
            2 => $vatNumber,
            3 => $timestamp,
            4 => $total,
            5 => $tax
        ];

        $tlv = "";
        foreach ($data as $tag => $value) {
            $value = (string)$value;
            $length = strlen($value);
            $tlv .= chr($tag) . chr($length) . $value;
        }

        return base64_encode($tlv);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function returns()
    {
        return $this->hasMany(SalesReturn::class);
    }
}

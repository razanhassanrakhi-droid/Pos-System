<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'unit_name_ar',
        'unit_name_en',
        'barcode',
        'conversion_factor',
        'sale_price',
        'pricing_mode',
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:4',
        'sale_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSalePriceAttribute($value)
    {
        if (($this->pricing_mode ?? 'custom') === 'automatic') {
            if ($this->relationLoaded('product') && $this->product) {
                return (float) ($this->product->sale_price * $this->conversion_factor);
            }
        }
        return (float) $value;
    }
    protected $appends = ['unit_name'];

    public function getUnitNameAttribute()
    {
        $locale = app()->getLocale();
        if ($locale === 'ar') {
            return $this->unit_name_ar ?? $this->unit_name_en;
        }
        return $this->unit_name_en ?? $this->unit_name_ar;
    }
}

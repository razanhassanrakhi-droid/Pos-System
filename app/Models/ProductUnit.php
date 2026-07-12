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
        $val = $locale === 'ar'
            ? ($this->unit_name_ar ?? $this->unit_name_en)
            : ($this->unit_name_en ?? $this->unit_name_ar);

        if ($locale === 'ar' && $val) {
            $translations = [
                'piece'  => 'حبة',
                'pieces' => 'حبة',
                'pices'  => 'حبة',
                'psc'    => 'حبة',
                'pcs'    => 'حبة',
                'box'    => 'علبة',
                'pack'   => 'عبوة',
                'tape'   => 'شريط',
                'tabe'   => 'شريط',
                'kg'     => 'كجم',
                'gram'   => 'جرام',
            ];
            return $translations[strtolower($val)] ?? $val;
        }
        return $val;
    }
}

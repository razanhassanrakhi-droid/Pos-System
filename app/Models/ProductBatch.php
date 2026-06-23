<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'branch_id',
        'batch_number',
        'quantity',
        'expiry_date',
        'purchase_price',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'decimal:2',
        'purchase_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeActive($query)
    {
        return $query->where('quantity', '>', 0)
                     ->where(function($q) {
                         $q->whereNull('expiry_date')
                           ->orWhere('expiry_date', '>', now());
                     });
    }
}

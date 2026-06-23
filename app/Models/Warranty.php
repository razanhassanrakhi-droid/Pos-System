<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'sale_item_id',
        'product_id',
        'customer_id',
        'branch_id',
        'serial_number',
        'warranty_start_date',
        'warranty_end_date',
        'warranty_period_months',
        'status',
        'notes',
    ];

    protected $casts = [
        'warranty_start_date' => 'date',
        'warranty_end_date' => 'date',
        'warranty_period_months' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'ACTIVE' && !$this->warranty_end_date->isPast();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_number',
        'warranty_type',
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
        'created_by',
    ];

    protected $casts = [
        'warranty_start_date' => 'date',
        'warranty_end_date' => 'date',
        'warranty_period_months' => 'integer',
    ];
    
    protected $appends = ['calculated_status'];

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
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function claims()
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    public function getCalculatedStatusAttribute()
    {
        // Check if there's any active claim
        $latestClaim = $this->claims()->latest()->first();
        
        if ($latestClaim && in_array($latestClaim->status, ['Pending', 'Approved'])) {
            return $latestClaim->status === 'Pending' ? 'Claim Submitted' : 'Claim Approved';
        }
        
        if ($this->status === 'Cancelled' || $this->status === 'Completed') {
            return $this->status;
        }

        if ($this->warranty_end_date->isPast()) {
            return 'Expired';
        }

        if (now()->diffInDays($this->warranty_end_date, false) <= 30) {
            return 'Expiring Soon';
        }

        return 'Active';
    }

    public function getIsActiveAttribute()
    {
        return $this->calculated_status === 'Active' || $this->calculated_status === 'Expiring Soon';
    }
}

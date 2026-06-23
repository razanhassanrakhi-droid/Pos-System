<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = [
        'purchase_id',
        'payment_method',
        'amount',
        'reference_number',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}

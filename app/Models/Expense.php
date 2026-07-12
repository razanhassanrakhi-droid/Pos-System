<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_number',
        'type',
        'amount',
        'expense_date',
        'description_ar',
        'description_en',
        'status',
        'payment_method',
        'attachment',
        'user_id',
        'branch_id',
    ];

    // علاقة المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة الفرع
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->status;
    }

    public function getAttachmentUrlAttribute()
    {
        if ($this->attachment) {
            return asset('storage/' . $this->attachment);
        }
        return null;
    }
}
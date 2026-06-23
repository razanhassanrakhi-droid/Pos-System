<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'expense_date',
        'description_ar',
        'description_en',
        'status',       // العمود الجديد
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
        return $this->status ? __('pos.active') : __('pos.inactive');
    }
}
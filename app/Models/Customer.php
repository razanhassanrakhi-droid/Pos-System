<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($customer) {
            if (!$customer->customer_number) {
                // Get the latest customer number
                $latest = self::where('customer_number', 'like', 'CUS-%')->whereRaw('LENGTH(customer_number) = 9')->orderBy('id', 'desc')->first();
                $sequence = 1;
                if ($latest) {
                    $parts = explode('-', $latest->customer_number);
                    $sequence = (int) end($parts) + 1;
                } else {
                    $sequence = self::max('id') + 1;
                }
                $customer->customer_number = sprintf("CUS-%05d", $sequence);
            }
        });
    }

    public function getShortNumberAttribute()
    {
        if (empty($this->customer_number)) {
            return '';
        }
        $parts = explode('-', $this->customer_number);
        $seq = end($parts);
        $label = app()->getLocale() == 'ar' ? 'عميل' : 'Customer';
        return $label . ' #' . $seq;
    }

    protected $fillable = [
        'customer_number',
        'name',
        'phone',
        'email',
        'address',
        'notes',
        'branch_id',
        'visit_count',
        'customer_type',
        'status',
        'dob',
        'loyalty_points',
        'credit_limit',
        'balance',
    ];

    protected $casts = [
        'name' => 'array',
        'dob' => 'date',
    ];

    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $value = $this->getAttributes()[$field] ?? null;

        if ($value && is_string($value) && is_array(json_decode($value, true))) {
            $value = json_decode($value, true);
        }

        if (is_array($value)) {
            return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? '';
        }

        return (string) $value;
    }

    public function getNameAttribute($value)
    {
        if (empty($value)) return '';
        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
        }
        return $value;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Increment the visit count (called when a new invoice/sale is recorded).
     */
    public function incrementVisit(): void
    {
        $this->increment('visit_count');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function returns()
    {
        // Assuming there is a SaleReturn model, or similar.
        // Update this based on the actual model for returns if needed.
        return $this->hasMany(SaleReturn::class, 'customer_id');
    }
}

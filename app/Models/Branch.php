<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phone',
        'address',
        'city',
        'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'address' => 'array',
        'city' => 'array',
        'is_active' => 'boolean',
    ];

    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        // Skip the accessor by using getRawOriginal
        $raw = $this->getRawOriginal($field);
        
        if (empty($raw)) return '';
        
        // Decode raw JSON if it's a string, or use as is if it's already an array
        $value = is_string($raw) ? json_decode($raw, true) : $raw;
        
        if (is_array($value)) {
             return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? '';
        }
        
        return $value;
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

    public function getAddressAttribute($value)
    {
        if (empty($value)) return '';
        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
        }
        return $value;
    }

    public function getCityAttribute($value)
    {
        if (empty($value)) return '';
        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
        }
        return $value;
    }

    public function returns()
    {
        return $this->hasMany(SalesReturn::class);
    }
}

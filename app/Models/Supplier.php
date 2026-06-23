<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($supplier) {
            $supplier->supplier_number = $supplier->supplier_number ?: \App\Services\DocumentNumberService::generateStaticNumber('SUP', self::class, 'supplier_number', $supplier->branch_id);
        });
    }

    public function getShortNumberAttribute()
    {
        if (empty($this->supplier_number)) {
            return '';
        }
        $parts = explode('-', $this->supplier_number);
        $seq = end($parts);
        $label = app()->getLocale() == 'ar' ? 'مورد' : 'Supplier';
        return $label . ' #' . $seq;
    }

    protected $fillable = [
        'supplier_number',
        'name',
        'contact_person',
        'email',
        'phone',
        'alternative_phone',
        'notes',
        'status',
        'address',
        'branch_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'name' => 'array',
        'address' => 'array',
        'contact_person' => 'array',
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

    public function getContactPersonAttribute($value)
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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}

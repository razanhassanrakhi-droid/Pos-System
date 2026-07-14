<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($category) {
            $category->category_number = $category->category_number ?: \App\Services\DocumentNumberService::generateStaticNumber('CAT', self::class, 'category_number', $category->branch_id);
        });
    }

    public function getShortNumberAttribute()
    {
        if (empty($this->category_number)) {
            return '';
        }
        $parts = explode('-', $this->category_number);
        $seq = end($parts);
        $label = app()->getLocale() == 'ar' ? 'فئة' : 'Category';
        return $label . ' #' . $seq;
    }

    protected $fillable = [
        'category_number', 'branch_id', 'name', 'is_active', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'name' => 'array',
        'is_active' => 'boolean',
    ];

    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $value = $this->getAttributes()[$field] ?? null;
        
        if (is_string($value)) {
            $value = json_decode($value, true);
        }
        
        if (is_array($value)) {
             return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? '';
        }
        
        return $value ?? '';
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
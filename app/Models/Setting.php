<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_logo',
        'company_address',
        'company_phone',
        'company_email',
        'tax_number',
        'registration_number',
        'footer_text',
        'currency',
        'default_tax',
    ];

    protected $casts = [
        'company_name' => 'array',
        'company_address' => 'array',
        'footer_text' => 'array',
        'currency' => 'array',
        'default_tax' => 'float',
    ];

    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        // Access the raw attribute to avoid infinite recursion through accessors
        $value = $this->attributes[$field] ?? null;

        // Manually apply JSON decoding if the attribute is cast as an array/json
        if ($value && is_string($value) && ($this->hasCast($field, ['array', 'json', 'object', 'collection']))) {
            $value = json_decode($value, true);
        }
        
        if (is_array($value)) {
            return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? '';
        }
        
        return $value;
    }

    public function getCompanyNameTranslationAttribute()
    {
        return $this->getTranslation('company_name');
    }

    public function getCompanyAddressTranslationAttribute()
    {
        return $this->getTranslation('company_address');
    }

    public function getCurrencyAttribute()
    {
        return $this->getTranslation('currency') ?: (app()->getLocale() == 'ar' ? 'ريال' : 'SAR');
    }

    public function getCurrencyRawAttribute()
    {
        $value = $this->attributes['currency'] ?? null;
        if ($value && is_string($value)) {
            return json_decode($value, true);
        }
        return $value;
    }

}

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
        'license_key',
        'license_expires_at',
    ];

    protected $casts = [
        'company_name' => 'array',
        'company_address' => 'array',
        'footer_text' => 'array',
        'currency' => 'array',
        'default_tax' => 'float',
        'license_expires_at' => 'date',
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

    public function isLicenseValid()
    {
        // 1. If a license exists, check deep verification
        if ($this->license_key && $this->license_expires_at) {
            if ($this->license_expires_at->isPast()) {
                return false;
            }

            try {
                $licenseData = json_decode(base64_decode($this->license_key), true);
                if (!$licenseData || !isset($licenseData['signature']) || !isset($licenseData['data'])) {
                    return false;
                }

                $secret = env('LICENSE_SECRET', 'DigitalAgePosSystemSecretKey2026!#');
                $expectedSignature = hash_hmac('sha256', json_encode($licenseData['data']), $secret);
                
                if (!hash_equals($expectedSignature, $licenseData['signature'])) {
                    return false;
                }

                // Check Device ID
                $storedDeviceId = $licenseData['data']['device_id'] ?? null;
                
                // Get Current Device ID (Logic from SettingController)
                $host = gethostname();
                $os = php_uname('s');
                $envKey = env('APP_KEY', 'default-salt');
                $hash = substr(md5($host . $os . $envKey), 0, 12);
                $currentDeviceId = strtoupper(implode('-', str_split($hash, 4)));

                return $storedDeviceId === $currentDeviceId;
            } catch (\Exception $e) {
                return false;
            }
        }

        // 2. Automatic 7-day trial if no license is present
        $trialDays = 7;
        $installationDate = $this->created_at ?: now();
        $trialExpiryDate = $installationDate->copy()->addDays($trialDays);

        return now()->lessThan($trialExpiryDate);
    }
}

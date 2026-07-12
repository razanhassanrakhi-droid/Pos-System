<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_id',
        'claim_date',
        'issue_description',
        'technician_notes',
        'resolution',
        'attachments',
        'status',
        'created_by',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'attachments' => 'array',
        'issue_description' => 'array',
        'resolution' => 'array',
    ];

    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $value = $this->$field;
        
        if (is_array($value)) {
             return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? '';
        }
        
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded[$locale] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
        }
        
        return $value;
    }

    public function getIssueDescriptionAttribute($value)
    {
        if (empty($value)) return '';
        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
        }
        return $value;
    }

    public function getResolutionAttribute($value)
    {
        if (empty($value)) return '';
        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
        }
        return $value;
    }

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

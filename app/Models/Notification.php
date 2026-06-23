<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_number',
        'title',
        'message',
        'type',
        'category',
        'priority',
        'reference_type',
        'reference_id',
        'read_status',
        'read_date',
        'branch_id',
        'created_by',
        'product_id',
        'batch_id',
        'resolved_at',
        'created_by_system',
    ];

    protected $casts = [
        'title' => 'array',
        'message' => 'array',
        'read_status' => 'boolean',
        'read_date' => 'datetime',
        'resolved_at' => 'datetime',
        'created_by_system' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($notification) {
            $notification->notification_number = $notification->notification_number ?: \App\Services\DocumentNumberService::generateDailyNumber('NTF', self::class, 'created_at', 'notification_number', $notification->branch_id);
        });
    }

    /**
     * Scope to only include active (unresolved) notifications.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Relationship to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship to Batch
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the referenced model (polymorphic).
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Get creator of the notification.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get branch associated with this notification.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Helper to get translated fields (title/message).
     */
    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $raw = $this->getRawOriginal($field);
        
        if (empty($raw)) return '';
        
        $value = is_string($raw) ? json_decode($raw, true) : $raw;
        
        if (is_array($value)) {
            return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? '';
        }
        
        return $value;
    }

    public function getTitleTranslationAttribute()
    {
        return $this->getTranslation('title');
    }

    public function getMessageTranslationAttribute()
    {
        return $this->getTranslation('message');
    }
}

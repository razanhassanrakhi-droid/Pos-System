<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'full_name',
        'username',
        'email',
        'phone',
        'role',
        'notification_settings',
        'password',
        'is_active',
        'security_question',
        'security_answer',
        'security_question_2',
        'security_answer_2',
        'security_question_3',
        'security_answer_3',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'full_name' => 'array',
        'username' => 'array',
        'notification_settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Check if a specific notification type is enabled for the user.
     */
    public function isNotificationEnabled($category, $type)
    {
        $category = strtolower($category);
        $settings = $this->notification_settings;
        if (is_null($settings)) {
            if ($category === 'sales') {
                return false;
            }
            return true;
        }
        return (bool) ($settings[$category][$type] ?? ($category === 'sales' ? false : true));
    }

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
        
        return $raw; // Fallback to raw string if not an array/JSON
    }

    public function getFullNameAttribute($value)
    {
        if (empty($value)) return '';
        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
        }
        return $value;
    }

    public function getUsernameAttribute($value)
    {
        if (empty($value)) return '';
        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
        }
        return $value;
    }

    /**
     * Hash the password automatically when saving.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * The branches that belong to the user.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_user')->withTimestamps();
    }

    /**
     * Get accessible branches.
     * If user is admin, return all active branches.
     * Otherwise, return only assigned branches.
     */
    public function accessibleBranches()
    {
        if ($this->role === 'admin') {
            return Branch::where('is_active', true)->get();
        }

        return $this->branches()->where('is_active', true)->get();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a standard employee
     */
    public function returns()
    {
        return $this->hasMany(SalesReturn::class, 'created_by');
    }
}

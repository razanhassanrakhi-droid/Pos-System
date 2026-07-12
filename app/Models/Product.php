<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Added this line

class Product extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->product_number = $product->product_number ?: \App\Services\DocumentNumberService::generateStaticNumber('PRD', self::class, 'product_number', $product->branch_id);
        });
    }

    protected $appends = ['short_number', 'current_stock', 'base_unit_name'];

    protected $dynamicCache = [];

    public function getBaseUnitNameAttribute()
    {
        $locale = app()->getLocale();
        $val = $locale === 'ar'
            ? ($this->base_unit_name_ar ?: $this->base_unit_name_en)
            : ($this->base_unit_name_en ?: $this->base_unit_name_ar);

        if ($locale === 'ar' && $val) {
            $translations = [
                'piece'  => 'حبة',
                'pieces' => 'حبة',
                'pices'  => 'حبة',
                'psc'    => 'حبة',
                'pcs'    => 'حبة',
                'box'    => 'علبة',
                'pack'   => 'عبوة',
                'tape'   => 'شريط',
                'tabe'   => 'شريط',
                'kg'     => 'كجم',
                'gram'   => 'جرام',
            ];
            return $translations[strtolower($val)] ?? $val;
        }
        return $val;
    }

    public function getCurrentStockAttribute()
    {
        if (array_key_exists('current_stock', $this->attributes)) {
            return (float) $this->attributes['current_stock'];
        }
        if (array_key_exists('current_stock', $this->dynamicCache)) {
            return $this->dynamicCache['current_stock'];
        }
        $branchId = session('branch_id');
        return $this->dynamicCache['current_stock'] = (float) ($branchId ? $this->currentBranchStock($branchId) : $this->totalStock());
    }

    public function getShortNumberAttribute()
    {
        if (empty($this->product_number)) {
            return '';
        }
        $parts = explode('-', $this->product_number);
        $seq = end($parts);
        $label = app()->getLocale() == 'ar' ? 'منتج' : 'Product';
        return $label . ' #' . $seq;
    }

    protected $fillable = [
        'product_number',
        'branch_id',
        'name',
        'brand',
        'barcode',
        'sku',
        'status',
        'base_unit_name_ar',
        'base_unit_name_en',
        'category_id',
        'description',
        'image',
        'sale_price',
        'minimum_stock',
        'is_active',
        'has_warranty',
        'warranty_period_months',
        'warranty_type',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'name' => 'array',
        'brand' => 'array',
        'description' => 'array',
        'sale_price' => 'decimal:2',
        'minimum_stock' => 'integer',
        'is_active' => 'boolean',
        'has_warranty' => 'boolean',
        'warranty_period_months' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get stock status (Sufficient, Low Stock).
     */
    public function getStockStatusAttribute()
    {
        $currentStock = $this->current_stock ?? 0;
        if ($currentStock <= 0) {
            return 'Out of Stock';
        }
        if ($currentStock <= $this->minimum_stock) {
            return 'Low Stock';
        }
        return 'Sufficient';
    }

    /**
     * Get the nearest active batch's expiry date dynamically.
     */
    public function getExpiryDateAttribute()
    {
        if (array_key_exists('expiry_date', $this->dynamicCache)) {
            return $this->dynamicCache['expiry_date'];
        }

        if ($this->relationLoaded('batches')) {
            $batch = $this->batches
                ->where('remaining_quantity', '>', 0)
                ->whereNotNull('expiry_date')
                ->sortBy('expiry_date')
                ->first();
            return $this->dynamicCache['expiry_date'] = ($batch ? \Carbon\Carbon::parse($batch->expiry_date) : null);
        }

        $dateStr = $this->batches()
            ->where('remaining_quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->orderBy('expiry_date', 'asc')
            ->value('expiry_date');

        return $this->dynamicCache['expiry_date'] = ($dateStr ? \Carbon\Carbon::parse($dateStr) : null);
    }

    /**
     * Get the latest batch's purchase price dynamically, or default to 0.
     */
    public function getPurchasePriceAttribute()
    {
        if (array_key_exists('purchase_price', $this->dynamicCache)) {
            return $this->dynamicCache['purchase_price'];
        }

        if ($this->relationLoaded('batches')) {
            $latestBatch = $this->batches->sortByDesc('created_at')->first();
            return $this->dynamicCache['purchase_price'] = (float) ($latestBatch ? $latestBatch->purchase_price : 0);
        }

        return $this->dynamicCache['purchase_price'] = (float) ($this->batches()->latest()->value('purchase_price') ?? 0);
    }

    /**
     * Get expiration status (Valid, Expiring Soon, Expired) dynamically based on batches.
     */
    public function getExpirationStatusAttribute()
    {
        $expiryDate = $this->expiry_date;
        if (!$expiryDate) {
            return 'Valid';
        }

        if ($expiryDate->isPast()) {
            return 'Expired';
        }

        if ($expiryDate->diffInDays(now()) <= 30) {
            return 'Expiring Soon';
        }

        return 'Valid';
    }

    // Automatically set status based on expiry
    public function getStatusAttribute($value)
    {
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return 'Expired';
        }
        return $value;
    }

    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $raw = $this->getRawOriginal($field);
        if (empty($raw)) return '';
        $decoded = is_array($raw) ? $raw : json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded[$locale] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
        }
        return $raw;
    }

    public function getNameAttribute($value)
    {
        if (empty($value)) return '';
        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (is_array($decoded)) {
            $name = $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
            return $name;
        }
        return $value;
    }

    public function getBrandAttribute($value)
    {
        if (empty($value)) return '';
        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
        }
        return $value;
    }

    public function getDescriptionAttribute($value)
    {
        if (empty($value)) return '';
        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $decoded['ar'] ?? '';
        }
        return $value;
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function units()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function adjustments()
    {
        return $this->hasMany(InventoryAdjustment::class);
    }

    /**
     * Get the current stock quantity for a specific branch.
     */
    public function currentBranchStock($branchId)
    {
        if ($this->relationLoaded('batches')) {
            return (float) $this->batches
                ->where('branch_id', $branchId)
                ->sum('remaining_quantity');
        }
        return (float) $this->batches()
            ->where('branch_id', $branchId)
            ->sum('remaining_quantity');
    }

    /**
     * Get the total stock quantity across all branches.
     */
    public function totalStock()
    {
        if ($this->relationLoaded('batches')) {
            return (float) $this->batches->sum('remaining_quantity');
        }
        return (float) $this->batches()->sum('remaining_quantity');
    }

    public function warranties()
    {
        return $this->hasMany(Warranty::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function returns()
    {
        return $this->hasMany(SalesReturn::class);
    }
}

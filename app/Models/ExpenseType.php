<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
    ];

    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $localizedField = $field . '_' . $locale;
        return $this->$localizedField ?? $this->name_en ?? $this->name_ar;
    }
}

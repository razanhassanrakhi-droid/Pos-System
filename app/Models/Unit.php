<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

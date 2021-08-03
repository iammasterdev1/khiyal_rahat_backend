<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalSpecifications extends Model
{
    use HasFactory;

    const ACTIVE = 1;
    const INACTIVE = 0;

    public $timestamps = false;

    protected $guarded = [];

    public function scopeActive($q)
    {
        return $q->where('status', self::ACTIVE);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Landing extends Model
{
    use HasFactory;

    const ACTIVE = 1;
    const INACTIVE = 0;

    protected $guarded = [];

    public function categories()
    {
        return $this->belongsToMany(categories::class, 'categories_landings', 'landing_id', 'category_id');
    }

    public function scopeActives($q)
    {
        return $q->where('status', self::ACTIVE);
    }
}

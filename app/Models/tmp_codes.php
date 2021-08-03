<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmp_codes extends Model
{
    use HasFactory;

    const EXPIRE = 1;
    const NO_EXPIRE = 0;
    const ACTIVE = 1;
    const INACTIVE = 0;
    const PRESENTER = 1;
    const NO_PRESENTER = 0;


    protected $guarded = [];

    public function scopeActive($q)
    {
        return $q->where('active', self::ACTIVE);
    }
}

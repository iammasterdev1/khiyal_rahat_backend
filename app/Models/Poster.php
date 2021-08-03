<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poster extends Model
{
    use HasFactory, SoftDeletes;

    const MONETARY = 0;
    const FREE = 1;
    const ACTIVE = 1;
    const INACTIVE = 0;

    protected $guarded = [];

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }

    public function scopeMonetary($q)
    {
        return $q->where('free', self::MONETARY);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}

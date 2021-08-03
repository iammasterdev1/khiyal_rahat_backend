<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class Lesson extends Model
{
    use HasFactory;

    const ACTIVE = 1;
    const INACTIVE = 0;

    protected $guarded = [];

    protected $columns = ['id',
        'title',
        'description',
        'cover',
        'responsive_image',
        'permalink',
        'status',
        'created_at',
        'price',
        'updated_at',
        'small_banner',
        'spotplayer',
        'room_id',
        'room_url',
        'online',
    ]; // add all columns from you table

    //region relations
    public function images()
    {
        return $this->morphMany(
            products_images::class,
            'product'
        );
    }

    public function posters()
    {
        return $this->hasMany(Poster::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

 public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

 public function comments()
    {
        return $this->morphMany(
            comments::class,
            'commentable'
        );
    }
    //endregion

    public function scopeGetAll($q)
    {
        return $q->where('status', self::ACTIVE);
    }

    public function scopeExclude($query, $value = [])
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }
}

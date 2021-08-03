<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Classes\Custom\PersianDate;
use Illuminate\Database\Eloquent\Model;


class comments extends Model
{
    use HasFactory;
    const ACTIVE = 1;
    const INACTIVE = 0;

    
    protected $guarded = [];
    public function productComment(){
        return $this->morphTo(
            'commentable'
        );
    }

public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function persianCreated()
    {
        return PersianDate::jdate('d F Y', strtotime($this->created_at));
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    const ACTIVE = 1;
    const INACTIVE = 0;

    protected $guarded = [];

    public function images(){
        return $this->morphMany(
            products_images::class ,
            'product'
        );
    }
}

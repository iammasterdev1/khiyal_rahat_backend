<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceProductTechnical extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function technical_specification()
    {
        return $this->belongsTo(TechnicalSpecifications::class);
    }
}

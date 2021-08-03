<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class brands extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function categories()
    {
        return $this->belongsToMany(categories::class, 'brand_categories', 'brand_id', 'category_id');
    }

    public function scopeCategoriesIn($q, $categories)
    {
        return $q
            ->select('brands.*')
            ->Join('brand_categories', 'brands.id', '=', 'brand_categories.brand_id')
            ->whereIn('brand_categories.category_id', $categories);
    }

    public function scopeCategories($q, $categories)
    {
        return $q
            ->select('brands.*')
            ->Join('brand_categories', 'brands.id', '=', 'brand_categories.brand_id')
            ->where('brand_categories.category_id', $categories);
    }
}

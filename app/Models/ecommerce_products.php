<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed category
 * @property int|mixed price_irr
 * @property mixed|string product_description
 * @property mixed|string product_name
 * @property int|mixed irr_price_after_off
 * @property mixed|string image
 * @method static find($get)
 * @method static orderBy(int|string $order_by_what, int|string $order_by_how)
 */
class ecommerce_products extends Model
{
    use HasFactory, SoftDeletes;

    const ACCEPTED = 1;
    const ACCEPTED_AND_HIDE = 2;
    const SHOW_ON_LANDING = 1;
    const HIDE_ON_LANDING = 0;

    protected $guarded = [];


    public function productFeatures(){
        return $this->morphMany(
            products_features::class ,
            'product'
        );
    }
    public function productImages(){
        return $this->morphMany(
            products_images::class ,
            'product'
        );
    }

    public function showColors(){
        return $this->belongsToMany(
            colors::class ,
            'ecommerce_colors' ,
            "product_id",
            'color_id' ,
            'id',
            "id"
        )->limit(4);
    }
    public function important_features(){
        return $this->hasMany(
            ecommerce_product_important_features::class ,
            'product_id'
        );
    }

    public function technical_specifications (){
        return $this->hasMany(
            ecommerce_product_technical_specifications::class ,
            'product_id'
        );
    }

    public function brand()
    {
        return $this->belongsTo(brands::class, 'brand_id', 'id');
    }

    public function categories()
    {
        return $this->belongsTo(categories::class, 'category', 'id');
    }

    public function allCategories()
    {
        return $this->belongsToMany(categories::class, 'category_product', 'ecommerce_product_id', 'category_id');
    }

    public function technicals()
    {
        return $this->hasMany(EcommerceProductTechnical::class, 'ecommerce_product_id', 'id');
    }

    /*public function sizes()
    {
        return $this->hasMany(ecommerce_sizes::class, 'product_id');
    }*/

    public function comments()
    {
        return $this->morphMany(
            comments::class,
            'commentable'
        );
    }

    public function sizes(){
        return $this->belongsToMany(
            sizes::class,
            'ecommerce_sizes',
            'product_id',
            'size_id'
        );
    }

    public function colors(){
        return $this->belongsToMany(
            colors::class ,
            'ecommerce_colors' ,
            "product_id",
            'color_id' ,
            'id',
            "id"
        );
    }

    public function scopeGetLast($q)
    {
        return $q->orderBy('id', 'desc');
    }

    public function scopeAccepted($q)
    {
        return $q->where('status', self::ACCEPTED);
    }

    public function scopeBrandsIn($q, $brands)
    {
        return $q->whereIn('ecommerce_products.brand_id', $brands);
    }

    public function scopeCategoriesIn($q, $categories)
    {
        return $q->
        select('ecommerce_products.*')
            ->join('category_product', 'ecommerce_products.id', '=', 'category_product.ecommerce_product_id')
            ->whereIn('category_product.category_id', $categories);
    }


    public function scopeLikeName($q, $name)
    {
        return $q->where('ecommerce_products.product_name', 'like', '%' . $name . '%');
    }

    public function scopePriceFrom($q, $price)
    {
        return $q->where('ecommerce_products.price_irr_after_off', '>=', $price);
    }

    public function scopePriceTo($q, $price)
    {
        return $q->where('ecommerce_products.price_irr_after_off', '<=', $price);
    }


}

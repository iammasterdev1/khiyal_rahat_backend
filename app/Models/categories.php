<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string cat
 * @property mixed|string cat_of
 * @property mixed sub_cat_of
 * @property mixed image
 * @method static find($get)
 * @method static where(string $string, string $string1, $get)
 */
class categories extends Model
{
    use HasFactory;

    const DEEP_PARENT_CATEGORY = 2;

    public static $parentsCount = 1;
    public static $childrenCount = 1;
    private static $ids = [];


    protected $guarded = [];

    public function technical_specifications()
    {
        return $this->hasMany(TechnicalSpecifications::class, 'category_id');
    }

    public function children()
    {
        return $this->hasMany(categories::class, 'sub_cat_of')->where('cat_of', 2);
    }

    public function parent()
    {
        return $this->belongsTo(categories::class, 'sub_cat_of')->where('cat_of', 2);
    }

    public function allParent()
    {
        return $this->parent()->with('allParent');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function products()
    {
        return $this->hasManyThrough(ecommerce_products::class, CategoryProduct::class, 'category_id', 'id', 'id', 'ecommerce_product_id');
    }

    public function scopeCategories($q)
    {
        return $q->where('cat_of', 2)->Orwhere('cat_of', 3);
    }

    public function scopeCategory($q)
    {
        return $q->where('cat_of', 2);
    }

    public function scopeCategoriesIn($q, $categories)
    {
        return $q->whereIn('id', $categories);
    }

    public function getAllChildrenIds()
    {
        $ids =  [$this->id];
        foreach ($this->allChildren as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }
        return $ids;
    }

    public function getAllChildrenValues($to = null)
    {
        self::$ids[] = $this->id;
        $values =  [
            'id'           =>  $this->id,
            'cat'          =>  $this->cat,
            'permalink'    =>  $this->permalink,
            'cat_image'    =>  $this->cat_image
        ];
        if (self::$childrenCount <= self::DEEP_PARENT_CATEGORY || isset($to) && $to && !array_search($to, self::$ids)){
            foreach ($this->allChildren as $child) {
                self::$childrenCount++;
                $values["children"][] = $child->getAllChildrenValues($to);
            }
        }
        return $values;
    }

    public function getAllParentsIds($countCategories = 1)
    {
        $ids =  [$this->id];
        if ($this->sub_cat_of != null && self::$parentsCount <= self::DEEP_PARENT_CATEGORY){
            self::$parentsCount++;
            $ids = array_merge($ids, $this->parent->getAllParentsIds());
        }
        return $ids;
    }



}

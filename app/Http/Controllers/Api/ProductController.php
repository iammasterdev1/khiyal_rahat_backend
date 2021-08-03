<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\Comment\StoreRequest;
use App\Http\Requests\Api\Search\Product\SearchRequest;
use App\Http\Requests\show_ecommerce_product_info;
use App\Http\Resources\CategoryAllResource;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\SingleBrandProductResource;
use App\Http\Resources\SingleCategoryProductResource;
use App\Http\Resources\SingleProductResource;
use App\Models\brands;
use App\Models\categories;
use App\Models\comments;
use App\Models\ecommerce_products;
use Illuminate\Support\Collection;

class ProductController extends Controller
{
    public function storeComment(StoreRequest $request)
    {
        $product = ecommerce_products::accepted()->where('id', $request->get('product_id'))->first();

        if (!$product)
            return response()->json([
                'status' => 404,
                'message' => "Product not found."
            ], 404);

        $user = $request->get('user');

        $comment = $product->comments()->create([
            'comment' => htmlspecialchars($request->get('comment')),
            'user_id' => $user->id,
            'status'  => comments::INACTIVE
        ]);

        return response()->json([
            'message' => 'comment added successfully.',
            'success' => $comment
        ]);
    }

    public function show(show_ecommerce_product_info $request)
    {
        $product = ecommerce_products::accepted()->where('id', $request->get('product_id'))->first();

        if (!$product)
            return response()->json([
                'status' => 404,
                'message' => 'We could not find product'
            ]);

        return response()->json([
            'message' => 'product information received successfully.',
            'success' => new SingleProductResource($product)
        ]);
    }

    public function search(SearchRequest $request)
    {

        $products = ecommerce_products::query()->accepted()->orderBy('stock', 'desc');

        if($request->has('categories')){
            $children = [];
            $relatedCategories = [];
            $inputCategories = array_filter(explode(',', $request->input('categories')));
            // key last category becuase front always sent parent category_id too
            $inputCategories && $inputCategories = $inputCategories[array_key_last($inputCategories)];
            $inputCategories && $categories = categories::categories()->where('id', $inputCategories)->first();
            //get all children of catgeories
            if (isset($categories)){
                $categories instanceof categories && $children = array_merge($children, $categories->getAllChildrenIds());
                if (count($children) <= 1 && $categories instanceof categories){
                    $all = $categories->parent->children;
                    $all->push($categories->parent);
                    $categories->parent && $relatedCategories = $all->reverse();
                }
                isset($children) && count($children) > 0 && $products->categoriesIn($children);
            }
        }



        $request->has('brands') && $products->brandsIn(explode(',', $request->input('brands')));

        $request->has('q') && $products->likeName($request->q);

        if ($request->get('price_from'))
            $products->priceFrom($request->price_from);

        if ($request->get('price_to'))
            $products->priceTo($request->price_to);

        if ($request->get('order_by')){

            if ($request->order_by == 1)
                $products->orderBy('created_at', 'desc');

            if ($request->order_by == 2)
                $products->orderBy('created_at', 'asc');

            if ($request->order_by == 3)
                $products->orderBy('price_irr_after_off', 'asc');

            if ($request->order_by == 4)
                $products->orderBy('price_irr_after_off', 'desc');
        }



        $products = $products->groupBy('ecommerce_products.id')->paginate(32);


        $allCategories = categories::query()->category();
        $brands = brands::query();

        (isset($categories) && $categories instanceof categories) && $brands->categories($categories->id);

        (isset($categories) && $categories instanceof categories) ? $allCategories->where('sub_cat_of', $categories->id) : $allCategories->where('sub_cat_of', null);

        return response()->json([
            'status'        =>  200,
            'data'          => new ProductCollection($products),
            'brands'        => SingleBrandProductResource::collection($brands->groupBy('brands.id')->latest()->get()),
            'categories'    => isset($relatedCategories) && count($relatedCategories) > 0 ? SingleCategoryProductResource::collection($relatedCategories) : SingleCategoryProductResource::collection($allCategories->get())
        ], 200);
    }

    public function searchTest(SearchRequest $request)
    {


        $products = ecommerce_products::query()->accepted()->orderBy('stock', 'desc');

        if($request->has('categories')){
            $children = [];
            $relatedCategories = [];
            $inputCategories = array_filter(explode(',', $request->input('categories')));
            // key last category becuase front always sent parent category_id too
            $inputCategories && $inputCategories = $inputCategories[array_key_last($inputCategories)];
            $inputCategories && $categories = categories::categories()->where('id', $inputCategories)->first();
            //get all children of catgeories
            if (isset($categories)){
                $categories instanceof categories && $children = array_merge($children, $categories->getAllChildrenIds());
                if (count($children) <= 1 && $categories instanceof categories){
                    $all = $categories->parent->children;
                    $all->push($categories->parent);
                    $categories->parent && $relatedCategories = $all->reverse();
                }
                isset($children) && count($children) > 0 && $products->categoriesIn($children);
            }
        }



        $request->has('brands') && $products->brandsIn(explode(',', $request->input('brands')));

        $request->has('q') && $products->likeName($request->q);

        if ($request->get('price_from'))
            $products->priceFrom($request->price_from);

        if ($request->get('price_to'))
            $products->priceTo($request->price_to);

        if ($request->get('order_by')){

            if ($request->order_by == 1)
                $products->orderBy('created_at', 'desc');

            if ($request->order_by == 2)
                $products->orderBy('created_at', 'asc');

            if ($request->order_by == 3)
                $products->orderBy('price_irr_after_off', 'asc');

            if ($request->order_by == 4)
                $products->orderBy('price_irr_after_off', 'desc');
        }



        $products = $products->groupBy('ecommerce_products.id')->paginate(32);


        $allCategories = categories::query()->category();
        $brands = brands::query();

        (isset($categories) && $categories instanceof categories) && $brands->categories($categories->id);

        (isset($categories) && $categories instanceof categories) ? $allCategories->where('sub_cat_of', $categories->id) : $allCategories->where('sub_cat_of', null);

        return response()->json([
            'status'        =>  200,
            'data'          => new ProductCollection($products),
            'brands'        => SingleBrandProductResource::collection($brands->groupBy('brands.id')->latest()->get()),
            'categories'    => isset($relatedCategories) && count($relatedCategories) > 0 ? SingleCategoryProductResource::collection($relatedCategories) : SingleCategoryProductResource::collection($allCategories->get())
        ], 200);
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryAllResource;
use App\Models\categories;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = categories::query()->category()->whereNull('sub_cat_of')->with('allChildren')->get();

        return response()->json([
            'data'      =>  CategoryAllResource::collection($categories),
            'status'    =>  200
        ], 200);
    }
}

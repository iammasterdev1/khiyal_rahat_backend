<?php

namespace App\Http\Controllers;

use App\Http\Requests\public_getCatInfo;
use App\Http\Requests\showArticle;
use App\Http\Requests\show_course_details;
use App\Http\Requests\public_showAllSubuCategoriesOfCategory;
use App\Models\categories;
use App\Models\ecommerce_products;
use App\Http\Resources\SingleProductResource;
use App\Models\school_courses;
use Illuminate\Http\Request;
use App\Http\Requests\show_ecommerce_product_info;
use App\Http\Requests\show_product_colors;
use App\Models\articles;
use App\Models\consultant;

class publics extends Controller
{
    public function showAllCourses(Request $request)
    {
        $errors = [];
        $requestInfo = [
            'page' => 1,
            'count' => 20,
            'order_by_what' => 'created_at',
            'order_by_how'  => 'DESC',
            'price_from'    => 0,
            'price_to'      => 99999999999999999999999999999999999999999999999999999999999999999999999999999999999,
            'brand_limit'  => []
        ];

        $request->validate([
            'cat_id' => 'required'
        ]);

        /**
         *
         * CHECK PAGE NUMBER BE TRUE
         *
         */

        if ($request->has('page')) {
            if (
                !is_null(
                    (int)$request->get('page')
                )
            ) {
                /**
                 *
                 * PAGE NUMBER OS CORRECT
                 *
                 */
                $requestInfo['page'] = (int)$request->get('page');
            }
        }
        /**
         *
         * CHECK REQUEST COUNT BE CORRECT
         *
         */
        if (
            is_null(
                (int)$request->get('count')
            )
        ) {
            /**
             *
             * COUNT OF PRODUCTS IN PAGE OS CORRECT
             *
             */
            $requestInfo['count'] = (int)$request->get('count');
        }

        /**
         *
         * CHECK ORDER BY STATUS CHANGING
         *
         */
        if ($request->has('order_by')) {
            /**
             *
             * THERE IS ORDER BY VALUE IN REQUEST
             *
             * ORDER BY VALUES:
             *
             * 1: NEWEST
             *
             * 2: LATEST
             *
             * 3: CHEAPEST
             *
             * 4: MOST EXPENSIVE
             *
             * 5: MOST SOLD
             *
             * 6: MOST POPULAR
             *
             */

            if ((int)$request->get('order_by') > 6 || (int)$request->get('order_by') < 1) {
                /**
                 *
                 * ENTERED ORDER BY VALUE IS INCORRECT
                 *
                 */
                $errors['order_by'][] = 'entered order by value is invalid.';
            } elseif ((int)$request->get('order_by') === 1) {
                /**
                 *
                 * ORDER BY NEWEST
                 *
                 */
                $requestInfo['order_by_what'] = 'created_at';
                $requestInfo['order_by_how']  = 'DESC';
            } elseif ((int)$request->get('order_by') === 2) {
                $requestInfo['order_by_what'] = 'created_at';
                $requestInfo['order_by_how']  = 'ASC';
            } elseif ((int)$request->get('order_by') === 3) {
                $requestInfo['order_by_what'] = 'price_irr';
                $requestInfo['order_by_how']  = 'ASC';
            } elseif ((int)$request->get('order_by') === 4) {
                $requestInfo['order_by_what'] = 'price_irr';
                $requestInfo['order_by_how']  = 'DESC';
            } elseif ((int)$request->get('order_by') === 5) {
                $requestInfo['order_by_what'] = 'created_at';
                $requestInfo['order_by_how']  = 'DESC';
            } elseif ((int)$request->get('order_by') === 6) {
                $requestInfo['order_by_what'] = 'created_at';
                $requestInfo['order_by_how']  = 'DESC';
            }
        }
        /**
         *
         * CHECK PRICE LIMIT
         *
         */
        if ($request->has('price_from')) {
            /**
             *
             * THERE IS PRICE FROM VALUE IN REQUEST
             *
             */
            if ((int)$request->get('price_from') === null) {
                /**
                 *
                 * ENTERED PRICE FROM IS NOT A NUMBER
                 *
                 */
                $errors['price_from'][] = 'entered price from value is incorrect.';
            } elseif ((int)$request->get('price_from') < 0) {
                /**
                 *
                 * PRICE FROM MUST BE MINIMUM 0
                 *
                 */
                $errors['price_from'][] = 'price from value mist be more than 0.';
            } else {
                /**
                 *
                 * THERE IS NO ERROR IN PRICE FROM VALUE
                 *
                 */
                $requestInfo['price_from'] = (int)$request->get('price_from');
            }
        }
        if ($request->has('price_to')) {
            /**
             *
             * THERE IS PRICE TO VALUE IN REQUEST
             *
             */
            if ((int)$request->get('price_to') === null) {
                /**
                 *
                 * ENTERED PRICE TO IS NOT A NUMBER
                 *
                 */
                $errors['price_to'][] = 'entered price to value is incorrect.';
            } else {
                /**
                 *
                 * THERE IS NO ERROR IN PRICE TO VALUE
                 *
                 */
                $requestInfo['price_to'] = (int)$request->get('price_to');
            }
        }

        /**
         *
         * GETTING INFO AND RETURN
         *
         */
        if (count($errors) === 0) {
            $products = school_courses::orderBy($requestInfo['order_by_what'], $requestInfo['order_by_how'])->offset($requestInfo['count'] * ($requestInfo['page'] - 1))->limit($requestInfo['count'] * $requestInfo['page'])->where('price_irr', ">=", $requestInfo['price_from'])->where('price_irr', "<=", $requestInfo['price_to'])->where('cat_id' , '=' , $request->get('cat_id'))->where('status', '=', 1)->get();
            $products2 = school_courses::orderBy($requestInfo['order_by_what'], $requestInfo['order_by_how'])->where('price_irr', ">=", $requestInfo['price_from'])->where('price_irr', "<=", $requestInfo['price_to'])->where('cat_id' , '=' , $request->get('cat_id'))->where('status', '=', 1)->get();
            $products->count = count($products2);
            return response()->json([
                'message' => 'date received successfully.',
                'count' =>     $products->count,
                'success' => $products
            ]);
        }
    }

    public function showAllEcommerceProducts(Request $request)
    {
        $errors = [];
        $requestInfo = [
            'page' => 1,
            'count' => 32,
            'order_by_what' => 'created_at',
            'order_by_how'  => 'DESC',
            'price_from'    => 0,
            'price_to'      => 99999999999999999999999999999999999999999999999999999999999999999999999999999999999,
            'brand_limit'  => [],
            'category_limit' => []
        ];

        /**
         *
         * check request has brand limit or no
         *
         */
        if (
            $request->has('brands')
            &&
            !empty($request->get('brands'))
        ) {
            /**
             *
             * request has brand limit
             *
             */
            $requestInfo['brand_limit'] = explode(',', $request->get('brands'));
        }

        /**
         *
         * if request hasn't brand limit
         *
         */
        else {
            /**
             *
             * request doesn't have brand limit
             *
             */
            $requestInfo['brand_limit'] = ecommerce_products::all('brand_id');
        }

        /**
         *
         * check request has category limit or no
         *
         */
        if (
            $request->has('categories')
            &&
            !empty($request->get('categories'))
        ) {
            /**
             *
             * request has categories limit
             *
             */
            $requestInfo['category_limit'] = explode(',', $request->get('categories'));
            foreach (categories::find($requestInfo['category_limit']) as $ind => $val){
                foreach (categories::where('sub_cat_of' , '=' , $val->id)->get() as $index => $value){
                    $requestInfo['category_limit'][] = $value->id;
                }
            }
        }

        /**
         *
         * if request hasn't categories limit
         *
         */
        else {
            /**
             *
             * request doesn't have categories limit
             *
             */
            $requestInfo['category_limit'] = ecommerce_products::all('category');
        }

        /**
         *
         * CHECK PAGE NUMBER BE TRUE
         *
         */

        if ($request->has('page')) {
            if (
                !is_null(
                    (int)$request->get('page')
                )
            ) {
                /**
                 *
                 * PAGE NUMBER IS VALID
                 *
                 */
                $requestInfo['page'] = (int)$request->get('page');
            }
        }

        /**
         *
         * CHECK REQUEST COUNT BE CORRECT
         *
         */
        if ($request->has('count')) {
            if (
                !is_null(
                    (int)$request->get('count')
                )
            ) {
                /**
                 *
                 * COUNT OF PRODUCTS IN PAGE OS CORRECT
                 *
                 */
                $requestInfo['count'] = (int)$request->get('count');
            }
        }

        /**
         *
         * CHECK ORDER BY STATUS CHANGING
         *
         */
        if ($request->has('order_by')) {
            /**
             *
             * THERE IS ORDER BY VALUE IN REQUEST
             *
             * ORDER BY VALUES:
             *
             * 1: NEWEST
             *
             * 2: OLDEST
             *
             * 3: CHEAPEST
             *
             * 4: MOST EXPENSIVE
             *
             * 5: MOST SOLD
             *
             * 6: MOST POPULAR
             *
             */

            if ((int)$request->get('order_by') > 6 || (int)$request->get('order_by') < 1) {
                /**
                 *
                 * ENTERED ORDER BY VALUE IS INCORRECT
                 *
                 */
                $errors['order_by'][] = 'entered order by value is invalid.';
            } elseif ((int)$request->get('order_by') === 1) {
                /**
                 *
                 * ORDER BY NEWEST
                 *
                 */
                $requestInfo['order_by_what'] = 'created_at';
                $requestInfo['order_by_how']  = 'DESC';
            } elseif ((int)$request->get('order_by') === 2) {
                $requestInfo['order_by_what'] = 'created_at';
                $requestInfo['order_by_how']  = 'ASC';
            } elseif ((int)$request->get('order_by') === 3) {
                $requestInfo['order_by_what'] = 'price_irr';
                $requestInfo['order_by_how']  = 'ASC';
            } elseif ((int)$request->get('order_by') === 4) {
                $requestInfo['order_by_what'] = 'price_irr';
                $requestInfo['order_by_how']  = 'DESC';
            } elseif ((int)$request->get('order_by') === 5) {
                $requestInfo['order_by_what'] = 'created_at';
                $requestInfo['order_by_how']  = 'DESC';
            } elseif ((int)$request->get('order_by') === 6) {
                $requestInfo['order_by_what'] = 'created_at';
                $requestInfo['order_by_how']  = 'DESC';
            }
        }
        /**
         *
         * CHECK PRICE LIMIT
         *
         */
        if ($request->has('price_from')) {
            /**
             *
             * THERE IS PRICE FROM VALUE IN REQUEST
             *
             */
            if ((int)$request->get('price_from') === null) {
                /**
                 *
                 * ENTERED PRICE FROM IS NOT A NUMBER
                 *
                 */
                $errors['price_from'][] = 'entered price from value is incorrect.';
            } elseif ((int)$request->get('price_from') < 0) {
                /**
                 *
                 * PRICE FROM MUST BE MINIMUM 0
                 *
                 */
                $errors['price_from'][] = 'price from value mist be more than 0.';
            } else {
                /**
                 *
                 * THERE IS NO ERROR IN PRICE FROM VALUE
                 *
                 */
                $requestInfo['price_from'] = (int)$request->get('price_from');
            }
        }
        if ($request->has('price_to')) {
            /**
             *
             * THERE IS PRICE TO VALUE IN REQUEST
             *
             */
            if ((int)$request->get('price_to') === null) {
                /**
                 *
                 * ENTERED PRICE TO IS NOT A NUMBER
                 *
                 */
                $errors['price_to'][] = 'entered price to value is incorrect.';
            } else {
                /**
                 *
                 * THERE IS NO ERROR IN PRICE TO VALUE
                 *
                 */
                $requestInfo['price_to'] = (int)$request->get('price_to');
            }
        }

        /**
         *
         * GETTING INFO AND RETURN
         *
         */
        if (count($errors) < 1) {
            $products   = ecommerce_products::orderBy($requestInfo['order_by_what'], $requestInfo['order_by_how'])->offset($requestInfo['count'] * ($requestInfo['page'] - 1))->limit($requestInfo['count'])->where('price_irr', ">=", $requestInfo['price_from'])->where('price_irr', "<=", $requestInfo['price_to'])->where('status', '=', 1)->whereIn('brand_id', $requestInfo['brand_limit'])->whereIn('category', $requestInfo['category_limit'])->where('stock', '<>', 0)->get();
            $products2  = ecommerce_products::orderBy($requestInfo['order_by_what'], $requestInfo['order_by_how'])->where('price_irr', ">=", $requestInfo['price_from'])->where('price_irr', "<=", $requestInfo['price_to'])->where('status', '=', 1)->whereIn('brand_id', $requestInfo['brand_limit'])->whereIn('category', $requestInfo['category_limit'])->where('stock', '<>', 0)->get();
            $products->count = count($products2);
            return response()->json([
                'message' => 'date received successfully.',
                'count' =>     $products->count,
                'success' => [
                    $products,
                ],
            ]);
        }
    }

    public function showAllSubCategoriesOfCategory(public_showAllSubuCategoriesOfCategory $request)
    {
        $errors = [];

        /**
         *
         * CHECK CATEGORY ID BE CORRECT
         *
         */
        if (
            is_null(
                categories::find($request->get('cat_id'))
            )
        ) {
            /**
             *
             * ENTERED CATEGORY ID IS INCORRECT
             *
             */
            $errors['cat_id'][] = 'entered category id is incorrect.';
        }

        /**
         *
         * SHOW RESULTS IF THERE AREN'T ANY ERRORS
         *
         */
        if (count($errors) < 1) {
            /**
             *
             * NO ERROR :)
             *
             */
            $allSubCats = categories::where('sub_cat_of', '=', $request->get('cat_id'))->get();
            return response()->json([
                'message' => 'all sub categories received from database to show.',
                'success' => $allSubCats
            ]);
        }
        return response()->json([
            'message' => 'entered data was invalid.',
            'errors' => $errors
        ], 401);
    }

    public function showAllParentsOfCategory(public_getCatInfo $request)
    {
        $errors = [];
        /**
         *
         * CHECK CATEGORY ID BE CORRECT
         *
         */
        if (
            is_null(
                categories::find($request->get('cat_id'))
            )
        ) {
            /**
             *
             * ENTERED CATEGORY ID IS INCORRECT
             *
             */
            $errors['cat_id'][] = 'entered category id is incorrect.';
        }

        /**
         *
         * GETTING INFORMATION AND SHOWING IT IF THERE AREN'T ANY ERRORS
         *
         */
        if (count($errors) < 1) {
            $allCategories = [];
            $cat_id = $request->get('cat_id');
            $cat = categories::find($cat_id);
            $cat_name = $cat->cat;
            array_unshift($allCategories, ["cat" => $cat_name, 'id' => $request->get('cat_id')]);
            if ($cat->sub_cat_of !== null) {
                $cat_of = $cat->sub_cat_of;
                while (
                    $cat_of !== null
                ) {
                    $cat_of_info = categories::find($cat_of);
                    array_unshift($allCategories, ["cat" => $cat_of_info->cat, 'id' => $cat_of]);
                    $cat_of = categories::find($cat_of)->sub_cat_of;
                }
            }
            return response()->json([
                'message' => 'all categories received successfully.',
                'success' => [
                    'cat_type' => $cat->cat_of,
                    'categories' => $allCategories
                ]
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors'  => $errors
        ], 401);
    }

    public function productInfo(show_ecommerce_product_info $request)
    {
        $product = ecommerce_products::Accepted()->where('id', $request->get('product_id'))->first();

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

    public function showProductInfo(show_ecommerce_product_info $request)
    {
        $errors = [];

        /**
         *
         * CHECK PRODUCT ID BE CORRECT
         *
         */
        if (
            is_null(
                ecommerce_products::find($request->get('product_id'))
            )
        ) {
            /**
             *
             * INVALID PRODUCT ID
             *
             */
            $errors['product_id'][] = 'entered product id is incorrect.';
        }

        /**
         *
         * SHOW RESPONSE IF THERE AREN'T ANY ERRORS
         *
         */
        if (count($errors) < 1) {
            /**
             *
             * NO ERROR :)
             *
             */

            $product_info = ecommerce_products::find($request->get('product_id'));
            $product_info->gallery = $product_info->productImages;
            $product_info->features = $product_info->productFeatures;
            $product_info->colors = $product_info->showColors;
            $product_info->important_features = $product_info->important_features;

            return response()->json([
                'message' => 'product information received from database successfully.',
                'success' => $product_info
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors'  => $errors
        ]);
    }

    public function showAllColorsOfProduct(show_product_colors $request)
    {
        $errors = [];

        /**
         *
         * SHOW CHECK PRODUCT ID BE CORRECT
         *
         */
        if (
            is_null(
                ecommerce_products::find($request->get('product_id'))
            )
        ) {
            /**
             *
             * INVALID PRODUCT ID
             *
             */
            $errors['product_id'][] = 'entered product id is invalid.';
        }

        /**
         *
         * SHOW RESPONSE IF THERE AREN'T ANY ERRORS
         *
         */
        if (count($errors) < 1) {
            /**
             *
             * NO ERROR
             *
             */

            $productColors = ecommerce_products::find($request->get('product_id'))->showColors();
            return response()->json([
                'message' => 'data received successfully.',
                'success' => $productColors
            ]);
        }
    }

    public function showCourseDetails(show_course_details $request)
    {
        $errors = [];

        /**
         *
         * CHECK COURSE ID BE CORRECT
         *
         */
        if (
            is_null(
                school_courses::find($request->get('course_id'))
            )
        ) {
            /**
             *
             * INVALID COURSE IF
             *
             */
            $errors['course_id'][] = 'entered course is invalid.';
        }

        /**
         *
         * SHOW INFORMATION OF COURSE IF THERE AREN'T ANY ERRORS
         *
         */
        if (count($errors) < 1) {
            /**
             *
             * NO ERROR :)
             *
             */

            $courseInformation = school_courses::find($request->get('course_id'));
            return response()->json([
                'message' => 'course information received from database successfully.',
                'success' => $courseInformation
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors'  => $errors
        ]);
    }

    public function blog_articles_list(Request $request)
    {
        $errors = [];
        $requestInfo = [
            'page'  => 1,
            'count' => 36
        ];

        /**
         *
         * check to edit count of results if has been sent in request
         *
         */
        if (
            $request->has('count')
        ) {
            /**
             *
             * set count of results if has been sent in request
             *
             */
            if ((int)$request->get('count') == $request->get('count'))
                $requestInfo['count'] = abs($request->get('count'));
        }
        /**
         *
         * check to edit page of results if has been sent in request
         *
         */
        if (
            $request->has('page')
        ) {
            /**
             *
             * set page of results if has been sent in request
             *
             */
            if ((int)$request->get('page') == $request->get('page'))
                $requestInfo['page'] = abs($request->get('page'));
        }

        /**
         *
         * show results if there aren't any errors
         *
         */
        if (
            count($errors) === 0
        ) {
            # no error
            $articles = articles::all();
            foreach ($articles as $ind => $val) {
                $owner = User::find($val->user_id);
                $ownerInfo = [
                    'fullName' => $owner->firstName . ' ' . $owner->lastName
                ];
                $articles->user = $ownerInfo;
            }
        }
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors'  => $errors
        ], 401);
    }

    public function showArticle(showArticle $request)
    {
        $errors = [];

        /**
         *
         * check article id be valid
         *
         */
        if (
            !articles::find($request->get('article_id'))
        ) {
            $errors['article_id'][] = 'entered article id is invalid.';
        }

        /**
         *
         * show result if there aren't any errors
         *
         */
        if (
            count($errors) === 0
        ) {
            # no error

            $article = articles::find($request->get('article_id'));
            $owner = User::find($article->user_id);
            $article->user = [
                'firstName' => $owner->firstName,
                'lastName'  => $owner->lastName,
            ];
        }
    }

    public function showConsultantsList(Request $request)
    {
        $errors = [];
        $requestInfo = [
            'count' => 30,
            'page'  => 1,
            'consultant' => ''
        ];

        /**
         *
         * check if list of consultants for limiting be sent, show resaults
         *
         */
        if (
            $request->has('consultants')
            &&
            !empty($request->has('consultants'))
        ) {
            /**
             *
             * there is list of consultants in request
             *
             */
            $consultantsListForLimit = str_split('|', $request->get('consultants'));
        } else {
            /**
             *
             * there isn't limit for result
             *
             */
            $consultantsListForLimit = consultant::all('user_id');
        }

        /**
         *
         * check to edit count of results if has been sent in request
         *
         */
        if (
            $request->has('count')
        ) {
            /**
             *
             * set count of results if has been sent in request
             *
             */
            if ((int)$request->get('count') == $request->get('count'))
                $requestInfo['count'] = abs($request->get('count'));
        }
        /**
         *
         * check to edit page of results if has been sent in request
         *
         */
        if (
            $request->has('page')
        ) {
            /**
             *
             * set page of results if has been sent in request
             *
             */
            if ((int)$request->get('page') == $request->get('page'))
                $requestInfo['page'] = abs($request->get('page'));
        }

        /**
         *
         * show results if there aren't any errors
         *
         */
        if (count($errors) === 0) {
            # no error

            // Get list of all consultants and applying limits
            $consultants = consultant::whereIn(
                'user_id',
                $consultantsListForLimit
            )->skip(
                $requestInfo['count'] * ($requestInfo['page'] - 1)
            )->limit(
                $requestInfo['count']
            )->get();

            $consultants = consultant::whereIn(
                'user_id',
                $consultantsListForLimit
            )->skip(
                $requestInfo['count'] * ($requestInfo['page'] - 1)
            )->limit(
                $requestInfo['count']
            )->get();

            /**
             *
             * adding additional information to consultants  information
             *
             */
            foreach ($consultants as $ind => $val) {
                $consultants[$ind]->important_features;
            }

            // show response
            return response()->json([
                'message' => 'list of consultants received successfully',
                'success' => $consultants
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors'  => $errors
        ], 401);
    }

    public function consultant_info()
    {
        $errors = [];
    }
}

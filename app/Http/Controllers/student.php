<?php

namespace App\Http\Controllers;

use App\Http\Requests\addCourseToCart;
use App\Http\Requests\addNewAddress;
use App\Http\Requests\Api\Lesson\StoreCommentRequest;
use App\Http\Requests\showAddress;
use App\Http\Requests\show_school_cart;
use App\Http\Requests\checkCoupon;
use App\Http\Requests\track_order;
use App\Http\Requests\show_address_list;
use App\Http\Requests\add_course_to_cart;
use App\Http\Requests\verify_transaction;
use App\Http\Resources\LessonResource;
use App\Http\Resources\OneLessonResource;
use App\Models\Lesson;
use App\Models\LessonPurchased;
use App\Models\consultants;
use App\Models\purchases_courses;
use App\Models\tmp_codes;
use Shetabit\Payment\Facade\Payment;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use App\Http\Requests\addProductIntoEcommerceCard;
use App\Http\Requests\addProductIntoSchoolCard;
use App\Http\Requests\get_user_cart_items;
use App\Http\Requests\get_orders_list;
use App\Http\Requests\askNewQuestionForCourse;
use App\Http\Requests\removeCourseFromCart;
use App\Http\Requests\removeItemFromEcommerceCart;
use App\Http\Requests\removeUserAddress;
use App\Http\Requests\student_add_comment_to_course;
use App\Http\Requests\student_answer_question;
use App\Http\Requests\student_get_invoice_ecommerce;
use App\Models\addresses;
use App\Models\ecommerce_basket;
use App\Models\ecommerce_products;
use App\Models\order_products_ecommerce;
use App\Models\orders;
use App\Models\school_baskets;
use App\Models\school_order;
use App\Models\skyroom_users;
use App\Models\questionAndAnswer_answers;
use App\Models\questionAndAnswer_questions;
use App\Models\school_basket;
use App\Models\school_courses;
use App\Models\shipping_methods;
use App\Models\User;
use Illuminate\Http\Request;
use Shetabit\Multipay\Invoice;
use App\Http\Controllers\purchase;
use App\Http\Controllers\jdate;

class student extends Controller
{
    public $apiKey = 'apikey-279361-212-fb04f8356b4560d7225f283ac94b7c4a';
    public $apiUrl = 'https://www.skyroom.online/skyroom/api/';
    public $apiLink= 'https://www.skyroom.online/skyroom/api/apikey-279361-212-fb04f8356b4560d7225f283ac94b7c4a';


    public function askQuestionForCourse(askNewQuestionForCourse $request){
        $errors = [];
        $this->middleware('student');

        /**
         *
         * CHECK COURSE ID BE VALID
         *
         */
        if(
        ! school_courses::find($request->get('course_id'))->where('status' , '='  , 1)->first()
        ){
            /**
             *
             * INVALID COURSE ID
             *
             */
            $errors['course_id'][] = 'entered course id is invalid.';
        }

        /**
         *
         * ADD QUESTION IF THERE AREN'T ANY ERRORS
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR :)
             *
             */
            $question = school_courses::find($request->get('course_id'))->courseQuestions()->create([
                'user_id' => User::where('token' , '=' , $request->get('token'))->first()->id ,
                'question'=> htmlspecialchars($request->get('question'))
            ]);
            return response()->json([
                'message' => 'question asked successfully.' ,
                'success' => $question
            ]);
        }

        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors' => $errors
        ] , '401');

    }

    public function answerQuestion(student_answer_question $request){
        $errors = [];

        /**
         *
         * CHECK QUESTION ID BE CORRECT
         *
         */
        if(
        is_null(
            questionAndAnswer_questions::find($request->get('question_id'))->where('status' , '=' , 1)->first()
        )
        ){
            /**
             *
             * QUESTION ID IS INCORRECT
             *
             */
            $errors['question_id'][] = 'entered question id is incorrect.';
        }

        /**
         *
         * ADD ANSWER IF THERE AREN'T ANY ERRORS
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR :)
             *
             */
            $addAnswer = new questionAndAnswer_answers();
            $addAnswer->answer = htmlspecialchars($request->get('answer'));
            $addAnswer->q_id = (int)htmlspecialchars($request->get('question_id'));
            $addAnswer->save();

            return response()->json([
                'message' => 'answer shared successfully.' ,
                'success' => $addAnswer
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors' => $errors
        ] , 401);

    }

    public function addCommentToCourse(student_add_comment_to_course $request){
        $errors = [];
        /**
         *
         * CHECK COURSE ID BE VALID
         *
         */
        if(
            count(
                school_courses::find($request->get('course_id'))->where('status' , '='  , 1)->get()
            ) === 0
        ){
            /**
             *
             * INVALID COURSE ID
             *
             */
            $errors['course_id'][] = 'entered course id is invalid.';
        }

        /**
         *
         * ADD COMMENT IF THERE AREN'T ANY ERRORS
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR :)
             *
             */
            $addComment = school_courses::find($request->get('course_id'))->courseComments()->create([
                'comment' => htmlspecialchars($request->get('comment')) ,
                'user_id' => User::where('token' , '=' , $request->get('token'))->first()->id
            ]);
            return response()->json([
                'message' => 'comment added successfully.' ,
                'success' => $addComment
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors' => $errors
        ]);
    }

    public function addCommentToLesson(StoreCommentRequest $request)
    {
        $lesson = Lesson::findOrFail($request->get('lesson'));

        $user = $request->get('user');

        $comment = $lesson->comments()->create([
            'comment' => htmlspecialchars($request->get('comment')),
            'user_id' => $user->id
        ]);

        return response()->json([
            'message' => 'comment added successfully.',
            'success' => $comment
        ]);
    }

    public function addProductIntoEcommerceCart(addProductIntoEcommerceCard $request)
    {
        try{

            $user = $request->get('user');
            /**
             *
             * CHECK PRODUCT ID BE CORRECT
             *
             */
            $product = ecommerce_products::accepted()->where('id', $request->get('product_id'))->first();
            if (!$product)
                return response()->json([
                    'message' => 'entered data is invalid.',
                    'errors' => 'entered product id is incorrect.'
                ], 401);

            /**
             *
             * CHECK PRODUCT BE IN STOCK
             *
             */
            if ($product->stock === 0)
                return response()->json([
                    'message' => 'entered data is invalid.',
                    'errors' => 'entered product id isn\'t in stock.'
                ], 401);

            # Store products types in variables
            $types = $product->type == 0 ? $product->colors : $product->sizes;
            $types = $types->pluck('id')->toArray();
            $type = $product->type == 0 ? "color" : "size";
            /**
             *
             * NO ERRORS
             *
             */
            $data = $request->get('more_info') ?: null;
            # If products does'nt have types data will be null
            count($types) == 0 && $data = null;
            # increase count of product in cart if there is in user's database
            $bascket = $user->showCartItems()->where('product_id', $product->id)->where('more_info', is_null($data) ? null : json_encode($data))->first();
            # Check if products has types, user can not buy without sending types
            if (($types && count($types) > 0) && ($data == null || !in_array(json_decode($data, true)[$type], $types)))
                return response()->json([
                    'message' => 'product type is not correct',
                    'status'  => 403
                ]);

            if (!$bascket){
                /**
                 *
                 * USER HADN'T THIS PRODUCT IN HIS CART, ADDING NOW
                 *
                 */
                $bascket = $user->showCartItems()->create([
                    'product_id'    =>  $product->id,
                    'more_info'     =>  is_null($data) ? null : json_encode($data)
                ]);
                return response()->json([
                    'message' => 'product added to cart successfully',
                    'success' => $bascket
                ]);
            }
            if ($product->stock > $bascket->count) {
                /**
                 *
                 * ADD ONE TO COUNT OF PRODUCT IN BASKET
                 *
                 */
                $bascket->update([
                    'count' =>  $bascket->count + 1
                ]);

                return response()->json([
                    'message' => 'product added to cart successfully',
                    'success' => $bascket
                ]);

            }

            return response()->json([
                'message' => 'product stock is not enough',
                'status'  => 403
            ]);

        }catch (\Exception $exception){
            return response()->json([
                'message' => 'Please tray again later',
                'status'  => 403
            ]);
        }
    }



    public function removeItemFromEcommerceCart(removeItemFromEcommerceCart $request){
        $errors = [];

        /**
         *
         * CHECK IS PRODUCT ID CORRECT
         *
         */
        if(
        !ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)
            ->where('product_id' , '=' , $request->get('item_id'))->first()
        ){
            /**
             *
             * PRODUCT ID IS INCORRECT
             *
             */
            $errors['item_id'][] = 'you haven\'t this product in your basket.';
        }

        /**
         *
         * IF THERE AREN'Y ANY ERRORS
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR :)
             *
             */
            if(
                ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->where('product_id' , '=' , $request->get('item_id'))->first()->count > 1
            ){
                /**
                 *
                 * MAKE COUNT - 1
                 *
                 */
                $productInBasket = ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->where('product_id' , '=' , $request->get('item_id'))->first();
                $productInBasket->count = (int)ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->where('product_id' , '=' , $request->get('item_id'))->first()->count -1 ;
                $productInBasket->save();

            }elseif(
                (int)ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->where('product_id' , '=' , $request->get('item_id'))->first()->count === 1
            ){
                /**
                 *
                 * REMOVE FROM BASKET
                 *
                 */
                $productInBasket = ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->where('product_id' , '=' , $request->get('item_id'))->first();
                $productInBasket->delete();

            }

            return response()->json([
                'message' => 'product removed successfully.' ,
                'success' => $productInBasket
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => (object)$errors
        ] , 401);

    }

    public function deleteItemFromEcommerceCart (removeItemFromEcommerceCart $request){
        $errors = [];

        /**
         *
         * CHECK IS PRODUCT ID CORRECT
         *
         */
        if(
        !ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)
            ->where('product_id' , '=' , $request->get('item_id'))->first()
        ){
            /**
             *
             * PRODUCT ID IS INCORRECT
             *
             */
            $errors['item_id'][] = 'you haven\'t this product in your basket.';
        }

        /**
         *
         * IF THERE AREN'Y ANY ERRORS
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR :)
             *
             */
            /**
             *
             * REMOVE FROM BASKET
             *
             */
            $productInBasket = ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->where('product_id' , '=' , $request->get('item_id'))->first();
            $productInBasket->delete();


            return response()->json([
                'message' => 'product removed successfully.' ,
                'success' => $productInBasket
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => (object)$errors
        ] , 401);


    }

    public function addSchoolProductIntoCart(addCourseToCart $request)
    {
        $user = User::where('token' , $request->get('token'))->first();
        if (!$user)
            return response([
                'message'   =>  'User not found.',
                'status'    =>  404
            ], 404);

        $course = school_courses::where('id', $request->get('course_id'))->where('status', school_courses::ACTIVE)->first();

        if (!$course)
            return response()->json([
                'message' => 'entered data is invalid.',
                'status' => 401
            ], 401);

        $purches = $user->coursePurched()->where('course_id', $request->get('course_id'))->count();

        if ($purches)
            return response([
                'message' => 'User already bought course',
                'status' => 401
            ], 401);

        $bascket = school_basket::where('user_id', $user->id)->where('productable_type', school_courses::class)->where('productable_id', $request->get('course_id'))->count();

        if (!$bascket) {
            $bascket = $user->basckets()->create([
                'productable_type' => school_courses::class,
                'productable_id' => $request->get('course_id')
            ]);

            return response()->json([
                'message' => 'product added to cart successfully.',
                'success' => $bascket
            ]);
        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)[
                'course_id' => [
                    'entered product is in cart'
                ]
            ]
        ], 401);
    }

    public function removeCourseFromCart(removeCourseFromCart $request){
        $errors = [];

        /**
         *
         * CHECK PRODUCT ID BE CORRECT
         *
         */
        if(
            count(school_courses::find($request->get('course_id'))->where('status' , '=' , 1)->get()) === 0
        ){
            /**
             *
             * PRODUCT ID IS INCORRECT
             *
             */
            $errors['course_id'][] = 'entered course id is incorrect.';
        }

        /**
         *
         * CHECK USER HAVE PRODUCT IN HIS/HER CART
         *
         */
        elseif(
            count(
                school_basket::where('productable_id' , '=' , $request->get('course_id'))->where('productable_type' , '=' , 'App\Models\school_courses')->where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->get()
            ) === 0
        ){
            /**
             *
             * PRODUCT ISN'T IN USER CART
             *
             */
            $errors['course_id'][] = 'entered product isn\'t in cart.';
        }

        /**
         *
         * REMOVE COURSE FROM USER CART IF THERE AREN'T ANY ERRORS
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR :)
             *
             */
            $courseInCart = school_basket::where('productable_id' , '=' , $request->get('course_id'))->where('productable_type' , '=' , 'App\Models\school_courses')->where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->first();
            $courseInCart->delete();
            return response()->json([
                'message' => 'product deleted from cart successfully.'
            ]);

        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ] , 401);

    }

    public function removeLessonFromCart(Request $request){

        $user = User::where('token', $request->get('token'))->first();
        if (!$user){
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }
        $lesson = Lesson::find($request->lesson);
        if (!$lesson){
            return response()->json([
                'message' => 'Lesson not found.'
            ], 404);
        }
        $bascket = school_basket::where('productable_id', $request->get('lesson'))->where('productable_type', Lesson::class)->where('user_id' , $user->id)->first();
        if (!$bascket){
            return response()->json([
                'message' => 'entered lesson isn\'t in cart.'
            ], 404);
        }
        $bascket->delete();
        return response()->json([
            'message' => 'lesson deleted from cart successfully.'
        ]);

    }

    public function addNewAddress (addNewAddress $request)
    {
        $errors = [];

        /**
         *
         * CHECK FORMAT OF POSTAL CODE
         *
         */
        if (
            (int)$request->get('postcode') != $request->get('postcode')
        ) {
            /**
             *
             * POST CODE IS INCORRECT
             *
             */
            $errors['postcode'][] = 'entered post code is incorrect.';
        }

        /**
         *
         * ADD ADDRESS IF THERE AREN'T ANY ERROR
         *
         */

        if (
            count($errors) < 1
        ) {
            /**
             *
             * NO ERROR :)
             *
             */

            $address = new addresses();
            $address->firstName = htmlspecialchars($request->get('firstName'));
            $address->lastName = htmlspecialchars($request->get('lastName'));
            $address->national_code = htmlspecialchars($request->get('national_code'));
            $address->receiver_phone_number = htmlspecialchars($request->get('phone_number'));
            $address->province = htmlspecialchars($request->get('province'));
            $address->city = htmlspecialchars($request->get('city'));
            $address->address = htmlspecialchars($request->get('address'));
            $address->postcode = htmlspecialchars($request->get('postcode'));
            $address->user_id = (int)User::where('token' , '=' , $request->get('token'))->first()->id;
            $address->save();

            return response()->json([
                'message' => 'address added successfully.',
                'success' => $address
            ]);
        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => $errors
        ] , 401);

    }

    public function removeAnAddress (removeUserAddress $request){
        $errors = [];
        $this->middleware('student');

        /**
         *
         * CHECK ADDRESS IF BE CORRECT
         *
         */
        if(
        is_null(
            addresses::find($request->get('addr_id'))
        )
        ){
            /**
             *
             * ENTERED ADDRESS ID IS INCORRECT
             *
             */
            $errors['addr_id'][] = 'entered addr id is invalid.';
        }

        /**
         *
         * CHECK THIS USER BE OWNER OF THIS ADDRESS
         *
         */
        elseif(
            (int)addresses::find($request->get('addr_id'))->user_id !== User::where('token' , '=' ,$request->get('token'))->first()->id
        ){
            /**
             *
             * THIS USER ISN'T OWNER OF VIDEO
             *
             */
            $errors['addr_id'][] = 'you can\'t remove this address, because isn\'t your address.';
        }

        /**
         *
         * DELETE ADDRESS IF THERE AREN'T ANY PROBLEMS
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR :)
             *
             */

            $address = addresses::find($request->get('addr_id'))->delete();

            return response()->json([
                'message' => 'address deleted successfully.'
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => (object)$errors
        ] , 401);
    }

    public function showAddressList (show_address_list $request){
        $errors = [];

        /**
         *
         * CHECK TOKEN BE CORRECT
         *
         */
        if(
            count(
                User::where('token' , '=' , $request->get('token'))->get()
            ) === 0
        ){
            /**
             *
             * ENTERED TOKEN IS INVALID
             *
             */
            $errors['token'][] = 'entered token is invalid.';
        }

        /**
         *
         * SHOW ADDRESS LIST IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR :)
             *
             */
            $userAddresses = User::where('token' , '=' , $request->get('token'))->first()->showAddresses;
            return response()->json([
                'message' => 'list of addresses received successfully.' ,
                'success' => $userAddresses
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors' => $errors
        ] , 401);
    }

    public function getInvoice(student_get_invoice_ecommerce $request){
        $errors = [];

        /**
         *
         * CHECK CART DOESN'T BE EMPTY
         *
         */
        if(
            count(
                ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->get()
            ) === 0
        ){
            /**
             *
             * CART IS EMPTY
             *
             */
            $errors['cart'][] = 'cart is empty.';
        }


        /**
         *
         * CHECK SHIPPING METHOD BE CORRECT
         *
         */
        if (
            is_null(
                shipping_methods::find($request->get('shipping_method'))
            )
        ){
            /**
             *
             * SHIPPING ADDRESS IS INVALID
             *
             */
            $errors['shipping_method'][] = 'entered shipping method is invalid.';
        }

        /**
         *
         * CHECK SHIPPING ADDRESS BE CORRECT AND BELONGS TO USER
         *
         */
        if(
            is_null(
                addresses::find($request->get('shipping_address'))
            )
            ||
            addresses::find($request->get('shipping_address'))->user_id != User::where('token' , '=' , $request->get('token'))->first()->id
        ){
            /**
             *
             * ENTERED ADDRESS ID IS INVALID OR DOESN'T BELONG TO THIS USER
             *
             */
            $errors['shipping_address'][] = 'entered shipping address is invalid.';
        }



        /**
         *
         * check all products be ready
         *
         */
        foreach (
            ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->get()
            as $ind => $val
        ){
            if(ecommerce_products::find($val->product_id)->stock < $val->count){
                $errors['cart'][] = 'product "'.ecommerce_products::find($val->product_id)->product_name . '" isn\'t available in this count you want.' ;
                $itemInCart = ecommerce_basket::where('user_id', '=', User::where('token', '=', $request->get('token'))->first()->id)->first();
//                return $itemInCart;
                if(ecommerce_products::find($val->product_id)->stock != 0) {
                    $itemInCart->count = ecommerce_products::find($val->product_id)->stock;
                }else {
                    $itemInCart->count = 0;
                }
                $itemInCart->save();
            }
        }

        /**
         *
         * COMPLETE ORDER IF THERE AREN'T ANY ERRORS
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR :)
             *
             */

            $totalPrice = 0;
            $finalPrice = shipping_methods::find($request->get('shipping_method'))->price_irr;

            foreach (
                ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->get()
                as $index => $value
            ) {
                /**
                 *
                 * CALCULATE TOTAL PRICE
                 *
                 */
                $totalPrice += ((int)$value->getProduct->price_irr * (int)$value->count);
            }

            foreach (
                ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->get()
                as $index => $value
            ){
                /**
                 *
                 * CALCULATE FINAL PRICE
                 *
                 */
                $finalPrice += ((int)$value->getProduct->price_irr_after_off * (int)$value->count);
            }

            $insertOrder = new orders();
            $insertOrder->user_id = (int)User::where('token' , '=' , $request->get('token'))->first()->id;
            $insertOrder->total_price = $totalPrice;
            $insertOrder->final_price = $finalPrice;
            $insertOrder->address     = $request->get('shipping_address');
            $insertOrder->save();


            foreach (
                ecommerce_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->get()
                as $index => $value
            ) {
                /**
                 *
                 * ADDING CART PRODUCTS TO DATABASE
                 *
                 */
                $insertOrderProducts = new order_products_ecommerce();
                $insertOrderProducts->order_id = $insertOrder->id;
                $insertOrderProducts->product_id = $value->product_id;
                $insertOrderProducts->count = $value->count;
                $insertOrderProducts->data = $value->more_info;
                $insertOrderProducts->save();
            }
//            $invoice = new invoice();
//            $invoice->detail([
//                'amount' => $finalPrice,
//                'order_id' => $insertOrder->id ,
//                'phone' => User::where('token' , '=' , $request->get('token'))->first()->phone_number ,
//                'callback' => 'https://khiyal.art/api/ecommerce/payment/verify'
//            ]);
//            return $finalPrice;


            return Payment::callbackUrl('https://api.khiyal.art/api/ecommerce/payment/verify')->purchase(
                (new Invoice)->amount((int)$finalPrice),
                function($driver, $transactionId) use($insertOrder){
                    $insertOrder->transaction_id = $transactionId;
                    $insertOrder->save();
                }
            )->pay()->toJson();


//            return Payment::purchase($invoice,
//                function($driver, $transactionId) {
//                    global $insertOrder;
//                    $order = orders::find($insertOrder->id);
//                    $order->transaction_id = $transactionId;
//                    $order->save();
//                })->pay()->toJson();



        }

        return response()->json([
            'message' => 'entered data was invalid.' ,
            'errors'  => $errors
        ] , 401);

    }

    public function make_school_order(Request $request)
    {
        $errors = [];
        /**
         *
         * CHECK TOKEN BE CORRECT
         *
         */
        if (
            count(
                User::where('token', '=', $request->get('token'))->get()
            ) === 0
        ) {
            /**
             *
             * ENTERED TOKEN IS INVALID
             *
             */
            $errors['token'][] = 'entered token is invalid.';
        } /**
         *
         * CHECK CART DOESN'T BE EMPTY
         *
         */
        elseif (
            count(
                school_basket::where(
                    'user_id', '=', User::where('token', '=', $request->get('token'))->first()->id)->get()
            ) === 0
        ) {
            /**
             *
             * CART OF SCHOOL IS EMPTY
             *
             */
            $errors['cart'][] = 'cart is empty.';
        }

        if (count($errors) < 1) {
            /**
             *
             * NO ERROR :)
             *
             */
            $user = User::where('token', '=', $request->get('token'))->firstOrFail();

            $productsInCart = school_basket::where('user_id', $user->id)->get();
            $coursesInCart = school_basket::where('user_id', $user->id)->where('productable_type', school_courses::class)->get();
            $lessonInCart = $productsInCart->where('productable_type', Lesson::class);
            $totalPrice = 0;
            $notFreeCourses = 0;
            $finalPrice = 0;

            /**
             *
             * CALCULATE TOTAL PRICE AND FINAL OF COURSES
             *
             */
            foreach ($coursesInCart as $ind => $val) {
                $course = school_courses::find($val->productable_id);
                $totalPrice += $course->price_irr;
                if ($course->irr_price_after_off != 0) {
                    $finalPrice += $course->irr_price_after_off;
                }
            }

            foreach ($lessonInCart as $ind => $val) {
                $lesson = Lesson::find($val->productable_id);
                $totalPrice += $lesson->price;

                if ($lesson->after_off_price != 0) {
                    $finalPrice += $lesson->after_off_price;
                }
            }


            if ($request->has('coupon')) {
                $coupon = tmp_codes::active()->where('code', $request->get('coupon'))->first();
                $coupon && $finalPrice = $this->calculateFinalPrice($finalPrice, $coupon);
                //                    $finalPrice -= ($notFreeCourses * $coupon->irr_price);
            }
            if ($finalPrice == 0) {
                $making_order = new school_order;
                $making_order->user_id = $user->id;
                $making_order->total_price = $totalPrice;
                $making_order->final_price = $finalPrice;
                $making_order->coopon_info = isset($coupon) ? $coupon->code : null;
                $making_order->payment_status = 1;
                $making_order->products = json_encode($productsInCart);
                $making_order->save();

                foreach ($productsInCart as $ind => $val) {
                    if ($val->productable_type == school_courses::class) {
                        spot_player::licence_generator($making_order->user_id, school_courses::find($val->productable_id)->id, 0);
                    } elseif ($val->productable_type == Lesson::class) {
                        spot_player::lessonLicenceGenerator($user->id, Lesson::find($val->productable_id)->id, 0);
                    }
                    school_basket::find($val->id)->delete();
                }
                return response()->json([
                    'message' => 'purchase was successfully.',
                    'action' => 'https://khiyal.art/dashboard/school_courses'
                ]);
            } else {
                 /*if ($user->id != 1078){
                 return response()->json([
                     'status' => 401,
                     'message' => 'Unauthorized user'
                 ], 401);
                }*/
                $making_order = new school_order;
                $making_order->user_id = $user->id;
                $making_order->total_price = $totalPrice;
                $making_order->final_price = $finalPrice;
                $making_order->coopon_info = isset($coupon) ? $coupon->code : null;
                $making_order->products = json_encode($productsInCart);
                $making_order->save();
                return Payment::callbackUrl('https://api.khiyal.art/api/school/verify_payment')->purchase(
                    (new Invoice)->amount((int)$finalPrice),
                    function ($driver, $transactionId) use ($making_order) {
                        $making_order->transaction_id = $transactionId;
                        $making_order->save();
                    }
                )->pay()->toJson();
            }


        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => $errors
        ], 401);
    }

    private function calculateFinalPrice($finalPrice, $tmp_code)
    {
        if ($tmp_code->expire == tmp_codes::NO_EXPIRE && $tmp_code->presenter == tmp_codes::NO_PRESENTER) {
            return 10000;
        }

        $tmp_code->expire == tmp_codes::EXPIRE && $this->inActiveCoupon($tmp_code);

        if ($tmp_code->type == 1){
            return $tmp_code->irr_price >= $finalPrice ? 0 : ($finalPrice - $tmp_code->irr_price);
        }

        $persent = 100 - $tmp_code->irr_price;

        return ($finalPrice * $persent) / 100;
    }

    private function inActiveCoupon(tmp_codes $code)
    {
        return $code->update([
            'active' => tmp_codes::INACTIVE
        ]);
    }

    public function verifyPayment(verify_transaction $request){
        $transaction_id = $request->get('id');
        $order = orders::where('transaction_id' , '=' , $transaction_id)->firstOrFail();
        try {
            $receipt = Payment::amount(
                (int)$order->final_price
            )->transactionId($transaction_id)->verify();
            $order->tracking_number = $receipt->getReferenceId();
            $order->payment_status = 1;
            $order->save();

            // COUNT OF STACK -1
            $cartProducts = order_products_ecommerce::where('order_id' , '=' , $order->id)->get();

            foreach (
                $cartProducts as $ind => $val
            ){
                $product =  ecommerce_products::findOrFail($val->product_id);
                $product->stock = (int)ecommerce_products::findOrFail($val->product_id)->stock -1;
                $product->save();

                /**
                 *
                 * removing from others' cart
                 *
                 */
                foreach (
                    ecommerce_basket::where('product_id' , '=' , $val->product_id)->get()
                    as $index => $value
                ){
                    if($value->count > $product->stock){
                        if($product->stock === 0){
                            $value->delete();
                        }else{
                            $value->count = $product->stock;
                            $value->save();
                        }
                    }
                }
                $item = ecommerce_basket::where('product_id' , '=' , $val->product_id)->where('user_id' , '=' , orders::where('transaction_id' , '=' , $transaction_id)->firstOrFail()->user_id)->first();
                $item->delete();
            }

            header('location:https://khiyal.art/dashboard/market_order');

        } catch (InvalidPaymentException $exceptions) {
            /**
             *
             * when payment is not verified, it will throw an exception.
             * We can catch the exception to handle invalid payments.
             * getMessage method, returns a suitable message that can be used in user interface.
             *
             */
            echo $exceptions->getMessage();
        }
    }

    public function schoolVerifyPayment(verify_transaction $request)
    {
        $transaction_id = $request->get('id');
        $order = school_order::where('transaction_id', $transaction_id)->firstOrFail();
        try {

            $receipt = Payment::amount(
                (int)$order->final_price
            )->transactionId($transaction_id)->verify();
            $order->tracking_number = $receipt->getReferenceId();
            $order->payment_status = 1;
            $order->save();
            $cartProducts = $order->products;
            $cartProducts = json_decode($cartProducts, true);
            $totalOff = (int)$order->total_price - (int)$order->final_price;

            foreach ($cartProducts as $ind => $val) {
                if ($val['productable_type'] == school_courses::class) {
                    $course = school_courses::find($val['productable_id']);
                    $coursePrice = $totalOff == 0 ? $course->irr_price_after_off : $this->calculateCoursePriceAfterOff($course->irr_price_after_off, $course->price_irr == $course->irr_price_after_off ? $order->total_price : $order->final_price, $order->final_price);
                    spot_player::licence_generator($order->user_id, $course->id, $coursePrice);
                    if (
                        $course->online == 1
                        &&
                        $course->room_id !== null
                    ) {
                        $user = User::find($order->user_id);
                        /**
                         *
                         * course is online
                         *
                         */
                        $this->addUserToRoom(
                            $course->room_id,
                            $user->phone_number,
                            $user->firstName . ' ' . $user->lastName,
                        );
                    }

                } elseif ($val['productable_type'] == Lesson::class) {
                    $lesson = Lesson::find($val['productable_id']);
                    $lessonPrice = $totalOff == 0 ? $lesson->after_off_price : $this->calculateCoursePriceAfterOff($lesson->after_off_price, $lesson->price == $lesson->after_off_price ? $order->total_price : $order->final_price, $order->final_price);
                    spot_player::lessonLicenceGenerator($order->user_id, $lesson->id, $lessonPrice);
                    if ($lesson->online == 1 && $lesson->room_id != null) {
                        $user = User::find($order->user_id);
                        $this->addUserToRoom(
                            $lesson->room_id,
                            $user->phone_number,
                            $user->firstName . ' ' . $user->lastName,
                        );
                    }
                }
                school_basket::find($val['id'])->delete();
            }
            header('location:http://khiyal.art/dashboard/school_courses');
            //$totalOff = (int)school_order::where('transaction_id' , '=' , $transaction_id)->first()->price_irr - (int)school_order::where('transaction_id' , '=' , $transaction_id)->first()->final_price;
            //$offPerEach = $totalOff / $notFreeCourses;
        } catch (InvalidPaymentException $exceptions) {

            echo $exceptions->getMessage();
        }
    }

    private function calculateCoursePriceAfterOff($coursePrice, $totalPrice, $finalPrice){
        if ($coursePrice != 0){
            $persent = ($coursePrice * 100) / $totalPrice;
            return (int)(($persent * $finalPrice) / 100);
        }
        return 0;
    }



    public function showCartItems (get_user_cart_items $request){
        $errors = [];

        /**
         *
         * CHECK TOKEN BE CORRECT
         *
         */
        if(
            count(
                User::where('token' , '=' , $request->get('token'))->get()
            ) === 0
        ){
            /**
             *
             * INVALID TOKEN
             *
             */
            $errors['token'][] = 'entered token is invalid.';
        }

        /**
         *
         * SHOW CART ITEMS IF THERE AREN'T ANY ERRORS
         *
         */
        if(
            count($errors) === 0
        ){
            /**
             *
             * NO ERROR :)
             *
             */
            $totalPrices = 0;
            $totalPricesAfterOff = 0;
            $cartItems = User::where('token' , '=' , $request->get('token'))->first();
            $cartItems = $cartItems->showCartItems;
            foreach ($cartItems as $index => $value){
                $cartItems[$index]->product_details = ecommerce_products::find($value->product_id);

                // Calculating total prices of products (without discounts)
                $totalPrices += ((int)ecommerce_products::find($value->product_id)->price_irr * $value->count);

                // Calculating total prices of products (calculating discounts)
                $totalPricesAfterOff += ((int)ecommerce_products::find($value->product_id)->price_irr_after_off * $value->count);
            }

            return response()->json([
                'message' => 'items in cat received successfully.' ,
                'success' => [
                    'products' => $cartItems ,
                    'total_irr' => $totalPrices,
                    'total_prices_after_off' => $totalPricesAfterOff
                ]
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ] , 401);

    }

    public function addCourseToCart(add_course_to_cart $request){
        $errors = [];

        /**
         *
         * CHECK TOKEN BE CORRECT
         *
         */
        if(
            count(
                User::where('token' , '=' , $request->get('token'))->get()
            ) === 0
        ){
            /**
             *
             * INVALID TOKEN
             *
             */
            $errors['token'][] = 'entered token is invalid.';
        }

        /**
         *
         * CHECK PRODUCT ID BE CORRECT
         *
         */
        if(
        is_null(
            school_courses::find($request->get('course_id'))
        )
        ){
            /**
             *
             * ENTERED COURSE ID IS INCORRECT
             *
             */
            $errors['course_id'][] = 'entered course id is incorrect.';
        }

        /**
         *
         * ADD COURSE TO CART IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR :-)
             *
             */
            if(
                count(
                    school_basket::where('user_id' , User::where('token' , '=' , $request->get('token'))->first()->id)->where('productable_id' , $request->get('course_id'))->where('productable_type' , 'App\Models\school_courses')->get()
                ) === 0
            ){
                $addCourseToCart = school_courses::find($request->get('course_id'))->coursesInCart()->create([
                    'user_id' => User::where('token' , '=' , $request->get('token'))->first()->id
                ]);

                return response()->json([
                    'message' => 'course added to database successfully.' ,
                    'success' => $addCourseToCart
                ]);
            }else{
                $errors['course_id'][] = 'entered product exists in cart.';
            }
        }

        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ] , 401);
    }

    public function showSchoolCart (show_school_cart $request){
        $errors = [];

        /**
         *
         * CHECK TOKEN BE CORRECT
         *
         */
        if(
            count(
                User::where('token' , '=' , $request->get('token'))->get()
            ) === 0
        ){
            /**
             *
             * INVALID TOKEN
             *
             */
            $errors['token'][] = 'entered token is invalid.';
        }

        /**
         *
         * SHOW CART IF THERE AREN'T ANY ERRORS
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR :)
             *
             */
            $inCart = school_basket::where('user_id' , '=' , User::where('token' , '=' , $request->get('token'))->first()->id)->get();
            $courses = [];
            $lessons = [];
            $price = null;
            foreach ($inCart as $ind => $value){
                if($value->productable_type === "App\Models\school_courses"){

                    $courses[] = school_courses::find($value->productable_id);
                    $price += school_courses::find($value->productable_id)->price_irr;
                }
            }

            foreach ($inCart->where('productable_type', Lesson::class) as $item => $value){
                $lessons[] = new OneLessonResource(Lesson::find($value->productable_id));
                $price += Lesson::find($value->productable_id)->price;
            }
            return response()->json([
                'message' => 'school cart received successfully.' ,
                'success' => [
                    "courses" => $courses,
                    'lessons' => $lessons
                ],
                'price' => $price
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ] , 401);
    }

    public function showCourseInformation (Request $request){
        $errors = [];
        include('jdate.php');
        $request->validate([
            'course_id' => 'required'
        ]);

        /**
         *
         * CHECK COURSE ID BE CORRECT
         *
         */
        if(
        is_null(
            school_courses::find($request->get('course_id'))
        )
        ){
            /**
             *
             * INVALID COURSE ID
             *
             */
            $errors['course_id'][] = 'entered course id is invalid.';
        }

        /**
         *
         * SHOW ALL INFORMATION ABOUT COURSE IF THERE AREN'T ANY ERRORS
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR :)
             *
             */
            $courseInformation = school_courses::find($request->get('course_id'));
            $courseInformation->sections = $courseInformation->showAllSections;
            $courseComments =  $courseInformation->courseComments;
            foreach ($courseComments as $ind => $val){
                $commentUserInfo = User::find($val->user_id);
                $courseComments[$ind]->user_info = $commentUserInfo->firstName . ' ' . $commentUserInfo->lastName;
                $courseComments[$ind]->created = jdate('d F Y' , strtotime($val->created_at));
            }

            return response()->json([
                'message' => 'course details received from system successfully.' ,
                'success' => $courseInformation
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ],401);
    }

    public function showPurchasedCourses(Request $request){
        $errors = [];

        /**
         *
         * SHOW PURCHASED COURSES INFORMATION IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR :)
             *
             */
            $user = User::where('token' , '=' , $request->get('token'))->firstOrFail();
            $purchasedCourses = purchases_courses::where('user_id' , '=' ,$user->id)->get();
            $lessonPurchased = LessonPurchased::where('user_id', $user->id)->get()->load('lessonInformation', 'lessonInformation.quizzes');
            foreach ($purchasedCourses as $ind  => $val){
                $purchasedCourses[$ind]->teacher= school_courses::find($val->course_id)->teacher->firstName . ' ' . school_courses::find($val->course_id)->teacher->lastName;
                $purchasedCourses[$ind]->courseInformation = $purchasedCourses[$ind]->courseInformation;
            }
            return response()->json([
                'message' => 'purchased course received successfully.',
                'success' => $purchasedCourses,
                'lesson'  => $lessonPurchased
            ]);

        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ],401);
    }

    public function showPurchasedLessons(Request $request)
    {
        $user = User::where('token' , '=' , $request->get('token'))->firstOrFail();
        $lessonPurchased = LessonPurchased::where('user_id', $user->id)->get()->load('lessonInformation', 'lessonInformation.posters');

        return response()->json([
            'message' => 'purchased lessons received successfully.',
            'lessons'  => $lessonPurchased
        ]);
    }

    public function checkCoupon (checkCoupon $request){
        $errors = [];

        /**
         *
         * check code is valid or no
         *
         */
        if(
            count(
                tmp_codes::where('code' , '=' , $request->get('coupon'))->get()
            ) === 0
        ){
            /**
             *
             * invalid coupon
             *
             */
            $errors['coupon'][] = 'entered coupon is invalid.';
        }

        /**
         *
         * check coupon doesn't be used
         *
         */
        if(
            count(
                tmp_codes::where('code' , '=' , $request->get('coupon'))->where('active' , '=' ,'1')->get()
            ) === 0
        ){
            /**
             *
             * used coupon
             *
             */
            $errors['coupon'][] = 'entered coupon has been used.';
        }

        /**
         *
         * return coupon information if count of errors is 0
         *
         */
        if(count($errors) === 0){
            /**
             *
             * NO ERROR :)
             *
             */
            $coupon = tmp_codes::where('code' , '=' , $request->get('coupon'))->first();
            return response()->json([
                'message' => 'information of coupon received successfully.' ,
                'success' => $coupon
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ],401);
    }

    /**
     *
     *
     *
     * skyroom
     *
     *
     *
     */
    public function getListOfUsers (){

        $getUsersInformation = curl_init();
        curl_setopt($getUsersInformation ,CURLOPT_URL , $this->apiLink);
        curl_setopt($getUsersInformation ,CURLOPT_HTTPHEADER , [
            'Content-Type: application/json'
        ]);
        curl_setopt($getUsersInformation ,CURLOPT_POST, true);
        curl_setopt($getUsersInformation ,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($getUsersInformation ,CURLOPT_POSTFIELDS , [
            "action"      => "getUsers"
        ]);
        $response            = curl_exec  ($getUsersInformation);
        curl_close ($getUsersInformation);
        return $response;
    }

    public function addUserIfNotExists ($phone_number , $fullName){
        $listOfUsers = json_decode($this->getListOfUsers(), true);
        $listOfUserNames = [];

        if($listOfUsers === null) $listOfUsers = [];
        /**
         *
         * making a list of username of skyroom users
         *
         */
        foreach ($listOfUsers as $ind => $val){
            $listOfUserNames[] = $val['result']['id'];
        }


        if(
            count(
                skyroom_users::where('user_id' , User::where('phone_number' , $phone_number)->first()->id)->get()
            ) === 0
        ){
            /**
             *
             * user must add to skyroom
             *
             */

            $getUsersInformation = curl_init();
            curl_setopt($getUsersInformation ,CURLOPT_URL , $this->apiLink);
            curl_setopt($getUsersInformation ,CURLOPT_HTTPHEADER , [
                'Content-Type: application/json'
            ]);
            curl_setopt($getUsersInformation ,CURLOPT_POST, true);
            curl_setopt($getUsersInformation ,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($getUsersInformation ,CURLOPT_POSTFIELDS , json_encode([
                "action"      => "createUser" ,
                'params'      => [
                    'username' => $phone_number,
                    'password' => $phone_number,
                    "nickname" => $fullName,
                    "status"=> 1,
                    "is_public"=> true
                ]
            ]));
            $response = curl_exec  ($getUsersInformation);
            curl_close ($getUsersInformation);
            $response       = (array)json_decode($response, true);
            $skyroomUserId  = $response['result'];
            $skyRoomUserAdd = new skyroom_users;
            $skyRoomUserAdd->id = $skyroomUserId;
            $skyRoomUserAdd->user_id = User::where('phone_number' , '=' , $phone_number)->first()->id;
            $skyRoomUserAdd->save();
            return $skyroomUserId;
        }
        /**
         *
         * else user is added to skyroom previously
         *
         */
        else{
            return skyroom_users::where('user_id' , User::where('phone_number' , $phone_number)->first()->id)->first()->id;
        }
    }

    public function addUserToRoom($roomId , $phone_number , $fullName){
        /**
         *
         * adding user to skyroom if didn't add previously
         *
         */
        $skyroomUserId = $this->addUserIfNotExists($phone_number , $fullName);
        /**
         *
         * add user to room
         *
         */
        $addUserToRoom = curl_init();
        curl_setopt($addUserToRoom ,CURLOPT_URL , $this->apiLink);
        curl_setopt($addUserToRoom ,CURLOPT_HTTPHEADER , [
            'Content-Type: application/json'
        ]);
        curl_setopt($addUserToRoom ,CURLOPT_POST, true);
        curl_setopt($addUserToRoom ,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($addUserToRoom ,CURLOPT_POSTFIELDS , json_encode([
            "action"       => "addRoomUsers" ,
            'params'       => [
                'room_id'  => $roomId,
                'users'    => [
                    (object)['user_id' => $skyroomUserId]
                ]
            ]
        ]));
        curl_exec  ($addUserToRoom);
        curl_close ($addUserToRoom);
    }

    /**
     *
     *
     *
     * skyroom
     *
     *
     *
     */

    public function showAllShippingMethods (){
        $errors = [];

        if(count($errors) === 0){
            # no error
            $shippingMethods = shipping_methods::all();
            return response()->json([
                'message' => 'shipping methods received from database successfully.' ,
                'success' => $shippingMethods
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ],401);
    }

    public function trackOrder (track_order $request){
        $errors = [];

        /**
         *
         * check order id be valid
         *
         */
        if(
        !orders::find($request->get('order_id'))
        ){
            /**
             *
             * entered order id is invalid
             *
             */
            $errors['order_id'][] = 'entered order id is invalid.';
        }

        /**
         *
         * check this order belongs to this user if user is be valid
         *
         */
        elseif(
            orders::find($request->get('order_id'))->user_id != User::where('token' , '=' , $request->get('token'))->first()->id
        ){
            /**
             *
             * this order doesn't depend to this user
             *
             */
            return response()->json([
                'message' => 'access denied.',
                'errors'  => [
                    'you don\'t have access to this order'
                ]
            ] , 403);
        }

        /**
         *
         * show order details if there aren't any errors
         *
         */
        if(
            count($errors) === 0
        ){
            # no error
            include('jdate.php');
            $order = orders::find($request->get('order_id'));
            $order->address_info;
            $order->products;
            foreach ($order->products as $ind => $val){
                $order->products[$ind]->productInformation;
                $order->products[$ind]->create = jdate('d F Y' , strtotime($order->created_at));
            }

            return response()->json([
                'message' => 'order details received successfully.' ,
                'success' => $order
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ],401);

    }

    public function ordersList (get_orders_list $request){
        $errors = [];

        /**
         *
         * get user information if there aren't any errors
         *
         */
        if(
            count($errors) === 0
        ) {
            # no error
            include('jdate.php');

            // list of orders
            $orders = User::where('token', '=', $request->get('token'))->first()->showOrders;

            /**
             *
             * getting list of products in cart
             *
             */
            foreach ($orders as $index => $value) {
                $orders[$index]->products;

                /**
                 *
                 * getting information of each product in cart
                 *
                 */
                foreach ($orders[$index]->products as $ind => $val) {
                    // Adding all information to product
                    $orders[$index]->products[$ind]->productInformation;

                    // Adding Jalali time to response (created at)
                    $orders[$index]->products[$ind]->productInformation->create = jdate('d F Y', strtotime($val->productInformation->created_at));
                }

            }

            return response()->json([
                'message' => 'orders received from database successfully.' ,
                'success' => $orders
            ]);

        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ],401);
    }

    public function addConsultantToCart (Request $request){
        $errors = [];

        /**
         *
         * check consultant id be valid
         *
         */
        if(
        !consultants::find($request->get('consultant_id'))
        ){
            /**
             *
             * entered consultant id is invalid.
             *
             */
            $errors['consultant_id'][] = 'entered consultant id is invalid.';
        }

        /**
         *
         * check selected plan
         *
         */
        elseif(
        !in_array(
            [
                1 , // once
                2 , // monthly
                3 , // yearly
            ],
            $request->get('consultant_plan')
        )
        ){
            /**
             *
             * entered plan isn't invalid
             *
             */
            $errors['consultant_plan'][] = 'entered plan is invalid.';
        }

        /**
         *
         * check the consultant supports the selected plan
         *
         */
        else{
            $plan = null;
            switch($request->get('consultant_plan')){
                case 1:
                    if(
                    is_null(
                        consultants::find($request->get('consultant_id'))->once_price
                    )
                    ){
                        /**
                         *
                         * this consultant doesn't support this plan
                         *
                         */
                        $errors['consultant_plan'][] = 'entered plan is not supported by this consultant.';
                    }
                    break;
                case 2:
                    if(
                    is_null(
                        consultants::find($request->get('consultant_id'))->monthly_price
                    )
                    ){
                        /**
                         *
                         * this consultant doesn't support this plan
                         *
                         */
                        $errors['consultant_plan'][] = 'entered plan is not supported by this consultant.';
                    }
                    break;
                case 3:
                    if(
                    is_null(
                        consultants::find($request->get('consultant_id'))->yearly_price
                    )
                    ){
                        /**
                         *
                         * this consultant doesn't support this plan
                         *
                         */
                        $errors['consultant_plan'][] = 'entered plan is not supported by this consultant.';
                    }
                    break;
            }
        }

        /**
         *
         * check user doesn't have this consultant is his cart
         *
         */
        if(
            count(
                school_basket::where(
                    'user_id' ,
                    User::where('token' , $request->get('token'))->first()->id
                )->where(
                    'productable_id' ,
                    $request->get('consultant_id')
                )->where(
                    'productable_type' ,
                    consultants::class
                )->get()
            ) !== 0
        ){
            /**
             *
             * user has this plan in his cart
             *
             */
            $errors['consultant_id'][] = 'you have this consultant in your cart.';
        }

        /**
         *
         * adding consultant to cart if there aren't any errors
         *
         */
        if(count($errors) === 0){
            # no error

            // Additional data
            $data = [
                'plan' => $plan
            ];

            $userCart = new school_basket();
            $userCart->user_id = User::where('token' , $request->get('token'))->first()->id;
            $userCart->productable_type = consultants::class;
            $userCart->productable_id = $request->get('consultant_id');
            $userCart->data = $data;
            $userCart->save();
            return response()->json([
                'message' => 'consultant added to cart successfully.' ,
                'success' => $userCart
            ]);
        }

        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ],401);
    }
}

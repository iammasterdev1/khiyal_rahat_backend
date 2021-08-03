<?php

use App\Http\Controllers\admin_panel;
use App\Http\Controllers\publics;
use App\Http\Controllers\register_user;
use App\Http\Controllers\student;
use App\Http\Controllers\message;
use App\Http\Controllers\Api\PosterController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\LandingController;
use App\Http\Controllers\Api\CategoryController;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware' => ["cors" , "admin"]] , function(){
    Route::any(
        '/admin/all_orders' ,
        [
            admin_panel::class ,
            'showEcommerceOrders'
        ]
    );
    Route::any(
        '/admin/get_order_details' ,
        [
            admin_panel::class ,
            'getOrderDetails'
        ]
    );

    Route::any(
        '/admin/order/{orderId}/3' ,
        [
            admin_panel::class ,
            'orderStatus3'
        ]
    );

    Route::any(
        '/admin/order/{orderId}/2' ,
        [
            admin_panel::class ,
            'orderStatus2'
        ]
    );

    Route::any(
        '/admin/order/{orderId}/1' ,
        [
            admin_panel::class ,
            'orderStatus1'
        ]
    );
    Route::any(
        '/admin/order/{orderId}/0' ,
        [
            admin_panel::class ,
            'orderStatus0'
        ]
    );

    Route::any(
        '/admin/users_list' ,
        [
            admin_panel::class ,
            'getAllUsers'
        ]
    );

    Route::any(
        '/admin/add_brand' ,
        [
            admin_panel::class ,
            'add_brand'
        ]
    );

    Route::any(
        '/admin/important_feature_add' ,
        [
            admin_panel::class ,
            'addImportantFeatureToProduct'
        ]
    );
    Route::post(
        "/admin/add_category" ,
        [
            admin_panel::class ,
            'add_category'
        ]
    );

    Route::post(
        "/admin/add_course" ,
        [
            admin_panel::class ,
            'newCourse'
        ]
    );

    Route::post(
        "/admin/add_color" ,
        [
            admin_panel::class ,
            'add_color_to_available_colors'
        ]
    );

    Route::post(
        '/admin/add_course_image/{courseId}' ,
        [
            admin_panel::class ,
            'addImageToCourse'
        ]
    );

    Route::post(
        '/admin/add_course_feature/{courseId}' ,
        [
            admin_panel::class ,
            'addFeatureToCourse'
        ]
    );

    Route::post(
        '/admin/add_section/{courseId}' ,
        [
            admin_panel::class ,
            'addSectionToCourse'
        ]
    );

    Route::post(
        '/admin/add_book' ,
        [
            admin_panel::class ,
            'addBookToSell'
        ]
    );

    Route::post(
        '/admin/pending_features' ,
        [
            admin_panel::class ,
            'showPendingFeatures'
        ]
    );

    Route::post(
        '/admin/pending_images' ,
        [
            admin_panel::class ,
            'showPendingImages'

        ]
    );

    Route::post(
        '/admin/pending_images/accept' ,
        [
            admin_panel::class ,
            'acceptImageForCourse'
        ]
    );

    Route::post(
        '/admin/pending_images/reject' ,
        [
            admin_panel::class ,
            'rejectImageForCourse'
        ]
    );

    Route::post(
        '/admin/pending_features/accept' ,
        [
            admin_panel::class ,
            'acceptFeatureForCourse'
        ]
    );

    Route::post(
        '/admin/pending_features/reject' ,
        [
            admin_panel::class ,
            'rejectFeatureForCourse'
        ]
    );

    Route::post(
        '/admin/add_ecommerce_product' ,
        [
            admin_panel::class ,
            'addProduct'
        ]
    );

    Route::post(
        '/admin/add_feature_to_ecommerce_product' ,
        [
            admin_panel::class ,
            'addFeatureToProduct'
        ]
    );

    Route::post(
        '/admin/add_image_to_ecommerce_product' ,
        [
            admin_panel::class ,
            'addImageToProduct'
        ]
    );

    Route::post(
        '/admin/delete_ecommerce_image' ,
        [
            admin_panel::class ,
            'deleteImageOfProduct'
        ]
    );

    Route::post(
        '/admin/delete_ecommerce_feature' ,
        [
            admin_panel::class ,
            'deleteFeatureOfProduct'
        ]
    );

    Route::post(
        '/admin/update_ecommerce_product_price' ,
        [
            admin_panel::class ,
            'updatePriceOfEcommerceProduct'
        ]
    );;

    Route::post(
        '/admin/update_ecommerce_product_price_after_off' ,
        [
            admin_panel::class ,
            'updatePriceAfterOffOfEcommerceProduct'
        ]
    );

    Route::post(
        '/admin/update_ecommerce_product_name' ,
        [
            admin_panel::class ,
            'updateNameOfProduct'
        ]
    );

    Route::post(
        '/admin/update_ecommerce_product_description' ,
        [
            admin_panel::class ,
            'updateDescriptionOfProduct'
        ]
    );

    Route::any(
        '/admin/profile_in_stock' ,
        [
            admin_panel::class ,
            'productInStockTrue'
        ]
    );

    Route::any(
        '/admin/profile_not_in_stock' ,
        [
            admin_panel::class ,
            'productInStockFalse'
        ]
    );

    Route::post(
        '/admin/shipping_method/add' ,
        [
            admin_panel::class ,
            'addShippingMethod'
        ]
    );


    Route::post(
        '/admin/shipping_method/delete' ,
        [
            admin_panel::class ,
            'deleteShippingMethod'
        ]
    );

    Route::any(
        '/admin/delete_product' ,
        [
            admin_panel::class ,
            'deleteProduct'
        ]
    );
    Route::any(
        '/admin/add_technical_specific' ,
        [
            admin_panel::class ,
            'addTechnicalSpecificationToProduct'
        ]
    );

    Route::any(
        '/admin/add_color_to_product' ,
        [
            admin_panel::class ,
            'add_color_to_product'
        ]
    );

    Route::any(
        '/admin/show_all_colors' ,
        [
            admin_panel::class ,
            'showAllColors'
        ]
    );
    Route::any(
        '/admin/hide_product' ,
        [
            admin_panel::class ,
            'hide_product'
        ]
    );
    Route::any(
        '/admin/unhide_product' ,
        [
            admin_panel::class ,
            'unhide_product'
        ]
    );



});

Route::group(['middleware' => ['cors']] , function(){
    Route::any(
        '/market/shipping' ,
        [
            student::class ,
            'showAllShippingMethods'
        ]
    );

    Route::any(
        '/school/cart/course/delete' ,
        [
            student::class ,
            'removeCourseFromCart'
        ]
    );
    Route::any(
        '/chat/list/get' ,
        [
            message::class ,
            'getListOfAllMessages'
        ]
    );
    Route::any(
        '/chats/messages/all' ,
        [
            message::class ,
            'getAllMessagesOfChat'
        ]
    );
    Route::any(
        '/chats/message/new' ,
        [
            message::class ,
            'newMessage'
        ]
    );
    Route::any(
        '/chats/new' ,
        [
            message::class ,
            'startChatWithTeacherOrGetInfo'
        ]
    );

    Route::any(
        '/student/courses/purchased' ,
        [
            student::class ,
            'showPurchasedCourses'
        ]
    );

    Route::any(
        '/student/lessons/purchased',
        [
            student::class,
            'showPurchasedLessons'
        ]
    );

    Route::any(
        '/school/courses/comment/add' ,
        [
            student::class ,
            'addCommentToCourse'
        ]
    );

    Route::any(
        '/school/lessons/comment/add',
        [
            student::class,
            'addCommentToLesson'
        ]
    )->middleware('userApi');

    Route::any(
        '/school/cart/course/delete' ,
        [
            student::class ,
            'removeCourseFromCart'
        ]
    );

    Route::any(
        '/school/cart/lesson/delete',
        [
            student::class,
            'removeLessonFromCart'
        ]
    );

    Route::any(
        '/coupon/check' ,
        [
            student::class ,
            'checkCoupon'
        ]
    );
    Route::any(
        '/admin/teacher_list' ,
        [
            admin_panel::class ,
            'showListOfTeachers'
        ]
    );
    Route::any(
        '/check_cookie' ,
        [
            register_user::class ,
            'checkToken'
        ]
    );

    Route::any(
        'school/all_courses' ,
        [
            publics::class ,
            'showAllCourses'
        ]

    );

    Route::any(
        '/register/get_number' ,
        [
            register_user::class ,
            'getPhoneNumberForRegister'
        ]
    );

    Route::any(
        '/login' ,
        [
            register_user::class ,
            'login'
        ]
    );

    Route::any(
        '/login/code_verification' ,
        [
            register_user::class ,
            'login_enter_code'
        ]
    );

    Route::any(
        '/register/code_verification' ,
        [
            register_user::class ,
            'verify_phone_number'
        ]
    );

    Route::any(
        '/register/complete' ,
        [
            register_user::class ,
            'completeRegister'
        ]
    );

    Route::any(
        'course/info' ,
        [
            student::class ,
            'showCourseInformation'
        ]

    );

 Route::any(
        'courses/{course}',
        [
            \App\Http\Controllers\Api\CourseController::class,
            'show'
        ]

    )->middleware('userApi');

    Route::any(
        'show_study_fields' ,
        [
            register_user::class ,
            'showAllStudyFields'
        ]
    );
    Route::any(
        'show_majors' ,
        [
            register_user::class ,
            'showAllMajors'
        ]
    );

    Route::any(
        '/allProducts' ,
        [
            publics::class ,
            'showAllEcommerceProducts'
        ]
    );

    Route::any(
        '/sub_categories' ,
        [
            publics::class ,
            'showAllSubCategoriesOfCategory'
        ]
    );

    Route::any(
        '/all_parent_categories' ,
        [
            publics::class ,
            'showAllParentsOfCategory'
        ]
    );
    Route::any(
        '/product_info' ,
        [
            publics::class ,
            'showProductInfo'
        ]
    );

    Route::any(
        '/products/info',
        [
            ProductController::class,
            'show'
        ]
    );

    Route::any(
        '/products/comments/store',
        [
            ProductController::class,
            'storeComment'
        ]
    )->middleware('userApi');

    Route::any(
        '/show_all_categories' ,
        [
            admin_panel::class ,
            'showAllEcommerceCategories'
        ]
    );

    Route::any(
        '/show_school_cats' ,
        [
            admin_panel::class ,
            'showAllSchoolCategories'
        ]
    );
    Route::any(
        '/show_all_brands' ,
        [
            admin_panel::class ,
            'showALlBrands'
        ]
    );

    Route::any(
        '/school/add_to_cart' ,
        [
            student::class ,
            'addSchoolProductIntoCart'
        ]
    );

    Route::any(
        '/school/show_cart' ,
        [
            student::class ,
            'showSchoolCart'
        ]
    );
});

Route::group(['middleware' => ['cors' ], 'as' => 'all_users'] , function(){
    Route::any(
        '/consultant/list' ,
        [
            publics::class ,
            'showConsultantsList'
        ]
    );

    Route::any(
        '/student/profile' ,
        [
            register_user::class ,
            'getProfileInfo'
        ]
    );
    Route::any(
        '/profile/update' ,
        [
            register_user::class ,
            'edit_profile'
        ]
    );
    Route::post(
        '/student/add_ecommerce_product_to_cart' ,
        [
            student::class ,
            'addProductIntoEcommerceCart'
        ]
    )->middleware('userApi');


    Route::post(
        '/student/add_new_address' ,
        [
            student::class ,
            'addNewAddress'
        ]
    );

    Route::any(
        '/ecommerce/get_invoice' ,
        [
            student::class ,
            'getInvoice'
        ]
    );

    Route::any(
        '/ecommerce/orders/track' ,
        [
            student::class ,
            'trackOrder'
        ]
    );

    Route::any(
        '/ecommerce/orders/list' ,
        [
            student::class ,
            'ordersList'
        ]
    );

    Route::any(
        '/ecommerce/payment/verify' ,
        [
            student::class ,
            'verifyPayment'
        ]
    );

    Route::post(
        '/school/get_invoice' ,
        [
            student::class ,
            'getInvoice'
        ]
    );

    Route::any(
        '/school/get_school_invoice' ,
        [
            student::class ,
            'make_school_order'
        ]
    );

    Route::any(
        '/market/get_user_cart_items' ,
        [
            student::class ,
            'showCartItems'
        ]
    );

    Route::any(
        '/school/verify_payment' ,
        [
            student::class ,
            'schoolVerifyPayment'
        ]
    );

    Route::any(
        '/market/remove_item_from_cart' ,
        [
            student::class ,
            'removeItemFromEcommerceCart'
        ]
    );
    Route::any(
        '/market/add_item_from_cart' ,
        [
            student::class ,
            'addProductIntoEcommerceCart'
        ]
    )->middleware('userApi');

    Route::any(
        '/get_all_addresses' ,
        [
            student::class,
            'showAddressList'
        ]
    );

    Route::any(
        '/shop/delete_products' ,
        [
            student::class ,
            'deleteItemFromEcommerceCart'
        ]
    );

    Route::any(
        '/address/delete' ,
        [
            student::class ,
            'removeAnAddress'
        ]
    );

    Route::any(
        '/course_details' ,
        [
            publics::class ,
            'showCourseDetails'
        ]
    );
});

//Posters

Route::middleware('cors')->prefix('school')->group(function () {
    Route::get('/lessons', [PosterController::class, 'index']);
    Route::post('/lessons', [PosterController::class, 'show'])->name('lessons.show');
    Route::get('/posters/{poster:slug}/download', [PosterController::class, 'download'])->name('posters.download');
    Route::post('/lessons/addToCart', [PosterController::class, 'addToCart']);
});

//landings

Route::middleware('cors')->prefix('shop')->group(function () {
    Route::get('/landings/{landing}', [LandingController::class, 'show']);
    Route::get('/landing_page/{landingPage}', [LandingController::class, 'landingPage']);
    Route::post('/search', [ProductController::class, 'search']);
    Route::post('/search/test', [ProductController::class, 'searchTest']);
    Route::post('categories', [CategoryController::class, 'index']);
});

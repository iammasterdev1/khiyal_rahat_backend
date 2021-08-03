<?php

use App\Http\Controllers\admin_panel;
use App\Http\Controllers\register_user;
use App\Http\Controllers\student;
use App\Http\Controllers\skyroom;
use App\Models\User;
use App\Models\tmp_codes;
use App\Models\skyroom_users;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/tetsmeu', function() {
//Artisan::call("migrate");
//});


Route::group(['middleware' => ["cors", "admin"]], function () {

    Route::post(
        "/admin/add_category",
        [
            admin_panel::class,
            'add_category'
        ]
    );

    Route::post(
        "/admin/add_course",
        [
            admin_panel::class,
            'newCourse'
        ]
    );

    Route::post(
        '/admin/add_course_image/{courseId}',
        [
            admin_panel::class,
            'addImageToCourse'
        ]
    );

    Route::post(
        '/admin/add_course_feature/{courseId}',
        [
            admin_panel::class,
            'addFeatureToCourse'
        ]
    );

    Route::post(
        '/admin/add_section/{courseId}',
        [
            admin_panel::class,
            'addSectionToCourse'
        ]
    );

    Route::post(
        '/admin/add_book',
        [
            admin_panel::class,
            'addBookToSell'
        ]
    );

    Route::post(
        '/admin/pending_features',
        [
            admin_panel::class,
            'showPendingFeatures'
        ]
    );

    Route::post(
        '/admin/pending_images',
        [
            admin_panel::class,
            'showPendingImages'

        ]
    );

    Route::post(
        '/admin/pending_images/accept',
        [
            admin_panel::class,
            'acceptImageForCourse'
        ]
    );

    Route::post(
        '/admin/pending_images/reject',
        [
            admin_panel::class,
            'rejectImageForCourse'
        ]
    );

    Route::post(
        '/admin/pending_features/accept',
        [
            admin_panel::class,
            'acceptFeatureForCourse'
        ]
    );

    Route::post(
        '/admin/pending_features/reject',
        [
            admin_panel::class,
            'rejectFeatureForCourse'
        ]
    );

    Route::any(
        '/admin/add_ecommerce_product',
        [
            admin_panel::class,
            'addProduct'
        ]
    );

    Route::post(
        '/admin/add_feature_to_ecommerce_product',
        [
            admin_panel::class,
            'addFeatureToProduct'
        ]
    );

    Route::post(
        '/admin/add_image_to_ecommerce_product',
        [
            admin_panel::class,
            'addImageToProduct'
        ]
    );

    Route::post(
        '/admin/delete_ecommerce_image',
        [
            admin_panel::class,
            'deleteImageOfProduct'
        ]
    );

    Route::post(
        '/admin/delete_ecommerce_feature',
        [
            admin_panel::class,
            'deleteFeatureOfProduct'
        ]
    );

    Route::post(
        '/admin/update_ecommerce_product_price',
        [
            admin_panel::class,
            'updatePriceOfEcommerceProduct'
        ]
    );

    Route::post(
        '/admin/update_ecommerce_product_name',
        [
            admin_panel::class,
            'updateNameOfProduct'
        ]
    );

    Route::post(
        '/admin/update_ecommerce_product_description',
        [
            admin_panel::class,
            'updateDescriptionOfProduct'
        ]
    );

    Route::post(
        '/admin/profile_in_stock',
        [
            admin_panel::class,
            'productInStockTrue'
        ]
    );

    Route::post(
        '/admin/profile_not_in_stock',
        [
            admin_panel::class,
            'productInStockFalse'
        ]
    );

    Route::post(
        '/admin/shipping_method/add',
        [
            admin_panel::class,
            'addShippingMethod'
        ]
    );


    Route::post(
        '/admin/shipping_method/delete',
        [
            admin_panel::class,
            'deleteShippingMethod'
        ]
    );


});

Route::group(['middleware' => ['cors' => 'student']], function () {

    Route::any(
        '/student/profile',
        [
            student::class,
            'profile'
        ]
    );

    Route::post(
        '/student/add_ecommerce_product_to_cart',
        [
            student::class,
            'addProductIntoEcommerceCart'
        ]
    );

    Route::post(
        '/student/add_new_address',
        [
            student::class,
            'addNewAddress'
        ]
    );

    Route::post(
        '/ecommerce/get_invoice',
        [
            student::class,
            'getInvoice'
        ]
    );

    Route::any(
        '/ecommerce/payment/verify',
        [
            student::class,
            'verifyPayment'
        ]
    );

    Route::post(
        '/school/get_invoice',
        [
            student::class,
            'getInvoice'
        ]
    );

});

Route::group(['middleware' => ['cors']], function () {

    Route::any(
        '/check_cookie',
        [
            register_user::class,
            'checkToken'
        ]
    );

    Route::any(
        '/register/get_number',
        [
            register_user::class,
            'getPhoneNumberForRegister'
        ]
    );

    Route::any(
        '/login',
        [
            register_user::class,
            'login'
        ]
    );

    Route::any(
        '/login/code_verification',
        [
            register_user::class,
            'login_enter_code'
        ]
    );

    Route::any(
        '/register/code_verification',
        [
            register_user::class,
            'verify_phone_number'
        ]
    );

    Route::any(
        '/register/complete',
        [
            register_user::class,
            'completeRegister'
        ]
    );

    Route::any(
        'show_study_fields',
        [
            register_user::class,
            'showAllStudyFields'
        ]
    );
    Route::any(
        'show_majors',
        [
            register_user::class,
            'showAllMajors'
        ]
    );
});

Route::group(['middleware' => 'cors'], function () {

    Route::get(
        '/player' ,
        function (){
            return view("player");
        }
    );


    Route::any(
        '/allProducts',
        [
            publics::class,
            'showAllEcommerceProducts'
        ]
    );

    Route::any(
        '/sub_categories',
        [
            publics::class,
            'showAllSubCategoriesOfCategory'
        ]
    );

    Route::any(
        '/all_parent_categories',
        [
            publics::class,
            'showAllParentsOfCategory'
        ]
    );
});

/**
 *
 * ADMIN PANEL
 *
 */
Route::any(
    "/adm/product_add" ,
    ['as' => 'add_image' ,function(){
        return view('admin.product_added_successfully')->with('id' , 22);
        }
    ]
);

Route::any(
    "/adm/cat_added_success" ,
    ['as' => 'cat_added' ,function(){
        return view('admin.cat_added_successfully')->with('id' , 22);
        }
    ]
);
Route::any(
    '/adm/course_added_success' ,
    [
        'as' => 'course_added' , function(){
            return view('admin.course_added_successfully')->with('id' , 22);
        }
    ]
);


Route::any(
    '/tes' ,
    function(){
        $purchased_courses = \App\Models\purchases_courses::all();
        foreach ($purchased_courses as $ind => $val){
            $courseInfo = \App\Models\school_courses::find($val->course_id);
            if((int)$courseInfo->irr_price_after_off != 0)
                $coursePrice = (int)$courseInfo->irr_price_after_off - 250000;
            else
                $coursePrice = 0;
            $product = \App\Models\purchases_courses::find($val->id);
            $product->price = $coursePrice;
            $product->save();
        }
    }
);


Route::any(
    '/courses' ,
    function(){
        $users = App\Models\User::get();
        $schoolCourses = \App\Models\school_courses::orderBy('id' , 'ASC')->get();
        $lessons = \App\Models\Lesson::get();


        echo '<table>';
        echo "<thead>";
        echo '<tr>';
        echo '<th>نام و نام خانوادگی</th>';
        echo '<th>شماره موبایل</th>';

        foreach ($schoolCourses as $i => $v){
            echo "<th>$v->course_name</th>";
        }
        foreach ($lessons as $i => $v){
            echo "<th>$v->title</th>";
        }

        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($users as $i => $v){
            echo '<tr>';
            echo "<td>$v->firstName $v->lastName</td>";
            echo "<td>$v->phone_number</td>";
            foreach ($schoolCourses as $in => $va){
                if(
                    count(
                        \App\Models\purchases_courses::where('course_id' , $va->id)->where('user_id' , $v->id)->get()
                    )!== 0
                ){
                    echo "<td> * </td>";
                }else  echo "<td> </td>";
            }

            foreach ($lessons as $in => $va){
                if(
                    count(
                        \App\Models\LessonPurchased::where('lesson_id' , $va->id)->where('user_id' , $v->id)->get()
                    )!== 0
                ){
                    echo "<td> * </td>";
                }else  echo "<td> </td>";
            }



            echo "<td></td>";
            echo '</tr>';
        }


        echo '</tbody>';
        echo "</table>";


    }


);





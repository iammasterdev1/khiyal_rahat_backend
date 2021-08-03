<?php

namespace App\Http\Controllers;

use App\Http\Requests\add_pdf_book_to_sell;
use App\Http\Requests\admin_add_available_colors;
use App\Http\Requests\add_color_to_product;
use App\Http\Requests\hide_and_show_product;
use App\Http\Requests\admin_get_order_details;
use App\Http\Requests\admin_delete_product;
use App\Http\Requests\addFeatureToCourse;
use App\Http\Requests\addFeatureToProduct;
use App\Http\Requests\addImageToCourse;
use App\Http\Requests\addImageToProduct;
use App\Http\Requests\admin_accept_penging_image;
use App\Http\Requests\admin_acceptExam;
use App\Http\Requests\admin_add_major;
use App\Http\Requests\admin_add_study_area;
use App\Http\Requests\admin_addProduct;
use App\Http\Requests\admin_addShippingMethod;
use App\Http\Requests\admin_panding_features_show;
use App\Http\Requests\admin_panel_add_category;
use App\Http\Requests\admin_pending_images_show;
use App\Http\Requests\admin_reject_pending_image;
use App\Http\Requests\admin_rejectExam;
use App\Http\Requests\admin_seeAllPendingExams;
use App\Http\Requests\create_new_course;
use App\Http\Requests\deleteProductFeature;
use App\Http\Requests\deleteProductImage;
use App\Http\Requests\deleteShippingMethod;
use App\Http\Requests\productIsInStock;
use App\Http\Requests\productIsNotInStock;
use App\Http\Requests\school_course_add_section;
use App\Http\Requests\updateDescriptionOfProduct;
use App\Http\Requests\updateNameOfProduct;
use App\Http\Requests\updatePriceOfProduct;
use App\Models\brands;
use App\Models\categories;
use App\Models\colors;
use App\Models\ecommerce_basket;
use App\Models\ecommerce_product_important_feature;
use App\Models\orders;
use Illuminate\Http\Request;
use App\Models\coursed_titles;
use App\Models\ecommerce_products;
use App\Models\exams;
use App\Models\majors;
use App\Models\pdf_books;
use App\Models\products_features;
use App\Models\products_images;
use App\Models\school_courses;
use App\Models\shipping_methods;
use App\Models\student_study_area;
use App\Models\User;
use Carbon\Carbon;
use Facade\FlareClient\Context\RequestContext;

class admin_panel extends Controller
{
    public $args = [
        'cdn_server' => 'https://cdn.khiyal.art'
    ];

    public function add_category(admin_panel_add_category $request)
    {
        $errors = [];

        /**
         *
         * CHECK VALUE OF SUB_CAT_OF BE TRUE IF IT HAS BEEN SENT
         *
         */
        if($request->has('sub_cat_of') && !empty($request->get('sub_cat_of'))){
            /**
             *
             * THIS CATEGORY IS SUB_CATEGORY OF ANOTHER VALUE
             *
             */
            if(
            is_null(
                categories::find($request->get('sub_cat_of'))
            )
            ){
                /**
                 *
                 * ENTERED CATEGORY ID FOR PARENT OF PARENT OF SUB CAT
                 *
                 */
                $errors['sub_cat_of'][] = 'entered category id for parent of sub category is invalid.';
            }
        }
        /**
         *
         * IMAGE CHECK
         *
         */
        $fileSaveLocation = md5(mt_rand(1 , 100)).date("Y-m-d,H:i:s");
        if(!is_dir(base_path() . "/private/". $fileSaveLocation)){
            /**
             *
             * THIS DIRECTORY IS INVALID, MUST CREATE FOLDER
             *
             */
            if (!mkdir($concurrentDirectory = base_path() . "/private/" . $fileSaveLocation, 0777, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
        $file_name = $request->image->getClientOriginalName();
        $request->file('image')->move(base_path()."/private/".$fileSaveLocation , $file_name);
        if(ftp_upload::ftp_upload_cdn_one($file_name , $fileSaveLocation)){
            /**
             *
             * FILE UPLOADED INTO CDN FILE
             *
             */
            $fileSaveLocation = $this->args['cdn_server'] . "/$fileSaveLocation" . "/".$file_name;
        }else{
            /**
             *
             * UPLOAD WASN'T SUCCESSFULLY
             *
             */
            $errors['image'][] = 'upload image into cdn server failed.';
        }

        /**
         *
         * Add category if there weren't any errors
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             *
             * EVERY THING OK
             *
             */
            $insertCat = new categories();
            $insertCat->cat = htmlspecialchars($request->get('cat_name'));
            if($request->has('sub_cat_of') && $request->get('sub_cat_of') !== '') {
                $insertCat->sub_cat_of = $request->get('sub_cat_of');
            }
            $insertCat->cat_image = $fileSaveLocation;
            $insertCat->cat_of = htmlspecialchars($request->get('cat_of'));
            $insertCat->save();

            return response()->json([
                "message" => "category added successfully.",
                "success" => [
                    "cat" => htmlspecialchars($request->get("cat_name")),
                    'sub_cat_of' => $request->get('sub_cat_of')
                ]
            ]);
        }

        return response()->json([
            "message" => "there was an error in your code",
            "errors" => $errors
        ])->setStatusCode("401");
    }

    public function newCourse(create_new_course $request)
    {
        $errors = [];

        /**
         *
         * CHECK OWNER OF COURSE
         *
         */
        if (
        is_null(
            User::find($request->get("owner"))
        )
        ) {
            /**
             *
             * INVALID OWNER ID
             *
             */
            $errors["owner"][] = 'entered owner id is invalid.';
        }

        /**
         *
         * CHECK OWNER IS TEACHER OR NO
         *
         */
        elseif (
            (int)User::find($request->get('owner'))->account_type !== 2
        ) {
            /**
             *
             * OWNER OF COURSE JUST CAN BE TEACHER
             *
             */
            $errors['owner'][] = 'owner of course just can be teacher.';
        }

        /**
         *
         * CHECK CATEGORY ID OF COURSE
         *
         */
        if(
        is_null(
            categories::find($request->get('cat_id'))->where('cat_of' , '=' , '1')->first()
        )
        ){
            /**
             *
             * ENTERED CATEGORY ID IS INCORRECT
             *
             */
            $errors['cat_id'][] = 'entered category id is incorrect.';
        }

        /**
         *
         * CHECK PRICE AFTER OFF
         *
         */
        if($request->has('after_off')){
            /**
             *
             * IN REQUEST THERE IS A PARAMETER FOR AFTER OFF
             *
             */
            if((int)$request->get('after_off') === null){
                /**
                 *
                 * ERROR IN PRICE AFTER OFF
                 *
                 */
                $errors['after_off'][] = 'price of product in off, just can be integer.';
            }elseif((int)$request->get('after_off') > (int)$request->get('price')){
                /**
                 *
                 * PRICE AFTER OFF CAN'T BE MORE THAN PRICE BEFORE OFF
                 *
                 */
                $errors['after_off'][] = 'price after off can\'t be more than price before off.';
            }
        }

        /**
         *
         * ADD COURSE IF THERE AREN'T ANY ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             *
             * CODE WITHOUT ERROR
             *
             */

            /**
             *
             * IMAGE CHECK
             *
             */
            $fileSaveLocation = md5(mt_rand(1 , 100)).date("Y-m-d,H:i:s");
            if(!is_dir(base_path() . "/private/". $fileSaveLocation)){
                /**
                 *
                 * THIS DIRECTORY IS INVALID, MUST CREATE FOLDER
                 *
                 */
                if (!mkdir($concurrentDirectory = base_path() . "/private/" . $fileSaveLocation, 0777, true) && !is_dir($concurrentDirectory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
            }
            $file_name = $request->image->getClientOriginalName();
            $request->file('image')->move(base_path()."/private/".$fileSaveLocation , $file_name);
            if(ftp_upload::ftp_upload_cdn_one($file_name , $fileSaveLocation)){
                /**
                 *
                 * FILE UPLOADED INTO CDN FILE
                 *
                 */
                $fileSaveLocation = $this->args['cdn_server'] . "/$fileSaveLocation" . "/".$file_name;

            }else{
                /**
                 *
                 * UPLOAD WASN'T SUCCESSFULLY
                 *
                 */
                $errors['image'][] = 'upload image into cdn server failed.';
            }

            /**
             *
             * IMAGE CHECK
             *
             */
            $mobileBannerImage = md5(mt_rand(1 , 100)).date("Y-m-d,H:i:s");
            if(!is_dir(base_path() . "/private/". $mobileBannerImage)){
                /**
                 *
                 * THIS DIRECTORY IS INVALID, MUST CREATE FOLDER
                 *
                 */
                if (!mkdir($concurrentDirectory = base_path() . "/private/" . $mobileBannerImage, 0777, true) && !is_dir($concurrentDirectory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
            }
            $file_name = $request->mobile_banner->getClientOriginalName();
            $request->file('mobile_banner')->move(base_path()."/private/".$mobileBannerImage , $file_name);
            if(ftp_upload::ftp_upload_cdn_one($file_name , $mobileBannerImage)){
                /**
                 *
                 * FILE UPLOADED INTO CDN FILE
                 *
                 */
                $mobileBannerImage = $this->args['cdn_server'] . "/$mobileBannerImage" . "/".$file_name;

            }else{
                /**
                 *
                 * UPLOAD WASN'T SUCCESSFULLY
                 *
                 */
                $errors['mobile_banner'][] = 'upload mobile banner image into cdn server failed.';
            }

            /**
             *
             * BIG BANNER IMAGE CHECK
             *
             */
            $bigBannerLocation = md5(mt_rand(1 , 100)).date("Y-m-d,H:i:s");
            if(!is_dir(base_path() . "/private/". $bigBannerLocation)){
                /**
                 *
                 * THIS DIRECTORY IS INVALID, MUST CREATE FOLDER
                 *
                 */
                if (!mkdir($concurrentDirectory = base_path() . "/private/" . $bigBannerLocation, 0777, true) && !is_dir($concurrentDirectory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
            }
            $file_name = $request->big_banner->getClientOriginalName();
            $request->file('big_banner')->move(base_path()."/private/".$bigBannerLocation , $file_name);
            if(ftp_upload::ftp_upload_cdn_one($file_name , $bigBannerLocation)){
                /**
                 *
                 * FILE UPLOADED INTO CDN FILE
                 *
                 */
                $bigBannerLocation = $this->args['cdn_server'] . "/$bigBannerLocation" . "/".$file_name;

            }else{
                /**
                 *
                 * UPLOAD WASN'T SUCCESSFULLY
                 *
                 */
                $errors['big_banner'][] = 'upload image into cdn server failed.';
            }


            /**
             *
             * CHECKING ERRORS COUNT AGAIN
             *
             */
            if(count($errors) < 1){
                $addCourse = new school_courses();
                $addCourse->course_name = htmlspecialchars($request->get("course_name"));
                $addCourse->course_description = htmlspecialchars($request->get('course_description'));
                $addCourse->price_irr = (int)htmlspecialchars($request->get("price"));
                $addCourse->spotplayer_code = $request->get('spot_code');
                $addCourse->big_banner = $bigBannerLocation;
                $addCourse->mobile_baner = $mobileBannerImage;
                $addCourse->cover_image = $fileSaveLocation;
                if($request->has("after_off")){
                    $addCourse->irr_price_after_off = (int)$request->get('after_off');
                }else{
                    $addCourse->irr_price_after_off = (int)$request->get('price');
                }
                $addCourse->owner = (int)htmlspecialchars($request->get('owner'));
                $addCourse->cat_id = $request->get('cat_id');
                $addCourse->status = 0;
                $addCourse->save();
                $addCourse->owner = User::find($request->get('owner'));
                return redirect("https://api.khiyal.art/adm/course_added_success?cid=".$addCourse->id);
                /**return response()->json([
                'message' => 'course added successfully.',
                'success' => $addCourse
                ]);*/
            }

        }
        return response()->json([
            "message" => "The given data was invalid.",
            "errors" => $errors
        ], 401);

    }

    public function addImageToCourse($courseId, addImageToCourse $request)
    {
        $errors = [];


        /**
         *
         * CHECK COURSE ID BE CORRECT
         *
         */
        if (
        is_null(
            school_courses::find($courseId)
        )
        ) {
            /**
             *
             * IF COURSE ID BE INCORRECT
             *
             */
            $errors["course_id"][] = "entered course id is invalid.";

        }


        /**
         *
         * ADD IMAGE IF COURSE ID WAS TRUE
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             *
             * THERE WEREN'T ANY ERRORS
             *
             */

            $newName = Carbon::now() . "_" . $request->file('product_image')->getClientOriginalName();
            $newName = md5($newName) . '.' . $request->file('product_image')->extension();
            $insertLocation = $request->product_image->storeAs('private', $newName);
            $upload = ftp_upload::ftp_upload_cdn_one($insertLocation, $newName);

            if ($upload) {
                /**
                 *
                 * FILE UPLOADED TO CDN SUCCESSFULLY
                 *
                 */
                $insertImage = school_courses::find($courseId)->coursesImages()->create([
                    "image_path" => htmlspecialchars($newName),
                    'image_alt' => htmlspecialchars($request->get("image_alt")),
                    "status" => 0
                ]);
                return response()->json([
                    "message" => "image added successfully",
                    "image_path" => htmlspecialchars($newName),
                    'image_alt' => htmlspecialchars($request->get("image_alt")),
                ]);
            }
            /**
             *
             * UPLOAD WASN'T SUCCESSFULLY
             *
             */
            return response()->json([
                "message" => "upload wasn't successfully.",
                'errors' => [
                    "server" => "there is a problem in our CDN server, try again after several minutes."
                ]
            ])->setStatusCode("500");


        }

        /**
         *
         * SHOW ERRORS
         *
         */
        return response()->json([
            "message" => "upload wasn't successfully",
            'errors' => (object)$errors
        ])->setStatusCode("401");
    }

    public function addFeatureToCourse($courseId, addFeatureToCourse $request)
    {
        $errors = [];

        /**
         *
         * CHECK COURSE ID BE TRUE
         *
         */
        if (
        is_null(
            school_courses::find($courseId)
        )
        ) {
            /**
             *
             * IF COURSE ID BE INCORRECT
             *
             */
            $errors["course_id"][] = "entered course id is invalid.";

        }

        /**
         *
         * ADD FEATURE IF THERE AREN'T ANY ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             *
             * CODE WITHOUT ERROR
             *
             */
            $insert = school_courses::find($courseId)->courseFeatures()->create([
                "index" => htmlspecialchars($request->get("index")),
                "value" => htmlspecialchars($request->get("value")),
                "status" => 0
            ]);

            return response()->json([
                "message" => "feature added successfully",
                'success' => [
                    "index" => htmlspecialchars($request->get("index")),
                    "value" => htmlspecialchars($request->get("value")),
                ]
            ]);
        }
        return response()->json([
            "message" => "insert feature wasn't successfully.",
            "errors" => (object)$errors
        ])->setStatusCode("401");

    }

    public function addSectionToCourse($courseId, school_course_add_section $request)
    {
        $errors = [];

        /**
         *
         * CHECK COURSE ID BE TRUE
         *
         */
        if (
        is_null(
            school_courses::find($courseId)
        )
        ) {
            /**
             *
             * COURSE ID IS INVALID
             *
             */
            $errors["course"][] = 'course id is invalid.';
        }

        /**
         *
         * ADD SECTION IF THERE AREN'T ANY ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             *
             * THERE AREN'T ANY ERRORS
             *
             */

            school_courses::find($courseId)->courseSections()->save(
                new coursed_titles([
                    "title" => htmlspecialchars($request->get("title")),
                    'description' => $request->get('description') ,
                    "status" => 1
                ])
            );

            return response()->json([
                "message" => "section added to course successfully.",
                'success' => [
                    'course_id' => htmlspecialchars($courseId),
                    'section_name' => htmlspecialchars($request->get("title"))
                ]
            ]);

        }

        return response()->json([
            'message' => "entered data is invalid.",
            'errors' => $errors
        ], 401);

    }

    public function addBookToSell(add_pdf_book_to_sell $request)
    {
        $errors = [];

        /**
         *
         * CHECK FORMAT OF PRICE
         *
         */
        if (
            (int)$request->get("irr_price") != $request->get('irr_price')
        ) {
            /**
             *
             * FORMAT OF IRR PRICE IS INCORRECT
             *
             */
            $errors["irr_price"][] = 'format of irr price is incorrect.';
        }

        /**
         *
         * CHECK CATEGORY ID OF BOOK
         *
         */
        if(
        is_null(
            categories::find($request->get('cat_id'))->where('cat_of' , '=' , '1')->first()
        )
        ){
            /**
             *
             * ENTERED CATEGORY ID IS INCORRECT
             *
             */
            $errors['cat_id'][] = 'entered category id is incorrect.';
        }

        if (
            count($errors) < 1
        ) {
            /**
             *
             * EVERYTHING WITHOUT ERROR
             *
             */

            if (
            $request->file('book')->storeAs(
                'private/pdf_books/', htmlspecialchars($request->book->getClientOriginalName())
            )
            ) {
                /**
                 *
                 * FILE SAVED
                 *
                 */
                $addBook = new pdf_books();
                $addBook->title = htmlspecialchars($request->get("title"));
                $addBook->description = htmlspecialchars($request->get("description"));
                $addBook->cat_id = $request->get('cat_id');
                $addBook->irr_price = (int)$request->get('irr_price');
                $addBook->path = $request->book->getClientOriginalName();
                $addBook->save();

                return response()->json([
                    "message" => "book added into server successfully",
                    "success" => [
                        'path' => htmlspecialchars($request->book->getClientOriginalName()),
                        "name" => htmlspecialchars($request->get("title")),
                        "description" => htmlspecialchars($request->get('description')),
                        'irr_price' => $request->get("irr_price")
                    ]
                ]);

            }

            return response()->json([
                'message' => 'adding book wasn\'t successfully',
                "errors" => [
                    "server" => "there was an internal error in our server."
                ]
            ])->setStatusCode("500");

        }

        return response()->json([
            'message' => 'entered data is invalid.',
            "errors" => $errors
        ])->setStatusCode("401");


    }

    public function showPendingFeatures(admin_panding_features_show $request)
    {
        $errors = [];


        /**
         *
         * CHECK PAGE NUMBER IS CORRECT OR USE ONE INSTEAD
         *
         */
        if (
            $request->has('p')
            &&
            (int)$request->get('p') == $request->get('p')
        ) {
            /**
             *
             * PAGE NUMBER IS CORRECT
             *
             */
            $pageNumber = (int)$request->get('p');
        } else {
            /**
             *
             * FORMAT OF PAGE NUMBER MUST BE NUMBER
             *
             */
            $pageNumber = 1;
        }


        /**
         *
         * CHECK LIMIT OF PAGE RESULTS IS CORRECT OR NO
         *
         */
        if (
            $request->has('c')
            &&
            (int)$request->get("c") == $request->get("c")
        ) {
            /**
             *
             * FORMAT OF LIMIT PAGE RESULTS IS TRUE
             *
             */
            $limit = (int)$request->get("c");
        } else {
            /**
             *
             * INCORRECT FORMAT
             *
             */
            $limit = 30;
        }


        if (
            count($errors) < 1
        ) {
            /**
             *
             * CODE WITHOUT ERROR
             *
             */
            $result = products_features::where("status", "=", "0")->skip($limit * ($pageNumber - 1))->limit($limit)->get();
            return response()->json([
                "message" => 'getting data was successfully.',
                'success' => $result
            ]);

        }

        return response()->json([
            "message" => "entered data is invalid.",
            'errors' => $errors
        ], 401);

    }

    public function showPendingImages(admin_pending_images_show $request)
    {
        $errors = [];

        /**
         *
         * CHECK PAGE NUMBER IS CORRECT OR USE ONE INSTEAD
         *
         */
        if (
            $request->has('p')
            &&
            (int)$request->get('p') == $request->get('p')
        ) {
            /**
             *
             * PAGE NUMBER IS CORRECT
             *
             */
            $pageNumber = (int)$request->get('p');
        } else {
            /**
             *
             * FORMAT OF PAGE NUMBER MUST BE NUMBER
             *
             */
            $pageNumber = 1;
        }


        /**
         *
         * CHECK LIMIT OF PAGE RESULTS IS CORRECT OR NO
         *
         */
        if (
            $request->has('c')
            &&
            (int)$request->get("c") == $request->get("c")
        ) {
            /**
             *
             * FORMAT OF LIMIT PAGE RESULTS IS TRUE
             *
             */
            $limit = (int)$request->get("c");
        } else {
            /**
             *
             * INCORRECT FORMAT
             *
             */
            $limit = 30;
        }


        if (
            count($errors) < 1
        ) {
            /**
             *
             * CODE WITHOUT ERROR
             *
             */
            $result = products_images::where("status", "=", "0")->offset($limit * ($pageNumber - 1))->limit($limit)->get();

            return response()->json([
                'message' => 'getting data was successfully.',
                'success' => $result
            ]);

        }

        return response()->json([
            "message" => "entered data is invalid.",
            'errors' => (object)$errors
        ], 401);

    }

    public function acceptImageForCourse(admin_accept_penging_image $request)
    {
        $errors = [];

        $this->middleware("admin");

        /**
         *
         * CHECK IMAGE ID IS CORRECT OR NO
         *
         */
        if (
        is_null(
            products_images::find($request->get("image_id"))
        )
        ) {
            /**
             *
             * IMAGE ID IS INVALID
             *
             */
            $errors["image_id"] = 'entered image id is invalid.';
        } elseif (
            (int)products_images::find($request->get("image_id"))->status !== 0
            &&
            (int)products_images::find($request->get("image_id"))->status !== -1
        ) {
            /**
             *
             * ENTERED IMAGE ID ISN'T ON PENDING OR REJECTED, CHECK STATUS OF IMAGE
             *
             */
            if (
                (int)products_images::find($request->get("image_id"))->status !== 1
            ) {
                /**
                 *
                 * IMAGE HAD BEEN ACCEPTED PREVIOUSLY
                 *
                 */
                $errors["image_id"][] = 'image had been accepted previously.';
            } elseif (
                (int)products_images::find($request->get("image_id"))->status !== -2
            ) {
                /**
                 *
                 * IMAGE HAVEN'T BEEN SUBMIT FOR ACCEPT OR REJECT BY ADMIN
                 *
                 */
                $errors["image_id"][] = 'image isn\'t on pending.';
            }
        }


        /**
         *
         * ACCEPT IMAGE IF THERE AREN'T ANY ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            $image = products_images::find($request->get('image_id'));
            $image->status = 1;
            $image->save();

            return response()->json([
                'message' => 'image accepted successfully.',
                'success' => $image
            ]);

        }

        /**
         *
         * SHOW ERRORS
         *
         */
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ])->setStatusCode("401");

    }

    public function rejectImageForCourse(admin_reject_pending_image $request)
    {
        $errors = [];

        $this->middleware("admin");

        /**
         *
         * CHECK IMAGE ID IS CORRECT OR NO
         *
         */
        if (
        is_null(
            products_images::find($request->get("image_id"))
        )
        ) {
            /**
             *
             * IMAGE ID IS INVALID
             *
             */
            $errors["image_id"] = 'entered image id is invalid.';
        } elseif (
            (int)products_images::find($request->get("image_id"))->status !== 0
            &&
            (int)products_images::find($request->get("image_id"))->status !== 1
        ) {
            /**
             *
             * ENTERED IMAGE ID ISN'T ON PENDING OR REJECTED, CHECK STATUS OF IMAGE
             *
             */
            if (
                (int)products_images::find($request->get("image_id"))->status !== -1
            ) {
                /**
                 *
                 * IMAGE HAD BEEN REJECTED PREVIOUSLY
                 *
                 */
                $errors["image_id"][] = 'image had been rejected previously.';
            } elseif (
                (int)products_images::find($request->get("image_id"))->status !== -2
            ) {
                /**
                 *
                 * IMAGE HAVEN'T BEEN SUBMIT FOR ACCEPT OR REJECT BY ADMIN
                 *
                 */
                $errors["image_id"][] = 'image isn\'t on pending.';
            }
        }

        /**
         *
         * REJECT IMAGE IF THERE AREN'T ANY ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            $image = products_images::find($request->get('image_id'));
            $image->status = -1;
            $image->save();

            return response()->json([
                'message' => 'image rejected successfully.',
                'success' => $image
            ]);

        }

        /**
         *
         * SHOW ERRORS
         *
         */
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ])->setStatusCode("401");

    }

    public function acceptFeatureForCourse(admin_accept_penging_image $request)
    {
        $errors = [];

        $this->middleware("admin");

        /**
         *
         * CHECK FEATURE ID IS CORRECT OR NO
         *
         */
        if (
        is_null(
            products_features::find($request->get("feature_id"))
        )
        ) {
            /**
             *
             * FEATURE ID IS INVALID
             *
             */
            $errors["feature_id"] = 'entered feature id is invalid.';
        } elseif (
            (int)products_features::find($request->get("feature_id"))->status !== 0
            &&
            (int)products_features::find($request->get("feature_id"))->status !== -1
        ) {
            /**
             *
             * ENTERED FEATURE ID ISN'T ON PENDING OR REJECTED, CHECK STATUS OF FEATURE
             *
             */
            if (
                (int)products_features::find($request->get("feature_id"))->status !== 1
            ) {
                /**
                 *
                 * FEATURE HAD BEEN ACCEPTED PREVIOUSLY
                 *
                 */
                $errors["feature_id"][] = 'feature had been accepted previously.';
            } elseif (
                (int)products_features::find($request->get("feature_id"))->status !== -2
            ) {
                /**
                 *
                 * FEATURE HAVEN'T BEEN SUBMIT FOR ACCEPT OR REJECT BY ADMIN
                 *
                 */
                $errors["feature_id"][] = 'feature isn\'t on pending.';
            }
        }


        /**
         *
         * ACCEPT FEATURE IF THERE AREN'T ANY ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            $feature = products_features::find($request->get('feature_id'));
            $feature->status = 1;
            $feature->save();

            return response()->json([
                'message' => 'feature accepted successfully.',
                'success' => $feature
            ]);

        }

        /**
         *
         * SHOW ERRORS
         *
         */
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ])->setStatusCode("401");

    }

    public function rejectFeatureForCourse(admin_reject_pending_image $request)
    {
        $errors = [];

        $this->middleware("admin");

        /**
         *
         * CHECK FEATURE ID IS CORRECT OR NO
         *
         */
        if (
        is_null(
            products_features::find($request->get("feature_id"))
        )
        ) {
            /**
             *
             * FEATURE ID IS INVALID
             *
             */
            $errors["feature_id"] = 'entered feature id is invalid.';
        } elseif (
            (int)products_features::find($request->get("feature_id"))->status !== 0
            &&
            (int)products_features::find($request->get("feature_id"))->status !== 1
        ) {
            /**
             *
             * ENTERED FEATURE ID ISN'T ON PENDING OR REJECTED, CHECK STATUS OF IMAGE
             *
             */
            if (
                (int)products_features::find($request->get("feature_id"))->status !== -1
            ) {
                /**
                 *
                 * FEATURE HAD BEEN REJECTED PREVIOUSLY
                 *
                 */
                $errors["feature_id"][] = 'feature had been rejected previously.';
            } elseif (
                (int)products_features::find($request->get("feature_id"))->status !== -2
            ) {
                /**
                 *
                 * FEATURE HAVEN'T BEEN SUBMIT FOR ACCEPT OR REJECT BY ADMIN
                 *
                 */
                $errors["feature_id"][] = 'feature isn\'t on pending.';
            }
        }

        /**
         *
         * REJECT FEATURE IF THERE AREN'T ANY ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            $feature = products_features::find($request->get('feature_id'));
            $feature->status = -1;
            $feature->save();

            return response()->json([
                'message' => 'feature rejected successfully.',
                'success' => $feature
            ]);

        }

        /**
         *
         * SHOW ERRORS
         *
         */
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);

    }

    public function seeAllPendingExams(admin_seeAllPendingExams $request)
    {
        $errors = [];
        $this->middleware('admin');
        $resultInfo = [
            'c' => 30,
            'p' => 1
        ];

        /**
         *
         * CHECK TO SEE DOES ADMIN SEND INFORMATION TO EDIT COUNT OF RESULTS OR PAGE NUMBER
         *
         */
        if (
            $request->has("c")
            &&
            (int)$request->get('c') == $request->get('c')
            &&
            (int)$request->get('c') >= 1
        ) {
            /**
             *
             * CHANGE NUMBER OF RESULTS IN EACH PAGE
             *
             */
            $resultInfo['c'] = (int)$request->get('c');
        } // AND IF:
        if (
            $request->has("p")
            &&
            (int)$request->get('p') == $request->get('p')
            &&
            (int)$request->get('p') >= 1
        ) {
            /**
             *
             * CHANGE PAGE NUMBER
             *
             */
            $resultInfo['p'] = (int)$request->get('p');
        }

        /**
         *
         * SHOW RESULTS  IF THERE AREN'T ANY ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             * THERE AREN'T ANY ERRORS
             */
            $info = exams::where('status', '=', 0)->get();
            foreach ($info as $index => $value) {
                $info->$index->questions = $info->$index->showAllQuestions;
            }
            return response()->json([
                'message' => 'exams received from database successfully.',
                'success' => $info
            ]);
        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ])->setStatusCode('401');

    }

    public function acceptExam(admin_acceptExam $request)
    {
        $errors = [];
        $this->middleware('admin');

        /**
         *
         * CHECK EXAM ID IS CORRECT OR NO
         *
         */
        if (
        is_null(
            exams::find($request->get('exam_id'))
        )
        ) {
            /**
             *
             * EXAM ID IS INCORRECT
             *
             */
            $errors['exam_id'][] = 'exam id is incorrect.';
        }
        /**
         *
         * CHECK EXAM ID IS CORRECT OR NO
         *
         */
        if (
        is_null(
            exams::find($request->get('exam_id'))
        )
        ) {
            /**
             *
             * EXAM ID IS INCORRECT
             *
             */
            $errors['exam_id'][] = 'exam id is incorrect.';
        }

        /**
         *
         * CHECK EXAM BE ON PENDING
         *
         */
        if (
            (int)exams::find($request->get('exam_id'))->status !== 0
        ) {
            /**
             *
             * CHECK STATUS OF EXAM
             *
             */
            if (
                (int)exams::find($request->get('exam_id'))->status === 1
            ) {
                /**
                 *
                 * THIS EXAM HAD BEEN ACCEPTED PREVIOUSLY
                 *
                 */
                $errors['exam_id'][] = 'this exam had been accepted previously.';
            } elseif (
                (int)exams::find($request->get('exam_id'))->status === -1
            ) {
                /**
                 *
                 * THIS EXAM HAD BEEN REJECTED PREVIOUSLY, IF YOU WANNA ACCEPT IT, IT'S OWNER MUST SUBMIT IT TO REVIEW
                 *
                 */
                $errors['exam_id'][] = 'this exam had been rejected previously, if you wanna accept it, it\'s teacher must request to review it.';
            } elseif (
                (int)exams::find($request->get('exam_id'))->status === -2
            ) {
                /**
                 *
                 * ENTERED EXAM ID IS NOT IN PENDING LIST
                 *
                 */
                $errors['exam_id'][] = 'this exam isn\'t on pending.';
            }

        }


        /**
         *
         * ACCEPT EXAM IF THERE AREN'T ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             *
             * NO ERRORS :)
             *
             */
            $exam = exams::find($request->get('exam_id'));
            $exam->status = 1;
            $exam->save();

            return response()->json([
                'message' => 'exam accepted successfully.',
                'success' => $exam
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], "401");

    }

    public function rejectExam(admin_rejectExam $request)
    {
        $errors = [];
        $this->middleware('admin');

        /**
         *
         * CHECK EXAM ID IS CORRECT OR NO
         *
         */
        if (
        is_null(
            exams::find($request->get('exam_id'))
        )
        ) {
            /**
             *
             * EXAM ID IS INCORRECT
             *
             */
            $errors['exam_id'][] = 'exam id is incorrect.';
        }
        /**
         *
         * CHECK EXAM ID IS CORRECT OR NO
         *
         */
        if (
        is_null(
            exams::find($request->get('exam_id'))
        )
        ) {
            /**
             *
             * EXAM ID IS INCORRECT
             *
             */
            $errors['exam_id'][] = 'exam id is incorrect.';
        }

        /**
         *
         * CHECK EXAM BE ON PENDING
         *
         */
        if (
            (int)exams::find($request->get('exam_id'))->status !== 0
        ) {
            /**
             *
             * CHECK STATUS OF EXAM
             *
             */
            if (
                (int)exams::find($request->get('exam_id'))->status === 1
            ) {
                /**
                 *
                 * THIS EXAM HAD BEEN ACCEPTED PREVIOUSLY
                 *
                 */
                $errors['exam_id'][] = 'this exam had been accepted previously.';
            } elseif (
                (int)exams::find($request->get('exam_id'))->status === -1
            ) {
                /**
                 *
                 * THIS EXAM HAD BEEN REJECTED PREVIOUSLY
                 *
                 */
                $errors['exam_id'][] = 'this exam had been rejected previously.';
            } elseif (
                (int)exams::find($request->get('exam_id'))->status === -2
            ) {
                /**
                 *
                 * ENTERED EXAM ID IS NOT IN PENDING LIST
                 *
                 */
                $errors['exam_id'][] = 'this exam isn\'t on pending.';
            }

        }


        /**
         *
         * REJECT EXAM IF THERE AREN'T ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             *
             * NO ERRORS :)
             *
             */
            $exam = exams::find($request->get('exam_id'));
            $exam->status = -1;
            $exam->save();

            return response()->json([
                'message' => 'exam rejected successfully.',
                'success' => $exam
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], "401");
    }

    public function addProduct(admin_addProduct $request)
    {
        $errors = [];

        /**
         *
         * CHECK CAT ID
         *
         */
        if (
        is_null(
            categories::find($request->get('cat_id'))->where('cat_of' , '=' , '2')->first()
        )
        ) {
            /**
             *
             * CAT ID IS INVALID
             *
             */
            $errors['cat_id'][] = 'entered cat id is incorrect.';
        }

        /**
         *
         * CHECK FORMAT OF PRICE BE NUMBER
         *
         */
        if (
            (int)$request->get('price') === null
        ) {
            /**
             *
             * FORMAT OF PRICE WAS INCORRECT
             *
             */
            $errors['price'][] = 'entered price must be number.';
        }

        /**
         *
         * CHECK PRICE AFTER OFF
         *
         */
        if($request->has('after_off')){
            /**
             *
             * IN REQUEST THERE IS A PARAMETER FOR AFTER OFF
             *
             */
            if((int)$request->get('after_off') === null){
                /**
                 *
                 * ERROR IN PRICE AFTER OFF
                 *
                 */
                $errors['after_off'][] = 'price of product in off, just can be integer.';
            }elseif((int)$request->get('after_off') > (int)$request->get('price')){
                /**
                 *
                 * PRICE AFTER OFF CAN'T BE MORE THAN PRICE BEFORE OFF
                 *
                 */
                $errors['after_off'][] = 'price after off can\'t be more than price before off.';
            }
        }
        /**
         *
         * CHECK STOCK NUMBER
         *
         */
        if((int)$request->get('stock') === null){
            /**
             *
             * ENTERED NUMBER FOR PRODUCTS IN STOCK IS INCORRECT
             *
             */
            $errors['stock'][] = 'entered number for count of products in stock is incorrect.';
        }elseif((int)$request->get('stock') < 0){
            /**
             *
             * NUMBER OF PRODUCTS IN STOCK CAN'T BE NEGATIVE
             *
             */
            $errors['stock'][] = 'number of products in stock can\'t be negative.';
        }

        /**
         *
         * IMAGE CHECK
         *
         */
        $fileSaveLocation = md5(mt_rand(1 , 100)).date("Y-m-d,H:i:s");
        if(!is_dir(base_path() . "/private/". $fileSaveLocation)){
            /**
             *
             * THIS DIRECTORY IS INVALID, MUST CREATE FOLDER
             *
             */
            if (!mkdir($concurrentDirectory = base_path() . "/private/" . $fileSaveLocation, 0777, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
        $file_name = $request->image->getClientOriginalName();
        $request->file('image')->move(base_path()."/private/".$fileSaveLocation , $file_name);
        if(ftp_upload::ftp_upload_cdn_one($file_name , $fileSaveLocation)){
            /**
             *
             * FILE UPLOADED INTO CDN FILE
             *
             */
            $fileSaveLocation = $this->args['cdn_server'] . "/$fileSaveLocation" . "/".$file_name;
        }else{
            /**
             *
             * UPLOAD WASN'T SUCCESSFULLY
             *
             */
            $errors['image'][] = 'upload image into cdn server failed.';
        }

        /**
         *
         * CHECK BRAND
         *
         */
        if(
        is_null(
            brands::find($request->get('brand'))
        )
        ){
            /**
             *
             * BRAND ID IS INCORRECT
             *
             */
            $errors['brand'][] = 'brand is is incorrect.';
        }
        /**
         *
         * INSERT PRODUCT IF THERE AREN'T ERRORS
         *
         */
        if (
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR :)
             *
             */

            $newProduct = new ecommerce_products();
            $newProduct->product_name = htmlspecialchars($request->get('product_name'));
            $newProduct->product_description = $request->get('product_description');
            $newProduct->price_irr = (int)$request->get('price');
            if($request->has("after_off")){
                $newProduct->price_irr_after_off = (int)$request->get('after_off');
            }else{
                $newProduct->price_irr_after_off = (int)$request->get('price');
            }
            $newProduct->cover_image = $fileSaveLocation;
            $newProduct->category = $request->get('cat_id');
            $newProduct->brand_id = $request->get('brand');
            $newProduct->save();

            return response()->json([
                'message' => 'product inserted successfully.',
                'success' => $newProduct
            ]);

//            return redirect()->route('add_image' , "pid=".$newProduct->id);
        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => $errors
        ], "401");

    }

    public function deleteProduct(admin_delete_product $request){
        $errors = [];

        /**
         *
         * CHECK PRODUCT ID BE CORRECT
         *
         */
        if(
        is_null(
            ecommerce_products::find($request->get('product_id'))
        )
        ){
            /**
             *
             * INVALID PRODUCT ID
             *
             */
            $errors['product_id'][] = 'entered product id is invalid.';
        }

        /**
         *
         * DELETE PRODUCT IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR
             *
             */

            $product = ecommerce_products::find($request->get('product_id'));
            $product->delete();
            return response()->json([
                'message' => 'product deleted successfully.'
            ]);

        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ],401);

    }

    public function hide_product(hide_and_show_product $request){
        $errors = [];

        /**
         *
         * CHECK PRODUCT ID BE CORRECT
         *
         */
        if(
        is_null(
            ecommerce_products::find($request->get('product_id'))
        )
        ){
            /**
             *
             * INVALID PRODUCT ID
             *
             */
            $errors['product_id'][] = 'entered product id is invalid.';
        }

        /**
         *
         * hide product if there aren't any errors
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR
             *
             */

            # Deleting from users cart
            foreach (
                ecommerce_basket::where('product_id' , '=' , $request->get('product_id'))->get()
                as $item
            ){
                /**
                 *
                 * Force deleting
                 *
                 */
                $item->delete();
            }

            $product = ecommerce_products::find($request->get('product_id'));
            $product->status = 2;
            $product->save();
            return response()->json([
                'message' => 'product hides successfully.' ,
                'success' => $product
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors'  => $errors
        ],401);
    }

    public function unhide_product(hide_and_show_product $request){
        $errors = [];

        /**
         *
         * CHECK PRODUCT ID BE CORRECT
         *
         */
        if(
        is_null(
            ecommerce_products::find($request->get('product_id'))
        )
        ){
            /**
             *
             * INVALID PRODUCT ID
             *
             */
            $errors['product_id'][] = 'entered product id is invalid.';
        }

        /**
         *
         * unhide product if there aren't any errors
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR
             *
             */

            $product = ecommerce_products::find($request->get('product_id'));
            $product->status = 1;
            $product->save();
            return response()->json([
                'message' => 'product unhides successfully.' ,
                'success' => $product
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors'  => $errors
        ],401);
    }

    public function addFeatureToProduct(addFeatureToProduct $request)
    {
        $errors = [];

        /**
         *
         * CHECK PRODUCT ID
         *
         */
        if (
        is_null(
            ecommerce_products::find($request->get("product_id"))->where('status', '>', 0)->first()
        )
        ) {
            /**
             *
             * PRODUCT ID IS INCORRECT
             *
             */
            $errors['product_id'][] = 'entered product id is incorrect.';
        }

        /**
         *
         * ADD FEATURE IF THERE AREN'T ANY ERRORS
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
            $addFeature = ecommerce_products::find($request->get('product_id'))->productFeatures()->create([
                'index' => htmlspecialchars($request->get('index')),
                'value' => htmlspecialchars($request->get('value')),
                'status' => 1
            ]);
            return response()->json([
                'message' => 'feature added to product successfully.',
                'success' => $addFeature
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);
    }

    public function addImportantFeatureToProduct(addFeatureToProduct $request)
    {
        $errors = [];

        /**
         *
         * CHECK PRODUCT ID
         *
         */
        if (
        is_null(
            ecommerce_products::find($request->get("product_id"))->where('status', '>', 0)->first()
        )
        ) {
            /**
             *
             * PRODUCT ID IS INCORRECT
             *
             */
            $errors['product_id'][] = 'entered product id is incorrect.';
        }

        /**
         *
         * ADD IMPORTANT FEATURE IF THERE AREN'T ANY ERRORS
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
            $addFeature = ecommerce_products::find($request->get('product_id'));

            $feature = new ecommerce_product_important_feature([
                'index' => htmlspecialchars($request->get('index')),
                'value' => htmlspecialchars($request->get('value')),
            ]);
            $addFeature->importantFeatures()->save($feature);
            return response()->json([
                'message' => 'feature added to product successfully.',
                'success' => $addFeature
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);
    }

    public function addTechnicalSpecificationToProduct(addFeatureToProduct $request)
    {
        $errors = [];

        /**
         *
         * CHECK PRODUCT ID
         *
         */
        if (
        is_null(
            ecommerce_products::find($request->get("product_id"))->where('status', '>', 0)->first()
        )
        ) {
            /**
             *
             * PRODUCT ID IS INCORRECT
             *
             */
            $errors['product_id'][] = 'entered product id is incorrect.';
        }

        /**
         *
         * ADD IMPORTANT FEATURE IF THERE AREN'T ANY ERRORS
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
            $addFeature = ecommerce_products::find($request->get('product_id'));

            $feature = new ecommerce_product_technical_specifications([
                'index' => htmlspecialchars($request->get('index')),
                'value' => htmlspecialchars($request->get('value')),
            ]);
            $addFeature->technical_specifications()->save($feature);
            return response()->json([
                'message' => 'feature added to product successfully.',
                'success' => $addFeature
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);
    }

    public function addImageToProduct(addImageToProduct $request)
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
             * IF PRODUCT ID BE INCORRECT
             *
             */
            $errors["product_id"][] = "entered product id is invalid.";

        }


        /**
         *
         * ADD IMAGE IF PRODUCT ID WAS TRUE
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             *
             * THERE WEREN'T ANY ERRORS
             *
             */

            $fileSaveLocation = md5(mt_rand(1 , 100)).date("Y-m-d,H:i:s");
            if(!is_dir(base_path() . "/private/". $fileSaveLocation)){
                /**
                 *
                 * THIS DIRECTORY IS INVALID, MUST QCREATE FOLDER
                 *
                 */
                if (!mkdir($concurrentDirectory = base_path() . "/private/" . $fileSaveLocation, 0777, true) && !is_dir($concurrentDirectory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
            }
            $file_name = $request->product_image->getClientOriginalName();
            $request->file('product_image')->move(base_path()."/private/".$fileSaveLocation , $file_name);
            if(ftp_upload::ftp_upload_cdn_one($file_name , $fileSaveLocation)){
                /**
                 *
                 * FILE UPLOADED INTO CDN FILE
                 *
                 */
                $fileSaveLocation = $this->args['cdn_server'] . "/$fileSaveLocation" . "/".$file_name;
                $insertImage = ecommerce_products::find($request->get('product_id'))->productImages()->create([
                    "image_path" => htmlspecialchars($fileSaveLocation),
                    'image_alt' => htmlspecialchars($request->get("image_alt")),
                    "status" => 1
                ]);
            }else{
                /**
                 *
                 * UPLOAD WASN'T SUCCESSFULLY
                 *
                 */
                $errors['image'][] = 'upload image into cdn server failed.';
            }

        }
        if(count($errors) < 1){
            /**
             *
             * NO ERROR :)
             *
             */

            return response()->json([
                "message" => "upload was successfully"
            ]);

        }
        /**
         *
         * SHOW ERRORS
         *
         */
        return response()->json([
            "message" => "upload wasn't successfully",
            'errors' => (object)$errors
        ])->setStatusCode("401");
    }

    public function deleteImageOfProduct(deleteProductImage $request)
    {
        $errors = [];

        /**
         *
         * CHECK IMAGE ID BE CORRECT
         *
         */
        if (
        is_null(
            products_images::find($request->get("image_id"))
        )
        ) {
            /**
             *
             * IMAGE ID IS INCORRECT
             *
             */
            $errors['image_id'][] = 'entered image id is incorrect.';
        }

        /**
         *
         * DELETE IMAGE IF THERE AREN'T ANY ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             *
             * NO ERROR
             *
             */

            $image = products_images::find($request->get('image_id'));
            $image->delete();
            return response()->json([
                'message' => 'image deleted successfully.',
                'success' => $image
            ]);
        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);

    }

    public function deleteFeatureOfProduct(deleteProductFeature $request)
    {
        $errors = [];
        $this->middleware('admin');

        /**
         *
         * CHECK FEATURE ID BE CORRECT
         *
         */
        if (
        is_null(
            products_features::find($request->get("feature_id"))
        )
        ) {
            /**
             *
             * FEATURE ID IS INCORRECT
             *
             */
            $errors['feature_id'][] = 'entered feature id is incorrect.';
        }

        /**
         *
         * DELETE FEATURE IF THERE AREN'T ANY ERRORS
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             *
             * NO ERROR
             *
             */

            $image = products_features::find($request->get('feature_id'));
            $image->delete();
            return response()->json([
                'message' => 'feature deleted successfully.',
                'success' => $image
            ]);
        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);

    }

    public function updatePriceOfEcommerceProduct(updatePriceOfProduct $request)
    {
        $errors = [];
        $this->middleware('admin');

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
             * PRODUCT ID IS INVALID
             *
             */
            $errors['product_id'][] = 'entered product id is invalid.';
        }

        /**
         *
         * CHECK FORMAT OF PRICE BE CORRECT
         *
         */
        if (
            (int)$request->get('price') != $request->get('price')
        ) {
            /**
             *
             * PRICE ISN'T INTEGER
             *
             */
            $errors['price'][] = 'entered price must be integer.';
        }

        /**
         *
         * UPDATE PRICE IF THERE AREN'T ANY ERRORS
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

            $product = ecommerce_products::find($request->get("product_id"));
            $product->price_irr = (int)htmlspecialchars($request->get('price'));
            $product->save();

            return response()->json([
                'message' => 'price updated successfully.',
                'success' => $product
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);

    }

    public function updatePriceAfterOffOfEcommerceProduct(updatePriceOfProduct $request)
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
             * PRODUCT ID IS INVALID
             *
             */
            $errors['product_id'][] = 'entered product id is invalid.';
        }

        /**
         *
         * CHECK FORMAT OF PRICE BE CORRECT
         *
         */
        if (
            (int)$request->get('price') != $request->get('price')
        ) {
            /**
             *
             * PRICE ISN'T INTEGER
             *
             */
            $errors['price'][] = 'entered price must be integer.';
        }

        /**
         *
         * UPDATE PRICE IF THERE AREN'T ANY ERRORS
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

            $product = ecommerce_products::find($request->get("product_id"));
            $product->price_irr_after_off = (int)htmlspecialchars($request->get('price'));
            $product->save();

            return response()->json([
                'message' => 'price updated successfully.',
                'success' => $product
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);

    }

    public function updateNameOfProduct(updateNameOfProduct $request)
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
             * PRODUCT ID IS INVALID
             *
             */
            $errors['product_id'][] = 'entered product id is invalid.';
        }

        /**
         *
         * UPDATE NAME IF THERE AREN'T ANY ERRORS
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

            $product = ecommerce_products::find((int)$request->get("product_id"));
            $product->product_name = htmlspecialchars($request->get('product_name'));
            $product->save();

            return response()->json([
                'message' => 'product name updated successfully.',
                'success' => $product
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);
    }

    public function updateDescriptionOfProduct(updateDescriptionOfProduct $request)
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
             * PRODUCT ID IS INVALID
             *
             */
            $errors['product_id'][] = 'entered product id is invalid.';
        }

        /**
         *
         * UPDATE DESCRIPTION IF THERE AREN'T ANY ERRORS
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

            $product = ecommerce_products::find($request->get("product_id"));
            $product->product_description = $request->get('product_description');
            $product->save();

            return response()->json([
                'message' => 'product description updated successfully.',
                'success' => $product
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);
    }

    public function productInStockTrue(productIsInStock $request)
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
             * PRODUCT ID IS INVALID
             *
             */
            $errors['product_id'][] = 'entered product id is invalid.';
        }

        /**
         *
         * UPDATE PRODUCT IF THERE AREN'T ANY ERRORS
         *
         */
        if (
            count($errors) === 0
        ) {
            /**
             *
             * NO ERROR :)
             *
             */
            $product = ecommerce_products::find($request->get('product_id'));
            $product->stock = 1000;
            $product->save();

            return response()->json([
                'message' => 'product updated successfully.',
                'success' => $product
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);

    }

    public function productInStockFalse(productIsNotInStock $request)
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
             * PRODUCT ID IS INVALID
             *
             */
            $errors['product_id'][] = 'entered product id is invalid.';
        }

        /**
         *
         * UPDATE PRODUCT IF THERE AREN'T ANY ERRORS
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
            $product = ecommerce_products::find($request->get('product_id'));
            $product->stock = 0;
            $product->save();

            return response()->json([
                'message' => 'product updated successfully.',
                'success' => $product
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => (object)$errors
        ], 401);
    }

    public function addShippingMethod(admin_addShippingMethod $request)
    {
        $errors = [];
        /**
         *
         * CHECK FORMAT OF MIN
         *
         */
        if (
            (int)$request->get('min') != $request->get('min')
        ) {
            /**
             *
             * MINIMUM PRICE OF SHIPPING MUST BE INTEGER
             *
             */
            $errors['min'][] = 'minimum price of shipping address bust be integer.';
        }
        /**
         *
         * CHECK FORMAT OF MAX
         *
         */
        if (
            (int)$request->get('max') != $request->get('max')
        ) {
            /**
             *
             * MAXIMUM PRICE OF SHIPPING MUST BE INTEGER
             *
             */
            $errors['max'][] = 'maximum price of shipping address bust be integer.';
        }

        /**
         *
         * CHECK FORMAT OF PRICE
         *
         */
        if (
            (int)$request->get('price') != $request->get('price')
        ) {
            /**
             *
             * FORMAT OF PRICE MUST BE NUMBER
             *
             */
            $errors['price'][] = 'format of price must be number.';
        }

        /**
         *
         * ADD METHOD IF THERE AREN'T ANY ERRORS
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
            $shippingMethod = new shipping_methods();
            $shippingMethod->shipping_method = htmlspecialchars($request->get('shipping_method'));
            $shippingMethod->price_irr = htmlspecialchars($request->get('price'));
            $shippingMethod->min_buy = htmlspecialchars($request->get('min'));
            $shippingMethod->max_buy = htmlspecialchars($request->get('max'));
            $shippingMethod->save();

            return response()->json([
                'message' => 'shipping method added successfully.',
                'success' => $shippingMethod
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => $errors
        ]);
    }

    public function deleteShippingMethod(deleteShippingMethod $request)
    {
        $errors = [];
        $this->middleware('admin');

        /**
         *
         * CHECK SHIPPING ID BE CORRECT
         *
         */
        if (
        is_null(
            shipping_methods::find($request->get('shipping_id'))
        )
        ) {
            /**
             *
             * SHIPPING ID IS INCORRECT
             *
             */
            $errors['shipping_id'][] = 'entered shipping id is incorrect.';
        }

        /**
         *
         * DELETE SHIPPING METHOD IF THERE AREN'T ANY ERROR
         *
         */
        if (
        count($errors)
        ) {
            /**
             *
             * NO ERROR :)
             *
             */

            $shippingMethod = shipping_methods::find($request->get('shipping_id'));
            $shippingMethod->delete();

            return response()->json([
                'message' => 'shipping method deleted successfully.',
                'success' => $shippingMethod
            ]);
        }

        return response()->json([
            'message' => 'entered data is invalid.',
            'errors' => $errors
        ]);

    }

    public function addMajor(admin_add_major $request)
    {

        /**
         *
         * ADDING MAJOR TO DATABASE
         *
         */
        $major = new majors;
        $major->major = htmlspecialchars($request->get('major'));
        $major->save();
        return response()->json([
            'message' => 'major added successfully.',
            'success' => [
                'major' => htmlspecialchars($request->get('major'))
            ]
        ]);
    }

    public function add_study_area(admin_add_study_area $request)
    {

        /**
         *
         * ADDING STUDY AREA
         *
         */
        $addArea = new student_study_area;
        $addArea->study_area = htmlspecialchars($request->get("study_area"));
        $addArea->save();

        return response()->json([
            'message' => 'study area added successfully.' ,
            'success' => $addArea
        ]);
    }

    public function showAllEcommerceCategories (){
        $errors = [];

        /**
         *
         * SHOW DATA IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR :)
             *
             */

            $categories = categories::where('cat_of' , '=' , 2)->get();
            foreach ($categories as $ind => $val){
                $categories[$ind]->count = count(ecommerce_products::where('category' , '=' , $val->id)->get());
            }
            return response()->json([
                'message' => 'categories received successfully.' ,
                'success'  => $categories
            ]);
        }

    }

    public function showAllSchoolCategories (){
        $errors = [];

        /**
         *
         * SHOW DATA IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR :)
             *
             */

            $categories = categories::where('cat_of' , '=' , 1)->get();
            return response()->json([
                'message' => 'categories received successfully.' ,
                'success'  => $categories
            ]);
        }

    }

    public function showALlBrands (){
        $errors = [];

        /**
         *
         * SHOW DATA IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR :)
             *
             */

            $brands = brands::all();
            foreach($brands as $ind => $val){
                $brands[$ind]->count = count(ecommerce_products::where('brand_id' , '=' , $val->id)->get());
            }

            return response()->json([
                'message' => 'brands received successfully.' ,
                'success'  => $brands
            ]);
        }
    }

    public function add_brand (Request $request){
        $errors = [];

        /**
         *
         * ADD BRAND IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR
             *
             */
            $brands= new brands;
            $brands->brand = htmlspecialchars($request->get('brand'));
            $brands->save();
            return response()->json([
                'message' => 'brand added successfully.' ,
                'success' =>  $brands
            ]);
        }
    }

    public function add_color_to_available_colors (admin_add_available_colors $request){
        $errors = [];

        /**
         *
         * ADD COLOR IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR
             *
             */

            $color = new colors;
            $color->color = htmlspecialchars($request->get('color'));
            $color->color_code = htmlspecialchars($request->get('color_code'));
            $color->save();
            return response()->json([
                'message' => 'color added successfully.' ,
                'success' => $color
            ]);

        }
    }

    public function showAllColors (){
        $errors = [];

        /**
         *
         * SHOW DATA IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR :)
             *
             */

            $allColors = colors::all();
            return response()->json([
                'message' => 'all colors received successfully.',
                'success' => $allColors
            ]);
        }
        return response()->json([
            'message' => 'error in getting information from database' ,
            'errors'  => $errors
        ] , 401);

    }

    public function add_color_to_product (add_color_to_product $request){
        $errors = [];

        /**
         *
         * CHECK PRODUCT ID BE CORRECT
         *
         */
        if(
        is_null(
            ecommerce_products::find($request->get('product_id'))
        )
        ){
            /**
             *
             * ENTERED PRODUCT ID IN INVALID
             *
             */
            $errors['product_id'][] = 'entered product id is incorrect.';
        }

        /**
         *
         * CHECK COLOR ID BE CORRECT
         *
         */
        if(
        is_null(
            colors::find($request->get('color_id'))
        )
        ){
            /**
             *
             * ENTERED COLOR ID IN INVALID
             *
             */
            $errors['color_id'][] = 'entered color id is incorrect.';
        }

        /**
         *
         * ADD PRODUCT IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR
             *
             */
            $attachColor = ecommerce_products::find($request->get('product_id'))->showColors()->attach($request->get('color_id'));
            return response()->json([
                'message' => 'color added to product successfully.' ,
                'success' => $attachColor
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ]);

    }

    public function showListOfTeachers (){
        $errors = [];

        /**
         *
         * SHOW RESULT IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR
             *
             */
            $teachersList = User::where('account_type' , '=' , 2)->get();
            return response()->json([
                'message' => 'list of teachers received successfully.' ,
                'success' => $teachersList
            ]);
        }


    }

    public function showEcommerceOrders (Request $request){
        $errors = [];
        $requestData = [
            'count' => 30 ,
            'page'  => 0
        ];
        /**
         *
         * show list of orders if there aren't any errors
         *
         */
        if(
            count($errors)
            === 0
        ){
            /**
             *
             * no error
             *
             */
            if($request->has('count'))
                $requestData['count'] = $request->get('count');
            if($request->has('page'))
                $requestData['page'] = $request->get('page');

            $ordersList = orders::offset(($requestData['page'] - 1) * $requestData['count'])->limit($requestData['count'])->get();
            foreach ($ordersList as $ind => $val){
                $ordersList[$ind]->products;
                $ordersList[$ind]->user_info;
            }
            return response()->json([
                'message' => 'list of orders received successfully.' ,
                'success' => $ordersList
            ]);
        }
    }

    public function getOrderDetails(admin_get_order_details $request){
        $errors = [];

        /**
         *
         * check order id be correct
         *
         */
        if(
            is_null(
                orders::find($request->get('order_id'))
            )
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
         * get order information if there aren't any errors
         *
         */
        if(
            count(
                $errors
            ) === 0
        ){
            /**
             *
             * no error :)
             *
             */
            $order = orders::find($request->get('order_id'));
            $order->address_info;
            $order->products;
            foreach ($order->products as $ind => $val){
                $order->products[$ind]->productInformation;
            }
            $order->user_info;

            return response()->json([
                'message' => 'order information received from database.' ,
                'success' => $order
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ] , 401);
    }





    public function orderStatus0 ($orderId){
        $errors = [];

        /**
         *
         * check order id be correct
         *
         */
        if(
        is_null(
            orders::find($orderId)
        )
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
         * change status if there aren't any errors
         *
         */
        if(
            count($errors) === 0
        ){
            /**
             *
             * no error :)
             *
             */
            $order = orders::find($orderId);
            $order->status = 0;
            $order->save();
            return response()->json([
                'message' => 'status changed successfully.'
            ] , 204);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ] , 401);
    }

    public function orderStatus1 ($orderId){
        $errors = [];

        /**
         *
         * check order id be correct
         *
         */
        if(
        is_null(
            orders::find($orderId)
        )
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
         * change status if there aren't any errors
         *
         */
        if(
            count($errors) === 0
        ){
            /**
             *
             * no error :)
             *
             */
            $order = orders::find($orderId);
            $order->status = 1;
            $order->save();
            return response()->json([
                'message' => 'status changed successfully.'
            ] , 204);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ] , 401);
    }

    public function orderStatus2 ($orderId){
        $errors = [];

        /**
         *
         * check order id be correct
         *
         */
        if(
        is_null(
            orders::find($orderId)
        )
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
         * change status if there aren't any errors
         *
         */
        if(
            count($errors) === 0
        ){
            /**
             *
             * no error :)
             *
             */
            $order = orders::find($orderId);
            $order->status = 2;
            $order->save();
            return response()->json([
                'message' => 'status changed successfully.'
            ] , 204);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ] , 401);
    }

    public function orderStatus3 ($orderId){
        $errors = [];

        /**
         *
         * check order id be correct
         *
         */
        if(
        is_null(
            orders::find($orderId)
        )
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
         * change status if there aren't any errors
         *
         */
        if(
            count($errors) === 0
        ){
            /**
             *
             * no error :)
             *
             */
            $order = orders::find($orderId);
            $order->status = 3;
            $order->save();
            return response()->json([
                'message' => 'status changed successfully.'
            ] , 204);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ] , 401);
    }

    public function getAllUsers (){
        $errors = [];

        /**
         *
         * show list of users if there aren't any errors
         *
         */
        if(
            count(
                $errors
            ) === 0
        ){
            /**
             *
             * no error :)
             *
             */
            $users = User::all();
            return response()->json([
                'message' => 'list of users received successfully' ,
                'success' => $users
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ] , 401);
    }
}

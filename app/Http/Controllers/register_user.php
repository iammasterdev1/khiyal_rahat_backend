<?php

namespace App\Http\Controllers;

use App\Http\Requests\edit_profile;
use App\Http\Requests\enter_two_step_verification_code;
use App\Http\Requests\get_phone_for_register_r;
use App\Http\Requests\login;
use App\Http\Requests\forget_password_request;
use App\Http\Requests\mobile_verification_for_users;
use App\Http\Requests\tokenCheck;
use App\Http\Requests\user_register_ls;
use App\Models\ecommerce_basket;
use App\Models\ecommerce_products;
use App\Models\majors;
use App\Models\school_basket;
use App\Models\purchases_courses;
use App\Models\school_courses;
use App\Models\second_step_login_token;
use App\Models\student_study_area;
use App\Models\User;
use App\Models\users_register_verify_phone;
//use Illuminate\Http\Request;
use App\Models\users_register_get_phone;
use App\Http\Requests\get_profile;
class register_user extends Controller
{

    public $dashboard = [
        1 => "/dashboard/student",
        2 => "/dashboard/teacher",
        3 => "/dashboard/consultant"
    ];

    public function getProfileInfo(get_profile $request){
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
         * SHOW INFORMATION OF USER IF THERE AREN'T ANY ERRORS
         *
         */
        if(count($errors) < 1){
            /**
             *
             * NO ERROR :-)
             *
             */

            // GETTING INFORMATION OF USER
            $userInfo = User::where('token' , '=' , $request->get('token'))->first();

            /**
             *
             * check is this user teacher or no
             *
             */
            if($userInfo->account_type == 2){
                /**
                 *
                 * user is teacher
                 *
                 */
                $IdOfCoursesOfThisUser = school_courses::where('owner' , User::where('token' , '=' , $request->get('token'))->first()->id)->get();
                $totalEarningsTillNow = 0;
                foreach ($IdOfCoursesOfThisUser as $ind => $val){
                    $IdOfCoursesOfThisUser[$ind] = $val->id;
                }

                $purchasedCourses = purchases_courses::whereIn('course_id' , $IdOfCoursesOfThisUser)->get();
//                return($purchasedCourses);
                foreach ($purchasedCourses as $ind => $val){
                    $totalEarningsTillNow += $val->price;
                }
                $userInfo->countOfStudents = count(
                    $purchasedCourses
                );
                $userInfo->totalBuy = $totalEarningsTillNow;

            }
            return response()->json([
                'message' => 'information of user received from database.' ,
                'success' => $userInfo
            ]);
        }

        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors'  => $errors
        ] , 401);

    }

    public function getPhoneNumberForRegister(get_phone_for_register_r $request)
    {
        $errors = [];

        /**
         *
         * CHECK USER DOESN'T HAVE ACCOUNT
         *
         */
        if(
            !is_null(
                User::where('phone_number' , '=' , $request->get('phone_number'))->first()
            )
        ){
            /**
             *
             * USER HAS ACCOUNT
             *
             */
            $errors['phone_number'][] = 'entered phone number belongs to an account.';
        }

        /**
         *
         * CHECK IF CODE WAS SEND LESS THAN 2 MINUTES AGO
         *
         */
        elseif (
            count(
                users_register_get_phone::where("phone_number", "=", $request->get("phone_number"))->get()
            ) !== 0
        ) {
            /**
             *
             * IF CODE SEND LESS THAN 2 MINUTES AGO
             *
             */
            if(
                strtotime(users_register_get_phone::where("phone_number", "=", $request->get("phone_number"))->first()->created_at) > (time() - 120)
            )
            $errors["time"][] = 'You can request code after ' . (time() - strtotime(users_register_get_phone::where("phone_number", "=", $request->get("phone_number"))->orderBy('id' , 'DESC')->first()->created_at)) . " seconds";
        }

        /**
         *
         * CHECK IF EVERYTHING WAS WITHOUT ERROR, SEND VERIFICATION CODE
         *
         */
        if (
            count($errors) < 1
        ) {
            /**
             *
             * SENDING CODE
             *
             */

            $token = '';
            for ($i = 0; $i < 100; $i++) {
                /**
                 *
                 * CREATING TOKEN
                 *
                 * TOKEN MUST SENT TO PHONE VERIFICATION PAGE WITH VERIFICATION CODE THAT SENT TO PHONE NUMBER
                 *
                 */
                $token .= substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 51), 1);
            }
            // RANDOM CODE GENERATOR TO SEND SMS
            $verificationCode = mt_rand(10000, 99999);

            $insertRequestInDB = new users_register_get_phone();
            $insertRequestInDB->phone_number = $request->get("phone_number");
            $insertRequestInDB->verification_code = $verificationCode;
            $insertRequestInDB->token = $token;
            $insertRequestInDB->send_request_ip = $_SERVER["REMOTE_ADDR"];
            $insertRequestInDB->save();
            sms_sender::send_message(
                $request->get('phone_number'),
                $verificationCode
//                "کد تایید شما برای ثبت نام در سامانه ی خیال راحت \n $verificationCode"
            );
            return response()->json([
                "message" => "Verification code was send successfully.",
                'success' => [
                    "phone_number" => $request->get("phone_number"),
                    "token" => $token
                ]
            ]);
        }


        /**
         *
         * SHOW ERRORS
         *
         */
        return response()->json([
            "message" => "entered data was invalid",
            "errors" => $errors
        ] , 401);

        // END OF METHOD
    }

    public function verify_phone_number(mobile_verification_for_users $request)
    {
        $errors = [];
        /**
         *
         * CHECK VALIDATION OF TOKEN
         *
         */
        if (
        is_null(
            users_register_get_phone::where("token", "=", $request->get("token"))->first()
        )
        ) {
            /**
             *
             * IF TOKEN WAS INVALID
             *
             */
            $errors["token"][] = "entered token is invalid.";
        }

        /**
         *
         * CHECK EXPIRE TIME OF TOKEN IF IT'S VALID
         *
         */
        elseif (
            time() - strtotime(users_register_get_phone::where("token", "=", $request->get("token"))->first()->created_at) > 1200
        ) {
            /**
             *
             * TOKEN WAS CREATED BEFORE 20 MINUTES AGO
             *
             */
            $errors["token"][] = "entered token is expired.";
                return users_register_get_phone::where("token", "=", $request->get("token"))->first()->created_at;
        }

        /**
         *
         * CHECK IF REQUESTED FOR VERIFICATION AFTER THIS TOKEN
         *
         */
//        elseif (
//            users_register_get_phone::where('phone_number' , '=' , users_register_get_phone::where('token' , '=' , $request->get('token'))->first()->phone_number)->first()->token !== $request->get('token')
//        ) {
//            /**
//             *
//             * ANOTHER TOKEN CREATED AFTER THIS ONE
//             *
//             */
//            $errors["token"][] = "You have another verification request.";
//        }

        /**
         *
         * CHECK IS CODE VALID OR NO
         *
         */
        elseif (
            users_register_get_phone::where("token", "=", $request->get("token"))->orderBy('id' , 'desc')->first()->verification_code != $request->get("code")
        ) {
            /**
             *
             * IF VERIFICATION CODE WAS INVALID
             *
             */
            $errors["code"][] = "Entered verification code is invalid.";
        }

        /**
         *
         * VERIFY PHONE NUMBER IF EVERYTHING WAS OKAY
         *
         */
        if (count($errors) < 1) {
            /**
             *
             * CODE WITHOUT ERROR
             *
             */

            $completeVerificationInsert = new users_register_verify_phone;
            $completeVerificationInsert->phone_number = users_register_get_phone::where("token", "=", $request->get("token"))->first()->phone_number;
            $completeVerificationInsert->token = $request->get("token");
            $completeVerificationInsert->save();
            return response()->json([
                "message" => "phone number verified successfully",
                'success' => [
                    "token" => $request->get("token")
                ]
            ]);
        }
        return response()->json([
            "message" => "error in phone number verification.",
            "errors" => $errors
        ] , 401);
        // END OF METHOD
    }

    public function completeRegister(user_register_ls $request)
    {
        $errors = [];

        /**
         *
         * VALIDATE TOKEN
         *
         */
        if (
            is_null(
                users_register_verify_phone::where("token", "=", $request->get("token"))->first()
            )
        ) {
            /**
             *
             * IF ENTERED TOKEN WAS INVALID
             *
             */
            $errors["token"][] = "Entered token is invalid.";
        }

        /**
         *
         * CHECK EXPIRE TIME OF TOKEN IF IT'S VALID
         *
         */
        elseif (
            time() - strtotime(users_register_verify_phone::where("token", "=", $request->get("token"))->first()->created_at) > 1200
        ) {
            /**
             *
             * TOKEN WAS CREATED BEFORE 20 MINUTES AGO
             *
             */
            $errors["token"][] = "entered token is expired.";
        }

        /**
         *
         * ANOTHER CHECK TO SEE PHONE NUMBER ISN'T THERE IN DATABASE
         *
         */
        elseif (
        !is_null(
            User::where("phone_number", "=", users_register_verify_phone::where('token', '=', $request->get("token"))->first()->phone_number)->first()
        )
        ) {
            /**
             *
             * PHONE NUMBER IS TAKEN IN DATABASE
             *
             */
            $errors["phone_number"][] = "entered phone number is taken.";
        }

        /**
         *
         * CHECK ACCOUNT TYPE
         *
         */
        if (
            (int)$request->get("account_type") > 3
            ||
            (int)$request->get('account_type') <= 0
        ) {
            /**
             *
             * INVALID ACCOUNT TYPE
             *
             */
            $errors["account_type"][] = 'entered account type in invalid.';
        }

        /**
         *
         * CHECKING STUDY AREA AND MAJOR BE SENT FOR STUDENTS AND THEY BE CORRECT
         *
         */
        if((int)$request->get('account_type') === 1){
            /**
             *
             * THIS USER IS STUDENT
             *
             */

            // CHECK MAJOR BE SENT
            if(
                !$request->has('major')
            ){
                /**
                 *
                 * MAJOR IS REQUIRED BUT HAVEN'T BEEN SENT
                 *
                 */
                $errors['major'][] = 'Major field is required.';

            }

            /**
             *
             * CHECK MAJOR ID BE CORRECT
             *
             */
            elseif(
            is_null(
                majors::find($request->get('major'))
            )
            ){
                /**
                 *
                 * MAJOR FIELD IS INCORRECT
                 *
                 */
                $errors['major'][] = 'major field is incorrect.';
            }

            // CHECK STUDY AREA BE SENT
            if(
                !$request->has('study_area')
            ){
                /**
                 *
                 * STUDY AREA IS REQUIRED BUT HAVEN'T BEEN SENT
                 *
                 */
                $errors['study_area'][] = 'Study area field is required.';
            }


            /**
             *
             * CHECK STUDY AREA ID BE CORRECT
             *
             */
            elseif(
            is_null(
                majors::find($request->get('study_area'))
            )
            ){
                /**
                 *
                 * STUDY AREA FIELD IS INCORRECT
                 *
                 */
                $errors['study_area'][] = 'study area field is incorrect.';
            }

        }

        /**
         *
         * COMPLETE REGISTER IF EVERYTHING WAS WITHOUT ERROR
         *
         */
        if (count($errors) < 1) {
            /**
             * COMPLETE REGISTER
             */
            $token = '';
            for ($i = 0; $i <= 100; $i++) {
                /**
                 *
                 * CREATING TOKEN
                 *
                 * TOKEN MUST SENT TO PHONE VERIFICATION PAGE WITH VERIFICATION CODE THAT SENT TO PHONE NUMBER
                 *
                 */
                $token .= substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 51), 1);
            }

            $completeRegister = new User();
            $completeRegister->firstName = htmlspecialchars($request->get("firstName"));
            $completeRegister->lastName = htmlspecialchars($request->get("lastName"));
            $completeRegister->phone_number = htmlspecialchars(users_register_verify_phone::where('token', '=', $request->get("token"))->first()->phone_number);
            $completeRegister->phone_number_verified_at = htmlspecialchars(users_register_verify_phone::where('token', '=', $request->get("token"))->first()->created_at);
            $completeRegister->token = $token;
            if($request->has('province') && !is_null($request->get('province')))
                $completeRegister->province = htmlspecialchars($request->get('province'));
            if($request->has('email') && !is_null($request->get('email')))
                $completeRegister->email = htmlspecialchars($request->get('email'));
            $completeRegister->account_type = (int)$request->get("account_type");
            $completeRegister->password = password_hash($request->get("password"), PASSWORD_DEFAULT);
            if($request->has('major'))
                $completeRegister->major = $request->get('major');
            if($request->has('study_area'))
                $completeRegister->study_area = $request->get('study_area');
            $completeRegister->ip_address = $_SERVER['REMOTE_ADDR'];
            $completeRegister->save();

            return response()->json([
                'message' => 'register completed successfully.' ,
                'success' => $completeRegister
            ]);
        }
        return response()->json([
            "message" => "Register wasn't successfully",
            "errors" => $errors
        ] , 401);
        // END OF METHOD
    }

    public function edit_profile(edit_profile $request)
    {
        $errors = [];

        /**
         *
         * CHECK TOKEN
         *
         */
        if (
        is_null(
            User::where("token", "=", $request->get("token"))->get()
        )
        ) {
            /**
             *
             * Entered token is invalid
             *
             */
            $errors["token"][] = "entered token is invalid.";
        }


        /**
         * USE THIS SECTION IF YOU WANNA PASSWORD BE REQUIRED FOR EDITING PROFILE
         */
//        /**
//         *
//         * CHECK PASSWORD
//         *
//         */
//        if(
//            User::where("token" , "=" , $request->get("token"))->first()->password !== $request->get("password")
//        ){
//          $errors["password"][] = "entered password is incorrect.";
//        }


        /**
         *
         * Edit if everything was without error
         *
         */
        if (count($errors) < 1) {
            /**
             * EDITING
             */
            $profile = User::where("token", "=", $request->get('token'))->first();
            $profile->firstName = htmlspecialchars($request->get("firstName"));
            $profile->lastName  = htmlspecialchars($request->get("lastName"));
            if ($request->has("email")) {
                $profile->email = $request->get("email");
            }

            $profile->save();
            return response()->json([
                'message' => 'information edited successfully.' ,
                'success' => $profile
            ]);

        }

        return response()->json([
            "message" => "entered data is invalid.",
            "errors" => $errors
        ] , 401);

    }

    public function login(login $request)
    {
        $errors = [];
        /**
         *
         * CHECK PHONE NUMBER IS FOR AN ACCOUNT OR NO
         *
         */
        if (
            is_null(
                User::where('phone_number', "=", $request->get("phone_number"))->first()
            )
        ) {
            /**
             *
             * PHONE NUMBER DOESN'T BELONG TO AN ACCOUNT
             *
             */
            $errors["phone_number"][] = "entered phone number doesn't belong to an account.";
        }

        /**
         *
         * CHECKING PASSWORD
         *
         */
        elseif (
            !password_verify(
                $request->get('password'),
                User::where('phone_number', "=", $request->get("phone_number"))->first()->password
            )
        ){
            /**
             *
             * PASSWORD WAS INCORRECT
             *
             */
            $errors["password"][] = "entered password is invalid.";
            $errors['hash'][] =       User::where('phone_number', "=", $request->get("phone_number"))->first()->password;
        }


        /**
         *
         * IF LOGIN WAS SUCCESSFULLY
         *
         */
        if (count($errors) < 1) {
            /**
             *
             * COMPLETE LOGIN
             *
             */
            $token = '';
            for ($i = 0; $i <= 100; $i++) {
                /**
                 *
                 * CREATING TOKEN
                 *
                 * TOKEN MUST SENT TO PHONE VERIFICATION PAGE WITH VERIFICATION CODE THAT SENT TO PHONE NUMBER
                 *
                 */
                $token .= substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 51), 1);
            }

            $code = mt_rand(10000 , 99999);
            sms_sender::send_message(
                $request->get('phone_number') ,
$code
//                "کد تایید شما برای ورود به خیال راحت: \n $code"
            );
            $user = User::where('phone_number', "=", $request->get("phone_number"))->first();
            $secondStepLoginTokens = new second_step_login_token;
            $secondStepLoginTokens->user_id = $user->id;
            $secondStepLoginTokens->token = $token;
            $secondStepLoginTokens->code = $code;
            if($request->has('data')){
                if(!empty($request->get('data'))){
                    $secondStepLoginTokens->data = $request->get('data');
                }
            }
            $secondStepLoginTokens->save();

            return response()->json([
                "message" => 'code sent to user\'s mobile.',
                'success' => [
                    "token" => $token
                ]
            ]);

        }
        return response()->json([
            "message" => 'login wasn\'t successfully',
            "errors" => $errors
        ])->setStatusCode("401");
    }

    public function login_enter_code (enter_two_step_verification_code $request){
        $errors = [];
        $redirect = null;

        /**
         *
         * CHECK TOKEN BE CORRECT
         *
         */
        if(
            is_null(
                second_step_login_token::where('token' , '=' , $request->get('token'))->first()
            )
        ){
            /**
             *
             * ENTERED TOKEN IS INCORRECT
             *
             */
            $errors['token'][] = 'entered token is invalid.';
        }
//        /**
//         *
//         * CHECKING TIME OF CODE SENDING
//         *
//         */
//        elseif(time() - strtotime(second_step_login_token::where('token' , '=' , $request->get('token'))->first()->created_at) >60000){
//            /**
//             *
//             * CODE WAS SENT BEFORE 10 MINUTES AGO
//             *
//             */
//            $errors['token'][] = 'entered token is expired.';
//        }
        /**
         *
         * CHECK CODE IF TOKEN WAS CORRECT
         *
         */
        elseif(
            second_step_login_token::where('token' , '=' , $request->get('token'))->first()->code != $request->get('code')
        ){
            /**
             *
             * ENTERED CODE IS INCORRECT
             *
             */
            $errors['code'][] = 'entered code is invalid.';
        }



        /**
         *
         * COMPLETE LOGIN IF EVERYTHING WAS WITHOUT ERROR
         *
         */
        if(
            count($errors) < 1
        ) {
            /**
             *
             * NO ERROR :)
             *
             */


            /**
             *
             * check data
             *
             */
            if(
                second_step_login_token::where('token' , '=' , $request->get('token'))->first()->data !== null
            ){
                /**
                 *
                 * data isn't null
                 *
                 */
                $data = second_step_login_token::where('token' , '=' , $request->get('token'))->first()->data;
                if(
                    strlen($data) >= 7
                ){
                    /**
                     *
                     * more than 7 chars
                     *
                     */
                    if(substr($data , 0 , 6) === 'course'){
                        /**
                         *
                         * ordering course
                         *
                         */
                        $courseId = (int)substr($data , 6 , (strlen($data) - 6));
                        if($courseId >= 1){
                            if(
                            !is_null(
                                school_courses::find($courseId)
                            )
                            ){
                                //
                                if(
                                    count(
                                        school_basket::where('user_id' , second_step_login_token::where('token', '=', $request->get('token'))->first()->user_id)->where('productable_type' , 'App\Models\school_courses')->where('productable_id' , $courseId)->get()
                                    ) === 0
                                ){
                                    $courseInCart = new school_basket;
                                    $courseInCart->user_id = second_step_login_token::where('token', '=', $request->get('token'))->first()->user_id;
                                    $courseInCart->productable_type = 'App\Models\school_courses';
                                    $courseInCart->productable_id = $courseId;
                                    $courseInCart->save();
                                    $redirect = '/sch/basket';
                                }
                            }
                        }
                    }
                    elseif(substr($data , 0 , 6) === 'eco_pr'){
                        /**
                         *
                         * ordering product
                         *
                         */
                        $product_id = (int)substr($data , 6 , (strlen($data) - 6));
                        if($product_id >= 1){
                            if(
                            !is_null(
                                ecommerce_products::find($product_id)
                            )
                            ){
                                //
                                if(
                                    count(
                                        ecommerce_basket::where('user_id' , second_step_login_token::where('token', '=', $request->get('token'))->first()->user_id)->where('product_id' , $product_id)->get()
                                    ) === 0
                                ){
                                    $productInCart = new ecommerce_basket();
                                    $productInCart->user_id = second_step_login_token::where('token', '=', $request->get('token'))->first()->user_id;
                                    $productInCart->product_id = $product_id;
                                    $productInCart->save();
                                    $redirect= '/basket';
                                }
                            }
                        }
                    }
                }
            }

            $user = User::find(second_step_login_token::where('token', '=', $request->get('token'))->first()->user_id);
            $user->token = $request->get('token');
            $user->save();

            return response()->json([
                'message' => 'login was successfully.' ,
                'success' => $user ,
                'redirect' => $redirect
            ]);
        }

        /**
         *
         * SHOWING ERRORS
         *
         */
        return response()->json([
            'message' => 'there was error in login process.' ,
            'errors'  => $errors
        ] , 401);
    }

    public function checkToken(tokenCheck $request)
    {
        /**
         *
         * CHECK IS TOKEN RIGHT OR NO
         *
         */
        if(
            is_null(
                User::where('token' , '=' , $request->get('token'))->first()
            )
        ){
            /**
             *
             * TOKEN IS INCORRECT
             *
             */
            return response()->json([
                'message' => 'entered token is invalid.'
            ] , 401);
        }

        /**
         *
         * TOKEN IS VALID
         *
         */
        return response()->json([
            'message' => 'entered token is valid.'
        ] , 200);

    }

    public function showAllStudyFields (){
        $allStudyFields = student_study_area::all();
        return response()->json($allStudyFields);
    }

    public function showAllMajors (){
        $allMajors = majors::all();
        return response()->json($allMajors);
    }

    public function forgetPassword (){

    }
}

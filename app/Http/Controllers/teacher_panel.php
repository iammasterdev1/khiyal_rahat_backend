<?php

namespace App\Http\Controllers;

use App\Http\Requests\addFeatureToCourse;
use App\Http\Requests\addImageToCourse;
use App\Http\Requests\createNewExam;
use App\Http\Requests\submitExam;
use App\Http\Requests\teacher_addQuestionToExam;
use App\Http\Requests\teacher_answer_question;
use App\Models\exam_questions;
use App\Models\exams;
use App\Models\products_images;
use App\Models\questionAndAnswer_answers;
use App\Models\questionAndAnswer_questions;
use App\Models\school_courses;
use App\Models\User;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;

class teacher_panel extends Controller
{

//    public function newCourse ( $request){
//        $errors = [];
//        /**
//         *
//         * CHECK TOKEN IS VALID OR NO
//         *
//         */
//        if(
//            is_null(
//                User::where("token" , "=" , $request->get("token"))->first()
//            )
//        ){
//            /**
//             *
//             * ENTERED TOKEN IS INVALID
//             *
//             */
//            $errors["token"][] = "entered token is invalid.";
//        }
//
//        /**
//         *
//         * CHECK USER BE TEACHER IF TOKEN WAS VALID
//         *
//         */
//        elseif(
//            (int)User::where("token" , "=" , $request->get("token"))->first()->account_type !== 2
//        ){
//            /**
//             *
//             * USER ISN'T TEACHER AND CAN'T SHARE COURSE
//             *
//             */
//            $errors["token"][] = "access denied, just teachers can share course.";
//        }
//
//        /**
//         *
//         * ADD COURSE IF THERE AREN'T ANY ERRORS
//         *
//         */
//        if(
//            count($errors) < 1
//        ){
//            /**
//             *
//             * CODE WITHOUT ERROR
//             *
//             */
//            $addCourse = new school_courses();
//            $addCourse->course_name = htmlspecialchars($request->get("course_name"));
//            $addCourse->course_description = htmlspecialchars($request->get('course_description'));
//            $addCourse->price_irr = (int)htmlspecialchars($request->get("price"));
//            $addCourse->owner = User::where("token" , "=" , $request->get("token"))->first()->id;
//            $addCourse->save();
//
//            return response()->json($addCourse);
//
//        }
//
//        return response()->json([
//            "message" => "adding course wasn't successfully" ,
//            "errors" => $errors
//        ]);
//
//    }

//    public function addImageToCourse($courseId , addImageToCourse $request){
//        $errors = [];
//        /**
//         *
//         * CHECK TOKEN IS VALID OR NO
//         *
//         */
//        if(
//        is_null(
//            User::where("token" , "=" , $request->get("token"))->first()
//        )
//        ){
//            /**
//             *
//             * ENTERED TOKEN IS INVALID
//             *
//             */
//            $errors["token"][] = "entered token is invalid.";
//        }
//
//        /**
//         *
//         * CHECK USER BE TEACHER IF TOKEN WAS VALID
//         *
//         */
//        elseif(
//            (int)User::where("token" , "=" , $request->get("token"))->first()->account_type !== 2
//        ){
//            /**
//             *
//             * USER ISN'T TEACHER AND CAN'T SHARE COURSE
//             *
//             */
//            $errors["token"][] = "access denied, just teachers can share course.";
//        }
//
//        /**
//         *
//         * CHECK COURSE ID BE TRUE
//         *
//         */
//        elseif(
//            is_null(
//                school_courses::find($courseId)
//            )
//        ){
//            /**
//             *
//             * IF COURSE ID BE INCORRECT
//             *
//             */
//            $errors["course_id"][] = "entered course id is invalid.";
//
//        }
//
//
//        /**
//         *
//         * ADD IMAGE IF COURSE ID WAS TRUE
//         *
//         */
//        if(
//            count($errors) < 1
//        ){
//            /**
//             *
//             * THERE WEREN'T ANY ERRORS
//             *
//             */
//
//            $newName = Carbon::now() . "_" . $request->product_image->getClientOriginalName();
//            $insertLocation = $request->file('product_image')->storeAs("../tpm" , htmlspecialchars($newName) , 'private');
//            $upload = ftp_upload::ftp_upload_cdn_one($insertLocation , htmlspecialchars($newName));
//
//            if($upload){
//                /**
//                 *
//                 * FILE UPLOADED TO CDN SUCCESSFULLY
//                 *
//                 */
//                $insertImage = school_courses::find($courseId)->coursesImages()->create([
//                    "image_path" => htmlspecialchars($newName) ,
//                    'image_alt' => htmlspecialchars($request->get("image_alt"))
//                ]);
//                return response()->json([
//                    "message" => "image added successfully" ,
//                    "image_path" => htmlspecialchars($newName) ,
//                    'image_alt' => htmlspecialchars($request->get("image_alt"))
//                ]);
//            }
//            /**
//             *
//             * UPLOAD WASN'T SUCCESSFULLY
//             *
//             */
//            return response()->json([
//                "message" => "upload wasn't successfully." ,
//                'errors' => [
//                    "server" => "there is a problem in our CDN server, try again after several minutes."
//                ]
//            ])->setStatusCode("500");
//
//
//
//        }
//
//        /**
//         *
//         * SHOW ERRORS
//         *
//         */
//        return response()->json([
//            "message" => "upload wasn't successfully" ,
//            'errors' => $errors
//        ])->setStatusCode("401");
//    }
//
//    public function addFeatureToCourse ($courseId , addFeatureToCourse $request){
//        $errors = [];
//        /**
//         *
//         * CHECK TOKEN IS VALID OR NO
//         *
//         */
//        if(
//        is_null(
//            User::where("token" , "=" , $request->get("token"))->first()
//        )
//        ){
//            /**
//             *
//             * ENTERED TOKEN IS INVALID
//             *
//             */
//            $errors["token"][] = "entered token is invalid.";
//        }
//
//        /**
//         *
//         * CHECK USER BE TEACHER IF TOKEN WAS VALID
//         *
//         */
//        elseif(
//            (int)User::where("token" , "=" , $request->get("token"))->first()->account_type !== 2
//        ){
//            /**
//             *
//             * USER ISN'T TEACHER AND CAN'T SHARE COURSE
//             *
//             */
//            $errors["token"][] = "access denied, just teachers can share course.";
//        }
//
//        /**
//         *
//         * CHECK COURSE ID BE TRUE
//         *
//         */
//        elseif(
//        is_null(
//            school_courses::find($courseId)
//        )
//        ){
//            /**
//             *
//             * IF COURSE ID BE INCORRECT
//             *
//             */
//            $errors["course_id"][] = "entered course id is invalid.";
//
//        }
//
//        /**
//         *
//         * ADD FEATURE IF THERE AREN'T ANY ERRORS
//         *
//         */
//        if(
//            count($errors) < 1
//        ){
//            /**
//             *
//             * CODE WITHOUT ERROR
//             *
//             */
//                school_courses::find($courseId)->courseFeatures()->create([
//                    "index" => htmlspecialchars($request->get("index")) ,
//                    "value" => htmlspecialchars($request->get("value"))
//                ]);
//
//                return response()->json([
//                    "message" => "feature added successfully" ,
//                    "index" => htmlspecialchars($request->get("index")) ,
//                    "value" => htmlspecialchars($request->get("value"))
//                ]);
//        }
//        return response()->json([
//            "message" => "insert feature wasn't successfully." ,
//            "errors" => $errors
//        ])->setStatusCode("401");
//
//    }

    public function createNewExamForCourse (createNewExam $request){
        $errors = [];

         $this->middleware('teacher');

        /**
         *
         * CHECK COURSE ID IS TRUE OR NO
         *
         */
        if(
            is_null(
                school_courses::find($request->get('course_id'))->where('status' , '=' , 1)->first()
            )
        ){
            /**
             *
             * COURSE ID IS INVALID
             *
             */
            $errors["course_id"][] = 'entered course id is invalid.';
        }


         /**
          *
          * CHECK TEACHER BE OWNER OF COURSE
          *
          */

         elseif(
            (int)User::where('token' , '=' , $request->get('token'))->first()->id !== (int)school_courses::find($request->get('course_id'))->where('status' , '=' , '1')->first()->owner
         ){
             /**
              *
              * THIS TEACHER ISN'T ADMIN OF COURSE
              *
              */
             $errors['token'][] = 'just teacher of course can add exam to course.';
         }

         /**
          *
          * ADD EXAM IF THERE AREN'T ANY ERRORS
          *
          */
         if(
             count($errors) < 1
         ){
             /**
              *
              * NO ERRORS
              *
              */
             $addExam = new exams();
             $addExam->exam_name = htmlspecialchars($request->get('exam_name'));
             $addExam->course_id = htmlspecialchars($request->get('course_id'));
             $addExam->save();

             return response()->json($addExam);
         }

         return response()->json([
             'message' => 'entered data is invalid.' ,
             'errors'  => $errors
         ])->setStatusCode("401");

    }

    public function addQuestionToExam(teacher_addQuestionToExam $request){
        $errors = [];
        $this->middleware('teacher');

        /**
         *
         * CHECK EXAM ID IS CORRECT OR NO
         *
         */
        if(
            is_null(
                exams::find($request->get('exam_id'))
            )
        ){
            /**
             *
             * EXAM ID IS INCORRECT
             *
             */
            $errors['exam_id'][] = 'exam id is incorrect.';
        }

        /**
         *
         * CHECK USER BE OWNER OF EXAM & ITS COURSE
         *
         */
        elseif(
            (int)User::where('token' , '=' , $request->get('token'))->first()->id !== (int)school_courses::find(exams::find($request->get('exam_id'))->course_id)->where('status' , '=' , 1)->first()->id
        ){
            /**
             *
             * THIS TEACHER ISN'T OWNER OF COURSE AND EXAM
             *
             */
            $errors['exam_id'][] = 'just teacher of course can change exam.';
        }

        /**
         *
         * CHECK FORMAT OF TRUE ANSWER
         *
         */
        if(
            (int)$request->get('answer_number') > 4 && (int)$request->get('answer_number') < 1
        ){
            /**
             *
             * CORRECT ANSWER MUST BE BETWEEN 1 AND 4
             *
             */
            $errors['answer_number'][] = 'correct answer must be between one and four.';
        }

        /**
         *
         * ADD QUESTION TO COURSE IF THERE AREN'T ANY ERRORS
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERRORS
             *
             */
            $addQuestion = new exam_questions();
            $addQuestion->question = htmlspecialchars($request->get('question'));
            $addQuestion->answer_one = htmlspecialchars($request->get('q1'));
            $addQuestion->answer_two = htmlspecialchars($request->get('q2'));
            $addQuestion->answer_three = htmlspecialchars($request->get('q3'));
            $addQuestion->answer_four = htmlspecialchars($request->get('q4'));
            $addQuestion->true_answer = $request->get('answer_number');
            $addQuestion->exam_id = $request->get('exam_id');
            $addQuestion->save();

            return response()->json([
                'message' => 'question added to exam successfully.'  ,
                'success' => $addQuestion
            ]);

        }

        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors' => $errors
        ])->setStatusCode("401");
    }

    public function submitExamToAdminsCheck (submitExam $request){
        $errors = [];
        $this->middleware('teacher');

        /**
         *
         * CHECK EXAM ID IS CORRECT OR NO
         *
         */
        if(
        is_null(
            exams::find($request->get('exam_id'))
        )
        ){
            /**
             *
             * EXAM ID IS INCORRECT
             *
             */
            $errors['exam_id'][] = 'exam id is incorrect.';
        }

        /**
         *
         * CHECK USER BE OWNER OF EXAM & ITS COURSE
         *
         */
        elseif(
            (int)User::where('token' , '=' , $request->get('token'))->first()->id !== (int)school_courses::find(exams::find($request->get('exam_id'))->course_id)->where('status' , '=' , 1)->first()->id
        ){
            /**
             *
             * THIS TEACHER ISN'T OWNER OF COURSE AND EXAM
             *
             */
            $errors['exam_id'][] = 'just teacher of course can change exam.';
        }

        /**
         *
         * CHECK IS EXAM ON -2 STATUS OR NO
         *
         */
        elseif(
            (int)exams::find($request->get('exam_id'))->status !== -2
            ||
            (int)exams::find($request->get('exam_id'))->status !== -1

        ){
            /**
             *
             * CAN'T SUBMIT THIS EXAM
             *
             */
            if(
                (int)exams::find($request->get('exam_id'))->status === 0
            ){
                /**
                 *
                 * THIS EXAM IS ON PENDING NOW
                 *
                 */
                $errors['exam_id'][] = 'this exam is on pending now.';
            }
            elseif(
                (int)exams::find($request->get('exam_id'))->status === 1
            ){
                /**
                 *
                 * THIS EXAM HAD BEEN ACCEPTED BY ADMIN PREVIOUSLY
                 *
                 */
                $errors['exam_id'][] = 'this exam had been accepted previously.';
            }
            else{
                /**
                 *
                 * THERE IS BUG IN SYSTEM
                 *
                 */
                $errors['others'][] = 'there is bug in system.';
            }

        }

        /**
         *
         * CHECK DOES EXAM HAVE MINIMUM ONE QUESTION OR NO
         *
         */
        elseif(
            is_null(
                exam_questions::where('exam_id' , '=' , $request->get('exam_id'))->get()
            )
        ){
            /**
             *
             * THERE AREN'T ANY QUESTIONS
             *
             */
            $errors['exam_id'][] = 'you must have minimum one question for exam to submit.';
        }
        /**
         *
         * IF THERE AREN'T ANY BUGS IN CODE
         *
         */
        if(
            count($errors) < 1
        ){
            /**
             *
             * NO ERROR IN CODES
             *
             */
            $exam = exams::find($request->get('exam_id'));
            $exam->status = 0;
            $exam->save();

            response()->json([
                'message' => 'exam submitted successfully.' ,
                'success' => (object)$exam
            ]);
        }
        response()->json([
            'message' => 'entered data is invalid.' ,
            'errors' => $errors
        ])->setStatusCode("401");

    }
    public function answerQuestion(teacher_answer_question $request){
        $errors = [];
        $this->middleware('teacher');

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


}

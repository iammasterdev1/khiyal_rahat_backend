<?php

namespace App\Http\Controllers;

use App\Http\Requests\get_list_of_chats;
use App\Http\Requests\getChatsList;
use App\Http\Requests\new_message;
use App\Http\Requests\new_chat;
use App\Http\Requests\chat_messages as chat_messages_validator;
use App\Models\teacher_student_chats;
use App\Models\chat_message;
use App\Models\purchases_courses;
use App\Models\school_courses;
use App\Models\User;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Object_;

class message extends Controller {

    public function startChatWithTeacherOrGetInfo (new_chat $request){
        $errors = [];

        if(
            school_courses::find($request->get('course_id')) === null
        ){
            /**
             *
             * entered course id is invalid
             *
             */
            $errors['course_id'][] = 'entered course id is invalid.';
        }
        elseif (
            school_courses::find($request->get('course_id'))->chat == 1
        ){
            /**
             *
             * check does user have chat with teacher for this course or no
             *
             */
            if(
                count(
                    teacher_student_chats::where(
                        'user_id' , User::where('token' , $request->get('token'))->first()->id
                    )->where(
                        'course_id' , $request->get('course_id')
                    )->get()
                ) !== 0
            ){
                /**
                 *
                 * user have an open chat with teacher of this course
                 *
                 */
                #check if this chat is expired, don't show user
                if(
                    count(
                        teacher_student_chats::where('user_id' , User::where('token' , $request->get('token'))->first()->id)->where('course_id' , $request->get('course_id'))->get()
                    ) !== 0
                    &&
                    (
                        time()
                        -
                        strtotime(
                            teacher_student_chats::where('user_id' , User::where('token' , $request->get('token'))->first()->id)->where('course_id' , $request->get('course_id'))->first()->expire_date
                        )
                    ) >= 0
                ){
                    /**
                     *
                     * chat with this teacher has been expired
                     *
                     */
                    response()->json([
                        'message' => 'chat with this teacher has been expired' ,
                    ] , 204);
                }else{
                    return response()->json([
                        'message' => 'you already have open chat with this user' ,
                        'success' => teacher_student_chats::where('user_id' , User::where('token' , $request->get('token'))->first()->id)->where('course_id' , $request->get('course_id'))->first()
                    ]);
                }
            }else{
                /**
                 *
                 * user doesn't have chat with teacher of this course
                 *
                 * check if user has this course and purchased before 3 weeks, make a chat with teacher
                 *
                 * and if course was for before 3 weeks ago, make a chat that's expired
                 *
                 */
                if(
                    count(
                        purchases_courses::where(
                            'user_id' ,
                            User::where('token' , $request->get('token'))->first()->id
                        )->where(
                            'course_id' ,
                            $request->get('course_id')
                        )->get()
                    ) === 0
                ){
                    /**
                     *
                     * entered course is not purchased by user.
                     *
                     */
                    response()->json([
                        'message' => 'access denied.' ,
                        'errors' => [
                            'you don\'t have access to this course becouse didn\'t purchase it.'
                        ]
                    ], 403);
                }elseif(
                    count(
                        purchases_courses::where(
                            'user_id' ,
                            User::where('token' , $request->get('token'))->first()->id
                        )->where(
                            'course_id' ,
                            $request->get('course_id')
                        )->get()
                    ) !== 0
                ){
                    $days = 21;
                    /**
                     *
                     * user have purchased this course.
                     *
                     * check time of buying this course purchase.
                     */
                    if(
                        time() - strtotime(
                            purchases_courses::where(
                                'user_id' ,
                                User::where('token' , $request->get('token'))->first()->id
                            )->where(
                                'course_id' ,
                                $request->get('course_id')
                            )->first()->created_at
                        ) > ($days *  86400)
                    ){
                        /**
                         *
                         * user have this course purchased but chatting is expired
                         *
                         */
                        $addChat = new teacher_student_chats;
                        $addChat->user_id = User::where('token' , $request->get('token'))->first()->id;
                        $addChat->course_id = $request->get('course_id');
                        $addChat->order_id = purchases_courses::where(
                            'user_id' ,
                            User::where('token' , $request->get('token'))->first()->id
                        )->where(
                            'course_id' ,
                            $request->get('course_id')
                        )->first()->id;
                        $addChat->expire_date = Carbon::now()->add($days . ' days')->toDateTimeString();
                        $addChat->save();
                        return response()->json([
                            'message' => 'chat created, but time of chat is expired.'
                        ] , 204);
                    }elseif(
                        time() - strtotime(
                            purchases_courses::where(
                                'user_id' ,
                                User::where('token' , $request->get('token'))->first()->id
                            )->where(
                                'course_id' ,
                                $request->get('course_id')
                            )->first()->created_at
                        ) < ($days *  86400)
                    ){
                        /**
                         *
                         * user purchase course and have time to chat
                         *
                         */
                        $addChat = new teacher_student_chats;
                        $addChat->user_id = User::where('token' , $request->get('token'))->first()->id;
                        $addChat->course_id = $request->get('course_id');
                        $addChat->order_id = purchases_courses::where(
                            'user_id' ,
                            User::where('token' , $request->get('token'))->first()->id
                        )->where(
                            'course_id' ,
                            $request->get('course_id')
                        )->first()->id;
                        $addChat->expire_date = Carbon::now()->add($days . ' days')->toDateTimeString();
                        $addChat->save();
                        return response()->json([
                            'message' => 'chat created successfully.' ,
                            'success' => $addChat
                        ]);
                    }

                }
            }
        }else{
            return response()->json([
                'message' => 'this course doesn\'t have chat' ,
            ],204);
        }

        return response()->json([
            'message' => 'internal server error.'
        ] , 500);

    }

    public function getListOfAllMessages (get_list_of_chats $request){
        $errors = [];
        $messages = [];

        $coursesOfThisUser = [];
        /**
         *
         * get all courses that this user is it's teacher
         *
         */
        foreach (
            school_courses::where('owner' , User::where('token' , $request->get('token'))->first()->id)->get()
            as $ind => $val
        ){
            /**
             *
             * adding courses information to courses list
             *
             */
            $coursesOfThisUser[] = $val->id;
        }
        /**
         *
         * getAllMessages
         *
         */
        if(
            count(
                teacher_student_chats::where('user_id' , User::where('token' , $request->get('token'))->first()->id)->get()
            ) !== 0
            ||
            count(
                teacher_student_chats::whereIn('course_id' , $coursesOfThisUser)->get()
            ) !== 0
        ){
            /**
             *
             * user has chats as student
             *
             */
            foreach (teacher_student_chats::where('user_id' , User::where('token' , $request->get('token'))->first()->id)->get() as $ind => $val){
                /**
                 *
                 * adding messages to list
                 *
                 */

                // CHECK CHAT DOESN'T BE EMPTY
                if(
                    count(
                        chat_message::where(
                            'chat_id' ,
                            $val->id
                        )->get()
                    ) !== 0
                ){
                    $teacherInformation = User::find(school_courses::find($val->course_id)->owner);
                    $userInfo = (object)[];
                    $userInfo->firstName  = $teacherInformation->firstName;
                    $userInfo->lastName   = $teacherInformation->lastName;
                    $userInfo->profile    = $teacherInformation->profile;
                    $course_info = (object)school_courses::find($val->course_id);
                    $val->course_info = $course_info;
                    if(strtotime($val->created_at) >= time()){
                        $val->expired = true;
                    }else{
                        $val->expired = false;
                    }
                    $val->contact = $userInfo;
                    $messages[] = $val;

                }

            }

            /**
             *
             * add chats as teacher
             *
             */
            foreach (teacher_student_chats::whereIn('course_id' , $coursesOfThisUser)->get() as $ind => $val){
                /**
                 *
                 * adding messages to list
                 *
                 */

                // CHECK CHAT DOESN"T BE EMPTY
                if(
                    count(
                        chat_message::where(
                            'chat_id' ,
                            $val->id
                        )->get()
                    ) !== 0
                ) {
                    $contact = User::find($val->user_id);
                    $userInfo = (object)[];
                    $course_info = (object)school_courses::find($val->course_id);
                    $userInfo->firstName = $contact->firstName;
                    $userInfo->lastName = $contact->lastName;
                    $userInfo->profile = $contact->profile;
                    $val->contact = $userInfo;
                    if (strtotime($val->created_at) >= time()) {
                        $val->expired = true;
                    } else {
                        $val->expired = false;
                    }
                    $val->course_info = $course_info;
                    $messages[] = $val;
                }
            }
        }
        return response()->json([
            'message' => 'messages received from database successfully.' ,
            'success' => $messages
        ]);
    }

    public function newMessage (new_message $request)
    {
        $errors = [];
        if (
            count(
                teacher_student_chats::find($request->get('chat_id'))->where('user_id', User::where('token', $request->get('token'))->first()->id)->get()
            ) !== 0
            ||
            (
                count(
                    school_courses::find(teacher_student_chats::find($request->get('chat_id'))->course_id)->get()
                ) !== 0
                &&
                school_courses::find(teacher_student_chats::find($request->get('chat_id'))->course_id)->owner
                ==
                User::where('token', $request->get('token'))->first()->id
            )

        ) {
            $chat = teacher_student_chats::find($request->get('chat_id'));
            $chat->updated_at = Carbon::now();
            $chat->save();
            /**
             *
             * user is student or teacher of this chat
             *
             */
            require "jdate.php";

            $userId = User::where('token', $request->get('token'))->first()->id;
            $addMessage = new chat_message;
            $addMessage->chat_id = $request->get('chat_id');
            $addMessage->message = htmlspecialchars($request->get('message'));
            $addMessage->user_id = $userId;
            $addMessage->save();
            $addMessage->time = jdate('d F Y H:i:s' , strtotime($addMessage->created_at));
            return response()->json([
                'message' => 'message entered successfully.',
                'success' => $addMessage
            ]);
        } else {
            return response()->json([
                'message' => 'access denied',
                'errors' => [
                    'you aren\'t student of this'
                ]
            ], 403);
        }

    }

    public function getAllMessagesOfChat(chat_messages_validator $request){
        $errors = [];

        if (
            count(
                teacher_student_chats::where('user_id', User::where('token', $request->get('token'))->first()->id)->where('id' , '=' , $request->get('chat_id'))->get()
            ) !== 0
            ||
            (
                count(
                    school_courses::find(teacher_student_chats::find($request->get('chat_id'))->course_id)->get()
                ) !== 0
                &&
                school_courses::find(teacher_student_chats::find($request->get('chat_id'))->course_id)->owner
                    ==
                User::where('token', $request->get('token'))->first()->id
            )
        ) {
            /**
             *
             * user is student or teacher of this chat
             *
             */
            require "jdate.php";
            $messages = chat_message::where('chat_id' , '=' , $request->get('chat_id'))->orderBy('created_at' , 'ASC')->get();
            foreach ($messages as $ind => $val){
                chat_message::find($val->id)->seen = 1;
                $messages[$ind]->time = jdate('d F Y H:i:s' , strtotime($val->created_at));
            }
            return response()->json([
                'message' => 'chat messages received successfully.',
                'success' => $messages
            ]);
        } else {
            return response()->json([
                'message' => 'access denied',
                'errors' => [
                    'you aren\'t student of this'
                ]
            ], 403);
        }
    }

}

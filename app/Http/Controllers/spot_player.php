<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonPurchased;
use App\Models\purchases_courses;
use App\Models\school_courses;
use App\Models\User;

class spot_player extends Controller
{
        public static function licence_generator ($user_id , $video_code , $coursePrice){
        $licence = null;
        $errors = [];
        $user = User::find($user_id);
        $course = school_courses::find($video_code);
        if($course->price_irr != 0) {
            $getLicence = curl_init();
            curl_setopt($getLicence, CURLOPT_URL, 'https://panel.spotplayer.ir/license/edit/');
            curl_setopt($getLicence, CURLOPT_HTTPHEADER, [
                '$API: YFc3jCuvd4SmO1qy4IionF7I9l5f9iSMGRr0',
                'Content-Type: application/json'
            ]);
            curl_setopt($getLicence, CURLOPT_POST, true);
            curl_setopt($getLicence, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($getLicence, CURLOPT_POSTFIELDS, json_encode([
                "course" => [$course->spotplayer_code],
                'name' => $user->getFullname(),
                'watermark' => [
                    'texts' => [
                        ['text' => $course->course_name . ' | ' . $user->phone_number]
                    ]
                ]
            ]));
            $response = curl_exec($getLicence);
            curl_close($getLicence);
            $getLicence = json_decode($response);
            if (isset($getLicence->key)){
                $licence = $getLicence->key;
            }
        }
        $newPurchasedCourse = new purchases_courses;
        $newPurchasedCourse->course_id = $video_code;
        $newPurchasedCourse->user_id = $user_id;
        $newPurchasedCourse->spot_code = isset($licence) ? $licence : "این دوره لایسنس ندارد";
        $newPurchasedCourse->price = $coursePrice;
        $newPurchasedCourse->save();
        return true;
    }

    public static function lessonLicenceGenerator ($user_id , $video_code , $lessonPrice){
        $licence = null;
        $errors = [];
        $user = User::findOrFail($user_id);
        $lesson = Lesson::findOrFail($video_code);
        if($lesson->price != 0) {
            $getLicence = curl_init();
            curl_setopt($getLicence, CURLOPT_URL, 'https://panel.spotplayer.ir/license/edit/');
            curl_setopt($getLicence, CURLOPT_HTTPHEADER, [
                '$API: YFc3jCuvd4SmO1qy4IionF7I9l5f9iSMGRr0',
                'Content-Type: application/json'
            ]);
            curl_setopt($getLicence, CURLOPT_POST, true);
            curl_setopt($getLicence, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($getLicence, CURLOPT_POSTFIELDS, json_encode([
                "course" => [$lesson->spotplayer],
                'name' => $user->getFullname(),
                'watermark' => [
                    'texts' => [
                        ['text' => $lesson->title . ' - ' . $user->phone_number]
                    ]
                ]
            ]));
            $response = curl_exec($getLicence);
            curl_close($getLicence);
            $getLicence = json_decode($response);
            if (isset($getLicence->key)){
                $licence = $getLicence->key;
            }
        }
        $newPurchasedCourse = new LessonPurchased();
        $newPurchasedCourse->lesson_id  = $video_code;
        $newPurchasedCourse->user_id = $user_id;
        $newPurchasedCourse->spot_code = isset($licence) ? $licence : "این دوره لایسنس ندارد";
        $newPurchasedCourse->price = $lessonPrice;
        $newPurchasedCourse->save();
        return true;
    }
}

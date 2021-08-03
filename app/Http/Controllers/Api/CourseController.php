<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\school_courses;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function show($course, Request $request)
    {
        $course = school_courses::actives()->whereId($course)->firstOrFail();
        $user = $request->get('user');

        $purches = $user->coursePurched()->where('course_id', $course->id)->count();

        if (!$purches)
            return response([
                'message' => 'User dont have this course',
                'status' => 401
            ], 401);

        return response()->json([
            'status' => 200,
            'data' => $course->load('archives')
        ], 200);
    }
}

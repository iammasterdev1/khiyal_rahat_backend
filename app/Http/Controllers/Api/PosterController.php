<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Lesson\AddToCartRequest;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use App\Models\Poster;
use App\Http\Resources\AllLessonResource;
use App\Models\school_basket;
use App\Models\User;
use Illuminate\Http\Request;

class PosterController extends Controller
{
   

    public function index(Request $request)
    {
        $lessons = Lesson::getAll();

        if ($request->get('order_by')){

            if ($request->order_by == 1)
                $lessons->orderBy('created_at', 'desc');

            if ($request->order_by == 2)
                $lessons->orderBy('created_at', 'asc');

            if ($request->order_by == 3)
                $lessons->orderBy('price', 'asc');

            if ($request->order_by == 4)
                $lessons->orderBy('price', 'desc');
        }

        if ($request->get('price_from'))
            $lessons->where('price', '>=', $request->price_from);

        if ($request->get('price_to'))
            $lessons->where('price', '<=', $request->price_to);

          return $lessons->paginate(32);
    }

    public function show(Request $request)
    {
        $lesson = Lesson::getAll()->findOrFail($request->lesson);
        return new LessonResource($lesson->load('posters', 'images', 'topics', 'comments'));
    }

    public function download(Poster $poster)
    {
        return
            ($poster->free == Poster::FREE && $poster->status == Poster::ACTIVE)
            ?
            \Response::redirectTo($poster->path) : abort(404);
    }

    public function addToCart(AddToCartRequest $request)
    {
        $this->middleware('student');

        $user = User::where('token' , $request->get('token'))->first();
        if (!$user)
            return response([
                'message'   =>  'User not found.',
                'status'    =>  404
            ], 404);

        $purches = $user->lessonPurched()->where('lesson_id', $request->lesson)->count();

        if ($purches)
            return response([
                'message'   =>  'User already bought lesson',
                'status'    =>  401
            ], 401);

        $bascket = school_basket::where('user_id', $user->id)->where('productable_type' , Lesson::class)->where('productable_id', $request->lesson)->count();
        if (!$bascket){

            $bascket = $user->basckets()->create([
                'productable_type'  =>  Lesson::class,
                'productable_id'    =>  (int)htmlspecialchars($request->lesson)
            ]);

            return response()->json([
                'message' => 'Lesson added to cart successfully.' ,
                'success' => $bascket
            ]);
        }

        return response()->json([
            'message' => 'entered data is invalid.' ,
            'errors' => (object)[
                'lesson' => [
                    'entered lesson is in cart'
                ]
            ]
        ] , 401);
    }
}

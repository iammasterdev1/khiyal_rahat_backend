<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class student
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $errors = [];
        if(
            !is_null(User::where('token' , '=' , $request->get('token'))->first())
        &&
            (int)User::where('token' , '=' , $request->get('token'))->first()->account_type == 1
        ){
            return $next($request);
        }
        elseif(
            is_null(User::where('token' , '=' , $request->get('token'))->first())
        ){
            $errors['token'][] = 'Entered token is invalid.';
            return response()->json([
                'message' => 'entered data is invalid.',
                'errors' => $errors
            ])->status('401');
        }elseif(
            (int)User::where('token' , '=' , $request->get('token'))->first()->account_type !== 1
        ){
            $errors['token'][] = 'access denied. user isn\'t admin';
            return response()->json([
                'message' => 'entered data is invalid.',
                'errors' => $errors
            ] , '403');
        }

    }
}

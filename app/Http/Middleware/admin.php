<?php

namespace App\Http\Middleware;

use App\Http\Requests\AdminmiddleWare;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class admin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $errors = [];

        if(
            !$request->has('token')
        ){
            /**
             *
             * TOKEN HAVEN'T BEEN SENT
             *
             */
            $errors['token'][] = 'The token field is required.';
            return response()->json([
                "message" => "The given data was invalid.",
                'errors' => (object)$errors
            ])->setStatusCode('422');
        }
        elseif (
            is_null(
                User::where("token", "=", $request->get("token"))->first()
            )
        ) {
            /**
             *
             * IF TOKEN WAS INVALID
             *
             */
            $errors["token"][] = "entered token is invalid.";

            return response()->json([
                "message" => "access denied",
                'errors' => (object)$errors
            ])->setStatusCode('403');
        }
        elseif (
            (int)User::where("token", "=", $request->get("token"))->first()->account_type !== 0
        ) {
            /**
             *
             * ACCESS DENIED - USER ISN'T ADMIN
             *
             */
            $errors['token'][] = "access denied, just admins can add add new pdf book.";

            return response()->json([
                "message" => "access denied",
                'errors' => (object)$errors
            ])->setStatusCode('403');

        } else {

            return $next($request);
        }
    }
}

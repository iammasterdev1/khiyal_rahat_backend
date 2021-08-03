<?php

namespace App\Http\Middleware\Api;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckUser
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
        //find user
        $user = User::where('token', $request->get('token'))->first();
        if (!$user)
            return response()->json([
                'status' => 404,
                'message' => 'User not found.'
            ], 404);
        //send user into controller
        $request->attributes->add(['user' => $user]);
        return $next($request);
    }
}

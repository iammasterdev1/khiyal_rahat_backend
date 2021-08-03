<?php

namespace App\Http\Controllers;

use App\Http\Requests\blog_add_new_article;
use App\Models\articles;


class blog_controller extends Controller {

    public function addNewArticle(blog_add_new_article $request){
        $errors = [];

        /**
         *
         * add article if there aren't any errors
         *
         */
        if(
            count($errors) === 0
        ){
            /**
             *
             * no error
             *
             */

            $addBlog            = new articles();
            $addBlog->user_id   = User::where('token' , '=' , $request->get('token'))->first()->id;
            $addBlog->title     = htmlspecialchars($request->get('title'));
            $addBlog->content   = htmlspecialchars($request->get('content'));
            $addBlog->save();
            return response()->json([
                'message' => 'article added successfully.' ,
                'success' => $addBlog
            ]);
        }
        return response()->json([
            'message' => 'entered data is invalid.',
            'errors'  => $errors
        ], 401);
    }

}

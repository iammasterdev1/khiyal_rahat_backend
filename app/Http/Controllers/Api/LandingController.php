<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LandingPageResource;
use App\Http\Resources\LandingResource;
use App\Models\Landing;
use App\Models\LandingPage;

class LandingController extends Controller
{
    public function show(Landing $landing)
    {
        if ($landing->status != Landing::ACTIVE)
            return response()->json([
                'status'  => 404,
                'message' => 'No landing found.'
            ], 404);

        return response()->json([
            'status' => 200,
            'data'   => new LandingResource($landing)
        ], 200);
    }

    public function landingPage(LandingPage $landingPage)
    {
        if ($landingPage->status != Landing::ACTIVE)
            return response()->json([
                'status'  => 404,
                'message' => 'No landing page found.'
            ], 404);

        return response()->json([
            'status' => 200,
            'data'   => new LandingPageResource($landingPage)
        ], 200);
    }
}

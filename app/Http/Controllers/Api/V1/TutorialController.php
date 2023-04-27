<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList(Request $request)
    {
        $data = Tutorial::query()->where('status', 1)->orderByDesc('sort')->get();

        return response()->json(['code' => 1, 'data' => $data]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetSecret(Request $request)
    {
        $user = $request->user();
        $user->app_secret = sha1(time());
        $user->save();

        return response()->json(['code' => 1, 'data' => $user->app_secret]);
    }
}

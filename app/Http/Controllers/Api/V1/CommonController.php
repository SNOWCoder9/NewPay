<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\VerifyCode;
use App\Notifications\RegisterEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CommonController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMail(Request $request)
    {
        $email = (string)$request->post('email');
        $ip = $request->ip();
        // 防刷机制
        if (Cache::has('send_verify_code_'.md5($ip))) {
            return response()->json(['code' => 0, 'message' => '请勿频繁发送验证码']);
        }
        // 发送邮件
        $code = Str::random(6);
        if (VerifyCode::create(['address' => $email, 'code' => $code])) { // 生成注册验证码
            Notification::route('mail', $email)->notifyNow(new RegisterEmail($code));
        }
        Cache::put('send_verify_code_'.md5($ip), $ip, 50);

        return response()->json(['code' => 1, 'message' => '发送成功']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfig(Request $request)
    {
        $key = $request->get('key', '');
        if (!$key){
            return response()->json(['code' => 0, 'message' => 'Failed']);
        }

        return response()->json(['code' => 1, 'data' => getConfig($key)]);
    }
}

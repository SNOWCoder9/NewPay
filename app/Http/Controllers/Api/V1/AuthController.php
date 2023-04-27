<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\VerifyCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::query()->where('email', $credentials['email'])->first();
        if (!$user) {
            return response()->json(['code' => 2, 'message' => __('auth.email_notExist')]);
        }
        // 校验用户密码
        if (!password_verify($credentials['password'], $user->password)) {
            return response()->json(['code' => 2, 'message' => __('auth.error.login_failed')]);
        }
        if ($user->status != 1) {
            return response()->json(['code' => 2, 'message' => __('auth.error.account_baned')]);
        }
        $createToken = $user->createToken($user->app_id);
        $createToken->token->expires_at = Carbon::now()->addDays(30);
        $createToken->token->save();
        $user->last_ip = $request->ip();
        $user->last_login = Carbon::now()->toDateTimeString();
        $user->save();

        return response()->json([
            'code' => 1,
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $createToken->accessToken,
                'expires_in' => Carbon::now()->addDays(30)
            ],
            'message' => '登录成功'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function TelegramLogin(Request $request)
    {
        $auth_data = $request->all();
        if ($this->telegram_oauth_check($auth_data) === true) {
            $telegram_id = $auth_data['id'];
            $user = User::where('telegram_id', $telegram_id)->first();
            if (!$user) {
                return response()->json(['code' => 2, 'message' => __('auth.error.telegram.no_bind')]);
            }
            if ($user->status != 1) {
                return response()->json(['code' => 2, 'message' => __('auth.error.account_baned')]);
            }
            $createToken = $user->createToken($user->app_id);
            $createToken->token->expires_at = Carbon::now()->addDays(30);
            $createToken->token->save();

            return response()->json([
                'code' => 1,
                'data' => [
                    'token_type' => 'Bearer',
                    'access_token' => $createToken->accessToken,
                    'expires_in' => Carbon::now()->addDays(30)
                ],
                'message' => '登录成功'
            ]);
        }
    }

    private function telegram_oauth_check($auth_data)
    {
        $check_hash = $auth_data['hash'];
        $bot_token = getConfig('telegram_bot_token');
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', $bot_token, true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            return false; // Bad Data :(
        }

        if ((time() - $auth_data['auth_date']) > 300) { // Expire @ 5mins
            return false;
        }

        return true; // Good to Go
    }

    /**
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        if (getConfig('open_register') == 'false') {
            return response()->json(['code' => 2, 'message' => '未开放注册~']);
        }
        $data = $request->all();
        if (!$data['code']) {
            return response()->json(['code' => 2, 'message' => '请输入验证码']);
        }
        $verifyCode = VerifyCode::whereAddress($data['email'])->whereCode($data['code'])->whereStatus(0)->first();
        if (!$verifyCode) {
            return response()->json(['code' => 2, 'message' => '验证码不正确']);
        }
        $user = new User();
        $user->email = $data['email'];
        $user->name = trimall($data['email']);
        $user->password = bcrypt($data['password']);
        $user->last_ip = $request->ip();
        $user->app_id = generate_auth_code(12, 'app_id');
        $user->app_secret = sha1(time());
        $user->expired_at = strtotime("+1 year");
        $user->last_login = now()->toDateTimeString();
        $user->register_at = now()->toDateTimeString();
        $user->save();
        // 验证码已使用
        $verifyCode->status = 1;
        $verifyCode->save();
        $createToken = $user->createToken($user->app_id);
        $createToken->token->expires_at = Carbon::now()->addDays(30);
        $createToken->token->save();

        return response()->json([
            'code' => 1,
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $createToken->accessToken,
                'expires_in' => Carbon::now()->addDays(30)
            ],
            'message' => __('auth.register.success')
        ]);
    }

    public function forget(ForgetRequest $request)
    {
        $data = $request->all();
        if (!$data['code']) {
            return response()->json(['code' => 2, 'message' => '请输入验证码']);
        }
        $verifyCode = VerifyCode::whereAddress($data['email'])->whereCode($data['code'])->whereStatus(0)->first();
        if (!$verifyCode) {
            return response()->json(['code' => 2, 'message' => '验证码不正确']);
        }
        $user = User::query()->where('email', $data['email'])->first();
        if (!$user) {
            return response()->json(['code' => 2, 'message' => '用户不存在']);
        }
        $user->password = bcrypt($data['password']);
        $user->save();
        // 验证码已使用
        $verifyCode->status = 1;
        $verifyCode->save();

        return response()->json(['code' => 1, 'message' => '重置成功']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        if (Auth::guard('api')->check()) {
            Auth::guard('api')->user()->tokens()->delete();
        }

        return response()->json(['code' => 1, 'message' => '登出成功~']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLoginConfig(Request $request)
    {
        $config = [
            'login_background' => getConfig('login_background'),
            'telegram_bot_name' => getConfig('telegram_bot_name'),
            'login_logo' => user_admin_config('login-logo'),
            'open_register' => getConfig('open_register'),
        ];

        return response()->json(['code' => 1, 'data' => $config]);
    }
}

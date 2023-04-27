<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordRequest;
use App\Models\Announcement;
use App\Models\Contract;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo(Request $request)
    {
        $user = $request->user();

        return response()->json(['code' => 1, 'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'app_id' => $user->app_id,
            'telegram_id' => $user->telegram_id,
            'expired_at' => $user->expired_at,
        ]]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAnn(Request $request)
    {
        $data = Announcement::query()->where('status', 1)->orderByDesc('id')->get();

        return response()->json(['code' => 1, 'data' => $data]);
    }

    public function getDashboardTable(Request $request)
    {
        $user = $request->user();
        $tokens = Contract::with(['shop:id,token,sort,name'])->where('user_id', $user->id)->get();
        $tokens = $tokens->sortByDesc('shop.sort');
        $data = [];
        foreach ($tokens as $token) {
            $total = Order::query()->selectRaw("sum(final_amount) as price, sum(token_price) as token_price")->where([
                'user_id' => $user->id,
                'token' => $token->token
            ])->where('status', '>', 1)->whereDate('created_at', Carbon::today())->first()->toArray();
            $pay_count = Order::query()->where([
                    'user_id' => $user->id,
                    'token' => $token->token,
                    'status' => 3
                ])->whereDate('created_at', Carbon::today())->count('id') ?? 0;
            $all = $user->order()->byToken($token->token)->today()->count() ?? 0;
            array_push($data, [
                'name' => $token->shop->name,
                'token' => $token->token,
                'type' => $token->type,
                'sort' => $token->shop->sort,
                'today_income' => floatval($total['price']) ?? 0,
                'today_token_income' => floatval($total['token_price']) ?? 0,
                'convert' => $pay_count > 0 ? bcdiv($pay_count, $all, 2) : 0,
            ]);
        }

        $result['table'] = $data;
        $result['statistic']['balance'] = $user->order()->success()->noWithdraw()->sum('final_amount');
        $result['statistic']['order_count'] = $user->order()->today()->count();
        $result['statistic']['order_success_count'] = $user->order()->today()->success()->count();
        $result['statistic']['income'] = $user->order()->today()->success()->sum('final_amount');
        $result['statistic']['withdraw'] = $user->settlement()->deal(0)->sum('money');

        return response()->json(['code' => 1, 'data' => $result]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataChart(Request $request)
    {
        $user = $request->user();

        $tokens = Contract::with(['shop:id,token,sort,name'])->where('user_id', $user->id)->get();
        $tokens = $tokens->sortByDesc('shop.sort');
        $data = [];
        $days = [];
        for ($i = 0; $i < 30; $i++) {
            $days[] = date('Y-m-d', strtotime("-{$i} day"));
        }
        $days = array_reverse($days,false);
        $keys = [];
        foreach ($tokens as $token) {
            $keys[] = $token->shop->name;
            $item['name'] = $token->shop->name;
            $item['type'] = 'line';
            $item['stack'] = 'Total';
            $item['smooth'] = true;
            $item['data'] = [];
            $dd = Order::query()
                ->select([DB::raw('DATE(created_at) as date'), DB::raw('sum(goods_price) as goods_price')])
                ->where('token', $token->token)
                ->where('user_id', $user->id)
                ->where('status', '>', 1)
                ->where('created_at', '<', Carbon::now())
                ->where('created_at', '>=', Carbon::today()->subDays(30))
                ->groupBy('date')
                ->get();
            foreach ($days as $key => $day){
                foreach ($dd as $v){
                    if (isset($item['data'][$key]) && $item['data'][$key] > 0){
                        break;
                    }
                    $item['data'][$key] = $v->date === $day ? $v->goods_price : 0;
                }
            }
            array_push($data, $item);
        }

        return response()->json(['code' => 1, 'data' => $data, 'days' => $days, 'keys' => $keys]);
    }

    public function updatePassword(PasswordRequest $request)
    {
        $old_password = $request->post('old_password');
        $password = $request->post('password');
        if ($password === $old_password) {
            return response()->json(['code' => 0, 'message' => '新旧密码不能相同']);
        }
        $user = $request->user();

        if (!checkPassword($user, $old_password)) {
            return response()->json(['code' => 0, 'message' => '旧密码错误']);
        }
        $user->password = bcrypt($password);
        $user->save();

        return response()->json(['code' => 1, 'message' => '更新成功']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAddress(Request $request)
    {
        $address = $request->post('address');
        $user = $request->user();
        $user->address = $address;
        $user->save();

        return response()->json(['code' => 1, 'message' => '更新成功']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        $user = $request->user();
        $data = [];
        $data['address'] = $user->address;
        $data['telegram_bot'] = getConfig('telegram_bot_name');
        $data['app_id'] = $user->app_id;

        return response()->json(['code' => 1, 'data' => $data]);
    }

    public function delete(Request $request)
    {
        $user = $request->user();
        $password = (string)$request->get('password');
        if (!checkPassword($user, $password)) {
            return response()->json(['code' => 0, 'message' => '密码错误']);
        }
        $user->delete();
        if (Auth::guard('api')->check()) {
            Auth::guard('api')->user()->tokens()->delete();
        }

        return response()->json(['code' => 1, 'message' => '删除成功！']);
    }
}

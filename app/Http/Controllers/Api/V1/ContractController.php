<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Shop;
use App\Services\BuyShop;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ContractController extends Controller
{
    public function getList(Request $request)
    {
        $user = $request->user();
        $shops = Shop::query()->where('status', 1)->orderByDesc('sort')->get();
        $shops->each(function ($item) use ($user) {
            $item->buy = $user->hasContractToken($item->token);
            if ($item->buy) {
                $item->buy->expired_at = date('Y-m-d', strtotime($item->buy->expired_at));
            }
        });

        return response()->json(['code' => 1, 'data' => $shops]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BobException
     */
    public function submit(Request $request)
    {
        $user = $request->user();
        $payment = (string)$request->post('payment', 'alipay');
        $cycle = (string)$request->post('cycle', 'year_price');
        $shop_id = (int)$request->post('shop_id');
        $address = (string)$request->post('address', '');
        // $address = getConfig('settlement_usdt_address');
        if (!$shop_id) {
            return response()->json(['code' => 0, 'message' => '请选择需要签约的支付']);
        }
        $shop_key = 'userid_' . $user->id . '_shopid_' . $shop_id . '_' . $cycle;
        $shop = Shop::query()->find($shop_id);
        if (Cache::tags('buy_shop')->has($shop_key)) {
            $order_sn = Cache::tags('buy_shop')->get($shop_key);
            $order = Cache::tags('buy_shop')->get($order_sn);
        } else {
            $order = new Collection();
            $order->goods_price = $shop->$cycle;
            $order->order_sn = createOrderNo('pay');
            $order->payment = $payment;
            $order->user_id = $user->id;
            $order->cycle = $cycle;
            $order->token = $shop->token;
            $order->type = $shop->type;
            $order->address = $address;
            $order->rate = $shop->rate;    // 支付费率
            $order->return_url = url("/#/contract");
            $order->notify_method = 'buy_shop';
            $order->token_price = number_format($order->goods_price / Cache::get('USDT_CNY'), 3);
            Cache::tags('buy_shop')->put($shop_key, $order->order_sn, now()->addMinutes(20));
            Cache::tags('buy_shop')->put($order->order_sn, $order, now()->addMinutes(20));
        }
        if ($shop->$cycle == 0) {
            BuyShop::postPayment($order->order_sn);

            return response()->json(['code' => 2, 'message' => '签约成功']);
        }
        if ($payment === 'alipay') {
            $order->platform = 'web';
            $shop = Shop::query()->where('token', 'alipay')->first();
            $payment_id = PaymentService::getPayment($shop->payment_ids, $order->goods_price);
            $order->payment_id = $payment_id;
            $paymentService = new PaymentService($payment_id);
            $result = $paymentService->purchase($order);

            return response()->json(['code' => 1, 'data' => [
                'type' => $payment, 'url' => $result, 'trade_no' => $order->order_sn]
            ]);
        }

        return response()->json(['code' => 1, 'data' => [
            'type' => $payment,
            'token_price' => $order->token_price,
            'address' => getConfig('settlement_usdt_address'),
        ]]);
    }

    public function addressUpdate(Request $request)
    {
        $address = (string)$request->post('address');
        $token = (string)$request->post('token');
        $user = $request->user();
        if (!$address) {
            return response()->json(['code' => 0, 'message' => '代币地址不能为空']);
        }
        Contract::query()->where('user_id', $user->id)->where('token', $token)->update(['address' => $address]);

        return response()->json(['code' => 1, 'message' => '修改成功']);
    }
}

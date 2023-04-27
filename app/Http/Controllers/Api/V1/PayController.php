<?php

namespace App\Http\Controllers\Api\V1;

use App\Enum\OrderEnum;
use App\Enum\TypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Services\PaymentService;
use App\Util\Tool;
use Illuminate\Http\Request;

class PayController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentList(Request $request)
    {
        $order_sn = $request->get('order_sn');
        $order = Order::query()->where('order_sn', $order_sn)->first();
        if (!$order) {
            return response()->json(['code' => 2, 'message' => '订单不存在！']);
        }
        $user = User::query()->find($order->user_id);
        $contract = Contract::query()->where('user_id', $user->id)->where('status', 1)->get();
        $payment = [];
        $contract->each(function ($item) use (&$payment) {
            $pay = Shop::query()->where('token', $item->token)->where('status', 1)->first();
            if ($pay) {
                $payment[] = [
                    'name' => $pay->name,
                    'token' => $pay->token,
                    'type' => $pay->type,
                    'desc' => $pay->desc,
                    'image' => $pay->image,
                    'sort' => $pay->sort,
                    'address' => $item->address,
                ];
            }
        });
        $sort = array_column($payment, 'sort');
        array_multisort($payment, SORT_ASC, $sort);
        $order->expired_at = (strtotime($order->created_at) + 1200) * 1000;
        $order->token_price = floatval($order->token_price);

        return response()->json(['code' => 1, 'data' => compact('order', 'payment')]);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BobException
     */
    public function getPaymentUrl(Request $request)
    {
        $payment = $request->get('payment');
        $order_sn = $request->get('order_sn');
        $platform = $request->get('platform');
        $order = Order::query()->where('order_sn', $order_sn)->first();
        if (!$order) {
            return response()->json(['code' => 0, 'message' => '订单不存在！']);
        }
        if ($order->status > OrderEnum::UNPAID) {
            return response()->json(['code' => 0, 'message' => '订单错误！']);
        }
        $user = User::query()->find($order->user_id);
        if (!$pay = $user->hasContractToken($payment)) {
            return response()->json(['code' => 0, 'message' => '支付方式不存在！']);
        }
        // 展示支付链接方式
        // $showPaymentType = $order->type == 3 ? 'url' : 'qrcode';
        $showPaymentType = 'qrcode';
        if ($order->address && TypeEnum::toType($payment) === $order->type) {
            $order->token_price = floatval($order->token_price);
            return response()->json(['code' => 1, 'data' => $order, 'type' => $showPaymentType]);
        }
        $order->token = $payment;
        $order->platform = $platform;   // 支付环境
        // 支付类型
        $type = TypeEnum::toType($payment);
        $order->type = $type;
        if ($type === 1) {// 虚拟货币
            $order->token_price = Tool::calcTokenPrice($payment, $user, $order);
            $order->address = $pay->address;
        } else {
            $shop = Shop::query()->where('token', $payment)->first();
            $payment_id = PaymentService::getPayment($shop->payment_ids, $order->goods_price);
            $order->payment_id = $payment_id;
            $order->save();
            $paymentService = new PaymentService($payment_id);
            $result = $paymentService->purchase($order);
            unset($order->notify_url);
            $order->address = $result;
            $order->token_price = $order->goods_price;
        }
        $order->save();
        $order->token_price = floatval($order->token_price);

        return response()->json(['code' => 1, 'data' => $order, 'type' => $showPaymentType]);
    }
}

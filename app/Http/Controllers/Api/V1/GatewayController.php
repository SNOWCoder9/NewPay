<?php


namespace App\Http\Controllers\Api\V1;


use App\Enum\OrderEnum;
use App\Enum\TypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GatewayController extends Controller
{
    /**
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tron(Request $request)
    {
        $data = $request->all();
        $user = User::query()->where('app_id', $data['app_id'])->first();
        if (!$user) {
            return response()->json(['code' => 0, 'msg' => '商户信息不存在']);
        }
        // 校验签名
        $verify = (new OrderService())->verifySign($data, $data['sign'], $user->app_secret);
        if (!$verify) {
            return response()->json(['code' => 0, 'msg' => '签名校验失败！']);
        }
        if ($user->status === 0) {
            return response()->json(['code' => 0, 'msg' => '账号已被禁用']);
        }
        if ($user->expired_at < time()) {
            return response()->json(['code' => 0, 'msg' => '授权已过期']);
        }
        $domain = getDomain($data['notify_url']);
        $domainList = Cache::get('domain_white_list', []);
        if (!in_array($domain, $domainList) && getConfig('pay_domain_forbid', 'true') == 'true') {
            return response()->json(['code' => 0, 'msg' => '该域名不可发起支付，原因：域名没过白，请前往支付平台授权支付域名（' . $domain . '）']);
        }
        if ($user->hasContract()->count() === 0) {
            return response()->json(['code' => 0, 'msg' => '暂未签约任何支付方式']);
        }
        $goodsPrice = bcdiv($data['total_amount'], 100, 2);
        if ($goodsPrice < getConfig('pay_min_amount')) {
            return response()->json(['code' => 0, 'msg' => '低于最小支付金额限制']);
        }
        if ($goodsPrice > getConfig('pay_max_amount')) {
            return response()->json(['code' => 0, 'msg' => '超出最大支付金额限制']);
        }
        $order = Order::query()->where('out_trade_no', $data['out_trade_no'])->first();
        if ($order) {
            return response()->json(['code' => 1, 'msg' => '成功', 'url' => getConfig('pay_host') . "/#/charges/{$order->order_sn}"]);
        }
        $pid = Str::random(16);
        $order = new Order();
        $order->order_sn = $pid;
        $order->user_id = $user->id;
        $order->goods_price = $goodsPrice;
        $order->notify_url = $data['notify_url'];
        $order->return_url = $data['return_url'];
        $order->out_trade_no = $data['out_trade_no'];
        $order->token = isset($data['type']) ? $data['type'] : 'alipay';
        $order->status = OrderEnum::UNPAID;
        if (!$order->save()) {
            return response()->json(['code' => 0, 'msg' => '生成订单失败']);
        }

        return response()->json(['code' => 1, 'msg' => '成功', 'url' => getConfig('pay_host') . "/#/charges/{$pid}"]);
    }

    /**
     * 直连下单
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tronDirect(Request $request)
    {
        $data = $request->all();
        $user = User::query()->where('app_id', $data['app_id'])->first();
        if (!$user) {
            return response()->json(['code' => 0, 'msg' => '商户信息不存在']);
        }
        // 校验签名
        $verify = (new OrderService())->verifySign($data, $data['sign'], $user->app_secret);
        if (!$verify) {
            return response()->json(['code' => 0, 'msg' => '签名校验失败！']);
        }
        if ($user->status === 0) {
            return response()->json(['code' => 0, 'msg' => '账号已被禁用']);
        }
        if ($user->expired_at < time()) {
            return response()->json(['code' => 0, 'msg' => '授权已过期']);
        }
        $domain = getDomain($data['notify_url']);
        $domainList = Cache::get('domain_white_list', []);
        if (!in_array($domain, $domainList) && getConfig('pay_domain_forbid', 'true') == 'true') {
            return response()->json(['code' => 0, 'msg' => '该域名不可发起支付，原因：域名没过白，请前往支付平台授权支付域名（' . $domain . '）']);
        }
        if ($user->hasContract()->count() === 0) {
            return response()->json(['code' => 0, 'msg' => '暂未签约任何支付方式']);
        }
        $goodsPrice = bcdiv($data['total_amount'], 100, 2);
        if ($goodsPrice < getConfig('pay_min_amount')) {
            return response()->json(['code' => 0, 'msg' => '低于最小支付金额限制']);
        }
        if ($goodsPrice > getConfig('pay_max_amount')) {
            return response()->json(['code' => 0, 'msg' => '超出最大支付金额限制']);
        }
        $order = Order::query()->where('out_trade_no', $data['out_trade_no'])->first();
        if ($order) {
            return response()->json(['code' => 1, 'msg' => '成功', 'url' => getConfig('pay_host') . "/#/charges/{$order->order_sn}"]);
        }
        $pid = Str::random(16);
        $order = new Order();
        $order->order_sn = $pid;
        $order->user_id = $user->id;
        $order->goods_price = $goodsPrice;
        $order->notify_url = $data['notify_url'];
        $order->return_url = $data['return_url'];
        $order->out_trade_no = $data['out_trade_no'];
        $order->token = isset($data['type']) ? $data['type'] : 'alipay';
        $order->status = OrderEnum::UNPAID;
        $order->save();
        if (!$order->save()) {
            return response()->json(['code' => 0, 'msg' => '生成订单失败']);
        }
        // 支付类型
        $type = TypeEnum::toType($order->token);
        $order->type = $type;
        switch ($order->token) {
            case 'alipay':
            case 'alipay_hk':
            case 'wechat':
                $shop = Shop::query()->where('token', $order->token)->first();
                $payment_id = PaymentService::getPayment($shop->payment_ids, $order->goods_price);
                $order->payment_id = $payment_id;
                $order->save();
                $paymentService = new PaymentService($payment_id);
                $result = $paymentService->purchase($order);
                unset($order->notify_url);
                $order->address = $result;
                $order->token_price = $order->goods_price;
                $order->save();

                return response()->json(['code' => 1, 'url' => $result]);
            default:
                return response()->json(['code' => 0, 'msg' => '支付类型错误']);
        }
    }
}

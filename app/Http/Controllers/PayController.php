<?php


namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use App\Services\PaymentService;
use App\Util\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PayController extends Controller
{
    /**
     * @throws \App\Exceptions\BobException
     */
    public function pay(Request $request, $pid)
    {
        if (!$pid) {
            return response()->redirectTo("https://www.baidu.com");
        }
        $order = Order::query()->where(['order_sn' => $pid, 'status' => 0])->first();
        if (!$order) {
            return response()->redirectTo("https://www.baidu.com");
        }
        $user = User::query()->find($order->user_id);
        if ($user->expired_at < time()){
            return response()->json(['code' => 0, 'msg' => '授权已过期~']);
        }
        if (!$order->address) {
            return response()->json(['code' => 0, 'msg' => '链接不存']);
        }
        $order->platform = Tool::isMobile() ? 'h5' : 'web';
        $shop = Shop::query()->where('token', 'alipay')->first();
        $payment_id = PaymentService::getPayment($shop->payment_ids, $order->goods_price);
        $order->payment_id = $payment_id;
        $order->save();
        $paymentService = new PaymentService($payment_id);
        $result = $paymentService->purchase($order);

        return redirect($result);
    }

}

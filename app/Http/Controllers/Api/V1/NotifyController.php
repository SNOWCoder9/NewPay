<?php


namespace App\Http\Controllers\Api\V1;

use App\Enum\OrderEnum;
use App\Http\Controllers\Controller;
use App\Jobs\OrderNotify;
use App\Models\Contract;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\BobPay;
use App\Services\BuyShop;
use App\Services\EPay;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotifyController extends Controller
{
    public function PayNotify(Request $request, $type, $payment = null)
    {
        switch ($type) {
            case 'wechat': // 微信回调
            case 'alipay':  // 支付宝回调
            case 'alipay_hk':  // 支付宝HK回调
                $this->orderNotify($request, $payment);
                break;
        }
    }

    private function orderNotify(Request $request, $paymentId)
    {
        $paymentService = new PaymentService($paymentId);
        $verify = $paymentService->notify($request);
        if ($verify){
            $this->postPayment($verify);
            return $verify['response_text'] ?? "SUCCESS";
        } else {
            return "Fail";
        }
    }

    public function postPayment($res)
    {
        $order = Order::query()->where('order_sn', $res['trade_no'])->first();
        if ($order && $order->status === OrderEnum::UNPAID) {
            $contract = Contract::query()->where(['user_id' => $order->user_id, 'token' => $order->token])->first();
            $order->final_amount = (float)sprintf('%.2f', $order->goods_price * ((100 - $contract->rate) / 100));
            $order->transaction_id = $res['callback_no'];
            $order->notified_at = now()->toDateTimeString();
            $order->status = OrderEnum::SUCCESS; // 支付成功
            $order->save();

            OrderNotify::dispatch($order);
        }
    }

    /**
     * @param Request $request
     * @throws \App\Exceptions\BobException
     */
    public function contractPayment(Request $request, $payment)
    {
        $paymentService = new PaymentService($payment);
        $verify = $paymentService->notify($request);
        if (!$verify){
            return "Fail";
        }
        BuyShop::postPayment($verify['trade_no'], $verify['callback_no']);

        return "SUCCESS";
    }
}

<?php


namespace App\Http\Controllers\Api\V1;

use App\Enum\OrderEnum;
use App\Enum\TypeEnum;
use App\Http\Controllers\Controller;
use App\Jobs\OrderNotify;
use App\Models\Order;
use App\Models\Settlement;
use App\Models\Shop;
use App\Payments\Alipay;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkOrderStatus(Request $request)
    {
        $order_sn = $request->get('order_sn');
        $order = Order::query()->where('order_sn', $order_sn)->first();
        if (!$order) {
            return response()->redirectTo("https://www.baidu.com");
        }
        // 订单待支付
        if ($order->status == OrderEnum::UNPAID) {
            return response()->json(['message' => 'wait....', 'code' => 3]);
        }
        // 订单不存在或者已经过期
        if ($order->status == OrderEnum::EXPIRED) {
            return response()->json(['message' => 'expired', 'code' => 2]);
        }

        return response()->json(['message' => 'success', 'code' => 1]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList(Request $request)
    {
        $order_sn = (string)$request->get('order_sn', '');
        $status = (array)$request->get('status', []);
        $token = (string)$request->get('token', '');
        $created_at = (array)$request->get('created_at', []);
        $pageSize = (int)$request->get('pageSize', 10);
        $settle_no = (string)$request->get('settle_no', '');
        $user = $request->user();
        $data = Order::query()->where('user_id', $user->id)
            ->when($order_sn, function ($query) use ($order_sn) {
                return $query->where(function ($query) use ($order_sn) {
                    $query->where('order_sn', $order_sn)
                        ->orWhere('out_trade_no', $order_sn)
                        ->orWhere('transaction_id', $order_sn);
                });
            })
            ->when($status, function ($query) use ($status) {
                return $query->whereIn('status', $status);
            })
            ->when($token, function ($query) use ($token) {
                return $query->where('token', strtoupper($token));
            })
            ->when($settle_no, function ($query) use ($settle_no) {
                return $query->where('settle_no', $settle_no);
            })
            ->when($created_at, function ($query) use ($created_at) {
                return $query->where('created_at', '>=', $created_at[0])
                    ->where('created_at', '<=', $created_at[1]);
            })
            ->orderByDesc('id')
            ->paginate($pageSize);

        return response()->json(['code' => 1, 'data' => $data]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function notify(Request $request)
    {
        $trade_no = $request->post('trade_no');
        $order = Order::query()->where('order_sn', $trade_no)->first();
        if ($order->status === 2 || $order->status === 4) {
            OrderNotify::dispatch($order);
        }
        return response()->json(['code' => 1, 'message' => '补单为异步操作，请等待...']);
    }

    public function refund(Request $request)
    {
        return response()->json(['code' => 2, 'data' => '该功能未启用！']);

        $trade_no = $request->post('trade_no');
        $order = Order::query()->where('order_sn', $trade_no)->first();
        // 是否退款
        if ($order->status === OrderEnum::REFUND) {
            return response()->json(['code' => 2, 'data' => "请不要重复申请退款！"]);
        }
        // 是否结账
        if ($order->withdraw === 1) {
            return response()->json(['code' => 2, 'data' => "当前订单无法退款"]);
        }
        // 是否虚拟币
        if ($order->type === TypeEnum::VIRTUAL) {
            return response()->json(['code' => 2, 'data' => "虚拟币无法申请退款！"]);
        }
        try {
            $paymentService = new PaymentService($order->payment_id);
            $result = $paymentService->refund($order);
            $order->status = OrderEnum::REFUND; // 已退款
            $order->save();
        } catch (\Exception $e) {
            return response()->json(['code' => 2, 'message' => $e->getMessage()]);
        }

        return response()->json(['code' => 1, 'data' => $result]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function settlement(Request $request)
    {
        $pageSize = (int)$request->get('pageSize', 10);
        $user = $request->user();
        $list = Settlement::query()->where('user_id', $user->id)->orderByDesc('id')->paginate($pageSize);

        return response()->json(['code' => 1, 'data' => $list]);
    }
}

<?php

namespace App\Admin\Grid\Tools;

use App\Models\Order;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Illuminate\Http\Request;

class TestPay extends AbstractTool
{
    /**
     * 按钮样式定义，默认 btn btn-white waves-effect
     *
     * @var string
     */
    protected $style = 'btn btn-primary btn-outline';


    /**
     * 按钮文本
     *
     * @return string|void
     */
    public function title()
    {
        return '测试支付';
    }

    /**
     *  确认弹窗，如果不需要则返回空即可
     *
     * @return array|string|void
     */
    public function confirm()
    {
        // 只显示标题
//        return '您确定要发送新的提醒消息吗？';

        // 显示标题和内容
        return [];
    }

    /**
     * 处理请求
     * 如果你的类中包含了此方法，则点击按钮后会自动向后端发起ajax请求，并且会通过此方法处理请求逻辑
     *
     * @param Request $request
     */
    public function handle(Request $request)
    {
        $order = Order::query()->create([
            'order_sn' => createOrderNo('pay'),
            'user_id' => 1,
            'goods_price' => 1,
            'token_price' => 1,
            'final_amount' => 1,
            'notify_url' => getConfig('api_host').'notify/alipay/test',
            'return_url' => route('index'),
            'status' => 0,
            'withdraw' => 0,
            'type' => 1,
            'token' => 'alipay',
            'platform' => 'web'
        ]);

        return $this->response()->locationToIntended(url("/#/charges/{$order->order_sn}"));
    }

    /**
     * 设置请求参数
     *
     * @return array|void
     */
    public function parameters()
    {
        return [

        ];
    }
}

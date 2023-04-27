<?php

namespace App\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yansongda\Pay\Pay;

class AlipayF2F
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function config()
    {
        return [
            'name'        => 'AlipayF2F',
            'show_name'   => '支付宝当面付',
            'config' => [
                'alipay_app_id' => [
                    'name' => 'APPID',
                    'type' => 'text',
                    'note' => '支付宝APPID',
                ],
                'alipay_private_key' => [
                    'name' => '商家私钥',
                    'type' => 'textarea',
                    'note' => '商家私钥',
                ],
                'alipay_public_key' => [
                    'name' => '支付宝公钥',
                    'type' => 'textarea',
                    'note' => '支付宝公钥，非商家公钥',
                ]
            ]
        ];
    }

    /**
     * @param $order
     * @return array|mixed|null
     * @throws \App\Exceptions\BobException
     */
    public function purchase($order)
    {
        if ($url = Cache::tags('alipayf2f')->get($order->order_sn, '')) {
            echo $url;die;
        }
        $titles = explode(PHP_EOL, getConfig('order_goods_name'));
        $config = [
            'app_id' => $this->config['alipay_app_id'],
            'ali_public_key' => $this->config['alipay_public_key'],
            'private_key' => $this->config['alipay_private_key'],
            'notify_url' => $order->notify_url,
            'return_url' => $order->return_url,
            'http' => [
                'timeout' => 10.0,
                'connect_timeout' => 10.0,
            ],
        ];
        $pay = Pay::alipay($config);
        $params = [
            'out_trade_no' => $order->order_sn,
            'total_amount' => (float)$order->goods_price,
            'subject' => $titles[rand(0, count($titles) - 1)]
        ];
        $result = $pay->scan($params);
        Cache::tags('alipayf2f')->put($order->order_sn, $result['qr_code'], now()->addMinutes(20));

        return $result['qr_code'];
    }

    /**
     * @param $params
     * @return array|false
     */
    public function notify(Request $request)
    {
        $params = $request->all();
        $config = [
            'app_id' => $this->config['alipay_app_id'],
            'ali_public_key' => $this->config['alipay_public_key'],
            'private_key' => $this->config['alipay_private_key'],
        ];
        $pay = Pay::alipay($config);
        try{
            // 验证签名
            $result = $pay->verify();
            if ($result->trade_status == 'TRADE_SUCCESS' || $result->trade_status == 'TRADE_FINISHED') {
                return [
                    'trade_no' => $result->out_trade_no,
                    'callback_no' => $result->trade_no
                ];
            }
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param $order
     * @return \Yansongda\Supports\Collection
     * @throws \Yansongda\Pay\Exceptions\GatewayException
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function refund($order)
    {
        $config = [
            'app_id' => $this->config['alipay_app_id'],
            'ali_public_key' => $this->config['alipay_public_key'],
            'private_key' => $this->config['alipay_private_key'],
        ];
        $pay = Pay::alipay($config);
        $response = $pay->refund([
            'out_trade_no' => $order->order_sn,
            'refund_amount' => $order->goods_price * 100,
            'reason' => "正常退款，感谢使用",
        ]);

        return $response;
    }
}

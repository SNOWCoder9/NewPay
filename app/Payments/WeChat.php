<?php

namespace App\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class WeChat
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function config()
    {
        return [
            'name'        => 'WeChat',
            'show_name'   => '微信',
            'config' => [
                'wechat_app_id' => [
                    'name' => 'APPID',
                    'type' => 'text',
                    'note' => '绑定支付的APPID（必须配置，开户邮件中可查看）',
                ],
                'wechat_mch_id' => [
                    'name' => 'MCHID',
                    'type' => 'textarea',
                    'note' => '商户号（必须配置，开户邮件中可查看）',
                ],
                'wechat_key' => [
                    'name' => 'KEY',
                    'type' => 'textarea',
                    'note' => '商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）',
                ]
            ]
        ];
    }

    /**
     * @throws \App\Exceptions\BobException
     */
    public function purchase($order)
    {
        if ($url = Cache::tags('wechat')->get($order->order_sn, '')){
            return $url;
        }
        $titles = explode(PHP_EOL, getConfig('order_goods_name'));
        $config = [
            'app_id' => $this->config['wechat_app_id'],
            'mch_id' => $this->config['wechat_mch_id'],
            'key' => $this->config['wechat_key'],
            'notify_url' => route('notify', ['type' => 'wechat']),
        ];
        $pay = Pay::wechat($config);
        try {
            $parameters = [
                'out_trade_no' => $order->order_sn,
                'body' => $titles[rand(0, count($titles) - 1)],
                'total_fee' => $order->goods_price * 100,
            ];
            $result = $pay->scan($parameters);
        }catch (\Exception $e){
            return_bob($e->getMessage());
        }
        $url = $result->code_url;
        Cache::tags('wechat')->put($order->order_sn, $url, now()->addMinutes(20));

        return $url;
    }

    /**
     * @param $params
     * @return array|false
     */
    public function notify(Request $request)
    {
        $config = [
            'app_id' => $this->config['wechat_app_id'],
            'mch_id' => $this->config['wechat_mch_id'],
            'key' => $this->config['wechat_key'],
        ];
        $pay = Pay::wechat($config);
        try{
            $result = $pay->verify(); // 是的，验签就这么简单！
            Log::debug('Wechat notify', $result->all());
            return [
                'trade_no' => $result->out_trade_no,
                'callback_no' => $result->trade_no
            ];
        } catch (\Exception $e) {
            Log::error('Wechat Pay Error', $e->getMessage());
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
            'app_id' => $this->config['wechat_app_id'],
            'mch_id' => $this->config['wechat_mch_id'],
            'key' => $this->config['wechat_key'],
        ];
        $pay = Pay::wechat($config);
        $response = $pay->refund([
            'out_trade_no' => $order->order_sn,
            'out_refund_no' => time(),
            'total_fee' => $order->goods_price * 100,
            'refund_fee' => $order->goods_price * 100,
            'refund_desc' => "正常退款，感谢使用",
        ]);

        return $response;
    }
}

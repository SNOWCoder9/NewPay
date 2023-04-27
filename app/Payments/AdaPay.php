<?php

namespace App\Payments;

use App\Payments\AdaPay\AdaPayCore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yansongda\Pay\Pay;

class AdaPay
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function config()
    {
        return [
            'name'        => 'AdaPay',
            'show_name'   => 'AdaPay(汇付)',
            'config' => [
                'app_id' => [
                    'name' => 'APPID',
                    'type' => 'text',
                    'note' => '渠道ID',
                ],
                'open_id' => [
                    'name' => 'open_id',
                    'type' => 'text',
                    'note' => '微信渠道的微信openid',
                ],
                'api_key_live' => [
                    'name' => 'API密钥',
                    'type' => 'textarea',
                    'note' => 'API密钥(prod模式)',
                ],
                'rsa_private_key' => [
                    'name' => '商户私钥',
                    'type' => 'textarea',
                    'note' => '商户私钥',
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
        $cache_key = $order->token . '_' . $order->order_sn;
        if ($url = Cache::tags('adapay')->get($cache_key, '')) {
            return $url;
        }
        $titles = explode(PHP_EOL, getConfig('order_goods_name'));
        $key = str_replace(PHP_EOL, '', $this->config['rsa_private_key']);
        $config = [
            'app_id' => $this->config['app_id'],
            'api_key_live' => $this->config['api_key_live'],
            'rsa_private_key' => $key,
        ];
        $app = AdaPayCore::config($config)->order([
            'trade_no' => $order->order_sn,
            'money' => (float)$order->goods_price,
            'goods_title' => $titles[rand(0, count($titles) - 1)],
            'goods_desc' => $order->order_sn,
            'notify_url' => $order->notify_url,
            'currency' => 'cny',
        ]);
        try{
            $result = $app->submit($order->token == 'alipay' ? 'alipay' : 'wx_pub', $this->config['open_id']);
        }catch (\Exception $e) {
            return_bob($e->getMessage());
        }
        $code_url = $result['expend']['pay_info'];

        Cache::tags('adapay')->put($cache_key, $code_url, now()->addMinutes(20));

        return $code_url;
    }

    /**
     * @param $params
     * @return array|false
     */
    public function notify(Request $request)
    {
        $params = $request->all();
        $key = str_replace(PHP_EOL, '', $this->config['rsa_private_key']);
        $config = [
            'app_id' => $this->config['app_id'],
            'api_key_live' => $this->config['api_key_live'],
            'rsa_private_key' => $key,
        ];
        $app = AdaPayCore::config($config);
        if ($app->ada_tools->verifySign($params['sign'], $params['data'])){
            $data = json_decode($params['data'] , true);
            if ($data['status'] == 'succeeded') {
                return [
                    'trade_no' => $data['order_no'],
                    'callback_no' => $data['id']
                ];
            } else {
                return false;
            }
        }
    }

    public function refund($order)
    {
        $key = str_replace(PHP_EOL, '', $this->config['rsa_private_key']);
        $config = [
            'app_id' => $this->config['app_id'],
            'api_key_live' => $this->config['api_key_live'],
            'rsa_private_key' => $key,
        ];
        $app = AdaPayCore::config($config);
        $res = $app->createRefund([
            'payment_id' => $order->callback_no,
            'refund_order_no' => TRADE_NO.'REF',
            'refund_amt' => $order->goods_price
        ]);

        return $res;
    }
}

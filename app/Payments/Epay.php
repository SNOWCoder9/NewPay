<?php

namespace App\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Epay
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function config()
    {
        return [
            'name' => 'Epay',
            'show_name' => 'Epay',
            'config' => [
                'url' => [
                    'name' => 'url',
                    'type' => 'text',
                    'note' => 'URL',
                ],
                'pid' => [
                    'name' => 'pid',
                    'type' => 'text',
                    'note' => '商户ID',
                ],
                'key' => [
                    'name' => 'key',
                    'type' => 'text',
                    'note' => '密钥',
                ],
                'type' => [
                    'name' => 'type',
                    'type' => 'text',
                    'note' => '支付方式：alipay或wxpay，不填默认wxpay',
                ],
                'method' => [
                    'name' => 'method',
                    'type' => 'text',
                    'note' => '跳转方式：mapi或submit，不填默认mapi',
                ]
            ]
        ];
    }

    public function purchase($order)
    {
        $cache_key = $order->token . '_' . $order->order_sn;
        if ($url = Cache::tags('Epay')->get($cache_key, '')) {
            return $url;
        }
        $method = $this->config['method'] ?: 'mapi';
        $url = $this->config['url'] . '/' . $method . '.php';
        $params = [
            'pid' => $this->config['pid'],
            'type' => $this->config['type'] ?: 'wxpay',
            'out_trade_no' => $order->order_sn,
            'notify_url' => $order->notify_url,
            // 'return_url' => $order->return_url,
            'money' => $order->goods_price,
            'name' => $order->order_sn,
            'clientip' => $this->getClientIp(),
        ];
        ksort($params);
        reset($params);
        $str = urldecode(http_build_query($params)) . $this->config['key'];
        $params['sign'] = md5($str);
        $params['sign_type'] = 'MD5';

        if ($method == 'submit') {
            $payUrl = $url . '?' . http_build_query($params);
        }else{
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            $res = curl_exec($curl);
            curl_close($curl);
            $result = @json_decode($res, true);
            if ($result['code'] != 1) return_bob($result['msg']);
            $payUrl = $result['qrcode'] ?? $result['payurl'];
        }
        Cache::tags('Epay')->put($cache_key, $payUrl, now()->addMinutes(20));
        return $payUrl;
    }

    public function notify(Request $request)
    {
        $params = $request->all();
        $sign = $params['sign'];
        unset($params['sign'], $params['sign_type']);
        ksort($params);
        $str = http_build_query($params) . $this->config['key'];
        if ($sign !== md5($str)) return false;

        return [
            'trade_no' => $params['out_trade_no'],
            'callback_no' => $params['trade_no'],
            'response_text' => 'success'
        ];
    }

    protected function getClientIp()
    {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
            $cip = getenv("REMOTE_ADDR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $cip = getenv("HTTP_CLIENT_IP");
        } else {
            $cip = "127.0.0.1";
        }
        return $cip;
    }
}

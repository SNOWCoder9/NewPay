<?php

namespace App\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AlphaPay
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function config()
    {
        return [
            'name' => 'AlphaPay',
            'show_name' => 'AlphaPay',
            'config' => [
                'url' => [
                    'name' => 'URL',
                    'type' => 'text',
                    'note' => 'URL',
                ],
                'app_id' => [
                    'name' => 'APPID',
                    'type' => 'text',
                    'note' => 'APPID',
                ],
                'app_secret' => [
                    'name' => 'APPSECRET',
                    'type' => 'text',
                    'note' => 'APPSECRET',
                ],
            ]
        ];
    }

    /**
     *
     * @param $order
     *
     * @return array|mixed
     * @throws \App\Exceptions\BobException
     */
    public function purchase($order)
    {
        $cache_key = $order->token . '_' . $order->order_sn;
        if ($url = Cache::tags('AlphaPay')->get($cache_key, '')) {
            return $url;
        }
        $params = [
            'app_id' => $this->config['app_id'],
            'out_trade_no' => $order->order_sn,
            'total_amount' => $order->goods_price * 100,
            'payment' => $order->token,
            'notify_url' => $order->notify_url,
            'return_url' => $order->return_url
        ];
        ksort($params);
        $str = http_build_query($params);
        $params['sign'] = strtolower(md5($str . $this->config['app_secret']));
        $res = $this->sendRequest($params);
        $result = @json_decode($res, true);
        if (!$result) return_bob('支付网关处理失败');
        if ($result['code'] === 0) return_bob($result['msg']);
        Cache::tags('AlphaPay')->put($cache_key, $result['url'], now()->addMinutes(20));
        return $result['url'];
    }

    /**
     * @param $params
     *
     * @return array|false
     */
    public function notify(Request $request)
    {
        $params = $request->all();
        $sign = $params['sign'];
        unset($params['sign']);
        ksort($params);
        $str = strtolower(md5(http_build_query($params) . $this->config['app_secret']));
        if ($sign !== $str) return false;
        return [
            'trade_no' => $params['out_trade_no'],
            'callback_no' => $params['trade_no']
        ];
    }

    /**
     * 发送请求
     *
     * @param $data
     *
     * @return bool|string
     */
    public function sendRequest($data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->config['url'] . '/api/v1/tronDirect');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}

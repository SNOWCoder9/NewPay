<?php

namespace App\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OuiTrustWechat
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function config()
    {
        return [
            'name' => 'OuiTrustWechat',
            'show_name' => 'OuiTrust微信',
            'config' => [
                'merchant_no' => [
                    'name' => '商户编号',
                    'type' => 'text',
                    'note' => 'Merchant ID',
                ],
                'sign_key' => [
                    'name' => 'Sign key',
                    'type' => 'textarea',
                    'note' => 'Sign key',
                ]
            ]
        ];
    }

    /**
     * @param $order
     *
     * @return array|mixed|null
     * @throws \App\Exceptions\BobException
     */
    public function purchase($order)
    {
        $cache_key = $order->token . '_' . $order->order_sn;
        if ($url = Cache::tags('ouitrust_wechat')->get($cache_key, '')) {
            return $url;
        }
        $url = 'https://gateway.wepayez.com/pay/gateway';
        $nonce_str = GetRandStr(8);

        // 获取汇率
        $rateResult = $this->rate();
        if (!$rateResult['success']) return_bob($rateResult['message']);
        $total_fee = $order->goods_price * 0.02 + $order->goods_price;
        $total_fee = intval($total_fee * $rateResult['data']['price'] * 100);

        $params = [
            'service' => 'pay.weixin.wap.intl',
            'mch_id' => $this->config['merchant_no'],
            'out_trade_no' => $order->order_sn,
            'body' => $order->order_sn,
            'total_fee' => $total_fee,
            'mch_create_ip' => $order->order_sn,
            'notify_url' => $order->notify_url,
            'nonce_str' => $nonce_str,
            'limit_credit_pay' => 1,
            'user_ip' => $this->get_client_ip()
        ];
        ksort($params, SORT_STRING);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $params['sign'] = strtoupper(md5($str . 'key=' . $this->config['sign_key']));
        $data = $this->arrayToXml($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:text/xml; charset=utf-8"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $response = json_decode(json_encode(@simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA)), 1);
        $url = $response['pay_url'];

        Cache::tags('ouitrust_wechat')->put($cache_key, $url, now()->addMinutes(20));

        return $url;
    }

    /**
     * @param $params
     *
     * @return array|false
     */
    public function notify(Request $request)
    {
        $xml = file_get_contents('php://input');
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $params = $data;
        unset($params['sign']);
        ksort($params, SORT_STRING);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $sign = md5($str . 'key=' . $this->config['sign_key']);

        if (strtoupper($sign) == $data['sign']) {
            return [
                'trade_no' => $data['out_trade_no'],
                'callback_no' => $data['out_transaction_id']
            ];
        } else {
            return false;
        }
    }

    private function rate()
    {
        $cacheKey = 'CNY2HKD_RATE';
        if ($rate = Cache::get($cacheKey)) {
            return ['success' => true, 'data' => $rate];
        }
        $url = 'https://www.mxnzp.com/api/exchange_rate/aim?from=CNY&to=GBP&app_id=fqknuiqhhqihlosl&app_secret=MlBXeFJzY0ZWNm01V3gvWjI1SXJJdz09';
        $result = @json_decode(file_get_contents($url), true);
        if (!isset($result['code']) || $result['code'] != 1) {
            return ['success' => false, 'message' => '请求接口失败'];
        }
        Cache::put($cacheKey, $result['data'], now()->addMinutes(30));
        return ['success' => true, 'data' => $result['data']];
    }

    private function get_client_ip()
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

    private function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }
}

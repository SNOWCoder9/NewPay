<?php

namespace App\Payments;

use App\Util\Strs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UnipockScanpay
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function config()
    {
        return [
            'name' => 'UnipockScanpay',
            'show_name' => 'Unipock扫码支付',
            'config' => [
                'url' => [
                    'name' => '请求地址',
                    'type' => 'text',
                    'note' => '请输入请求地址',
                ],
                'mrcht_id' => [
                    'name' => '商户ID',
                    'type' => 'text',
                    'note' => '请输入您的商户ID',
                ],
                'sign_key' => [
                    'name' => '签名key',
                    'type' => 'text',
                    'note' => '请输入您的签名key',
                ],
                'channel' => [
                    'name' => '渠道标识',
                    'type' => 'text',
                    'note' => '渠道标识：0000 微信跨境、0001 微信香港本地、0002 支付宝跨境、0003 支付宝香港本地',
                ],
                'rate' => [
                    'name' => '费率',
                    'type' => 'text',
                    'note' => '额外加费（费率），例如：0.03',
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
        $cacheKey = $order->token . '_' . $order->order_sn;
        if ($url = Cache::tags('UnipockScanpay')->get($cacheKey, '')) {
            return $url;
        }
        // 获取汇率
        $currencyResult = $this->linkCurrencyTranslation('HKD');
        if (!$currencyResult['success']) return_bob($currencyResult['message']);
        $price = $order->goods_price * $this->config['rate'] + $order->goods_price;
        $price = round($price / round($currencyResult['data']['Rate'], 2), 2);
        $requestUrl = rtrim($this->config['url'], '/') . "/payway/payment/scanpay";
        $params = [
            'MrchtID' => $this->config['mrcht_id'],
            'MrchtOrderNo' => $order->order_sn,
            'Product' => $order->order_sn,
            'TotalAmt' => (int)($price * 100),
            'NotifyURL' => $order->notify_url,
            'Channel' => $this->config['channel'],
            'NonceStr' => Strs::randString(32)
        ];
        ksort($params);
        $str = '';
        foreach ($params as $k => $val) {
            $str .= $k . '=' . $val . '&';
        }
        $str .= 'key=' . $this->config['sign_key'];
        $params['Signature'] = strtoupper(md5($str));
        $res = $this->sendRequest($requestUrl, $params);
        $result = @json_decode($res, true);
        if ($result['ReqStatus'] === 'FAIL') return_bob($result['ReturnMsg']);
        $qrcodeUrl = $result['CodeURL'];
        Cache::tags('UnipockScanpay')->put($cacheKey, $qrcodeUrl, now()->addMinutes(20));
        return $qrcodeUrl;
    }

    public function notify(Request $request)
    {
        $params = $request->all();
        $signature = $params['Signature'];
        unset($params['Signature']);
        ksort($params);
        $str = '';
        foreach ($params as $k => $val) {
            $str .= $k . '=' . $val . '&';
        }
        $str .= 'key=' . $this->config['sign_key'];
        $sign = strtoupper(md5($str));
        if ($sign !== $signature) return false;

        if ($params['PayStatus'] == 'SUCCESS') {
            return [
                'trade_no' => $params['MrchtOrderNo'],
                'callback_no' => $params['OrderNo']
            ];
        } else {
            return false;
        }
    }

    /**
     * 汇率
     *
     * @param $currency
     *
     * @return array
     */
    protected function linkCurrencyTranslation($currency = 'HKD')
    {
        $cacheKey = 'Unipock:' . $currency;
        if ($result = Cache::get($cacheKey)) return returnSuccessQian($result);
        $requestUrl = rtrim($this->config['url'], '/') . '/payway/payment/rate';
        $params = [
            'MrchtID' => $this->config['mrcht_id'],
            'Currency' => $currency,
            'NonceStr' => Strs::randString(32)
        ];
        ksort($params);
        $str = '';
        foreach ($params as $k => $val) {
            $str .= $k . '=' . $val . '&';
        }
        $str .= 'key=' . $this->config['sign_key'];
        $params['Signature'] = strtoupper(md5($str));
        $res = $this->sendRequest($requestUrl, $params);
        $result = @json_decode($res, true);
        if ($result['ReqStatus'] === 'FAIL') return returnFailQian($result['ReturnMsg']);
        Cache::put($cacheKey, $result, now()->addMinutes(30));
        return returnSuccessQian($result);
    }

    /**
     * 发送请求
     *
     * @param            $url
     * @param array|NULL $params
     *
     * @return bool|string
     */
    protected function sendRequest($url, array $params = NULL)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, @json_encode($params, JSON_UNESCAPED_UNICODE));
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}

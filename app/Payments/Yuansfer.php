<?php

namespace App\Payments;

use App\Enum\OrderEnum;
use App\Enum\TypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Yuansfer
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function config()
    {
        return [
            'name'        => 'Yuansfer',
            'show_name'   => 'Yuansfer支付',
            'config' => [
                'merchant_id' => [
                    'name' => '商户编号',
                    'type' => 'text',
                    'note' => 'Merchant ID',
                ],
                'store_id' => [
                    'name' => '店铺编号',
                    'type' => 'text',
                    'note' => 'Store ID',
                ],
                'token' => [
                    'name' => 'API TOKEN',
                    'type' => 'textarea',
                    'note' => 'API TOKEN',
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
        if ($url = Cache::tags('yuansfer')->get($cache_key, '')) {
            return $url;
        }
        $url = 'https://mapi.yuansfer.com/online/v3/secure-pay';
        $params = [
            'merchantNo' => $this->config['merchant_id'],
            'storeNo' => $this->config['store_id'],
            'amount' => $order->goods_price,
            'currency' => 'CNY',
            'settleCurrency' => 'USD',
            'vendor' => $order->type === TypeEnum::ALIPAY ? 'alipay' : 'wechatpay',
            'terminal' => $order->platform == 'h5' ? 'WAP' : 'ONLINE',
            'reference' => $order->order_sn,
            'ipnUrl' => $order->notify_url,
            'callbackUrl' => $order->return_url,
        ];
        ksort($params, SORT_STRING);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $params['verifySign'] = md5($str . md5($this->config['token']));
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
        ));
        $result = curl_exec($ch);
        curl_exec($ch);
        $result = json_decode($result, true);
//        $url = $result['result']['cashierUrl'];
        if ($order->platform == 'h5'){
            $url = $result['result']['cashierUrl'];
        } else {
            $url = "https://mapi.yuansfer.com/app-redirect-record/yuancheck-to-alicheck/".$result['result']['transactionNo'];
        }
        Cache::tags('yuansfer')->put($cache_key, $url, now()->addMinutes(20));

        return $url;
    }

    /**
     * @param $params
     * @return array|false
     */
    public function notify(Request $request)
    {
        $param_post = $request->all();
        $params = [];
        $invoiceId =  $param_post["reference"];
        $status = $param_post["status"];
        $transactionId = $param_post["transactionNo"];
        $verifySign = $param_post['verifySign'];
        $params["amount"] = $param_post["amount"];
        $params["currency"] = $param_post["currency"];
        $params["reference"] = $param_post["reference"];
        $params["settleCurrency"] = $param_post["settleCurrency"];
        $params["status"] = $param_post["status"];
        $params["time"] = $param_post["time"];
        $params["transactionNo"] = $param_post["transactionNo"];

        ksort($params, SORT_STRING);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $sign = md5($str . md5($this->config['token']));
        if($status == 'success' && $sign == $verifySign){
            return [
                'trade_no' => $invoiceId,
                'callback_no' => $transactionId
            ];
        } else {
            return false;
        }
    }

    /**
     * @param $order
     * @return \BobCoders9\Cashier\Responses\Refund
     */
    public function refund($order)
    {
        $url = 'https://mapi.yuansfer.com/app-data-search/v3/refund';
        $params = [
            'merchantNo' => $this->config['merchant_id'],
            'storeNo' => $this->config['store_id'],
            'amount' => $order->goods_price,
            'reference' => $order->order_sn
        ];
        ksort($params, SORT_STRING);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $params['verifySign'] = md5($str . md5($this->config['token']));
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
        ));
        $result = curl_exec($ch);
        curl_exec($ch);

        return json_decode($result, true);
    }
}

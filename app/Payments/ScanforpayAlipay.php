<?php

namespace App\Payments;

use App\Services\CurlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ScanforpayAlipay
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function config()
    {
        return [
            'name' => 'ScanforpayAlipay',
            'show_name' => 'Scanforpay支付宝',
            'config' => [
                'url' => [
                    'name' => '请求地址',
                    'type' => 'text',
                    'note' => '例如：https://xxxx.com/',
                ],
                'store_no' => [
                    'name' => '门店编号',
                    'type' => 'text',
                    'note' => '',
                ],
                'partner_no' => [
                    'name' => '合作伙伴编号',
                    'type' => 'text',
                    'note' => '',
                ],
                'key' => [
                    'name' => 'Key',
                    'type' => 'text',
                    'note' => '',
                ],
                'wallet' => [
                    'name' => '钱包',
                    'type' => 'text',
                    'note' => 'AlipayHK: 支付宝香港；Alipay: 支付宝',
                ],
                'rate' => [
                    'name' => '通道费率',
                    'type' => 'text',
                    'note' => '例如：0.022',
                ],
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
        if ($url = Cache::tags('scanforpay')->get($cacheKey, '')) {
            return $url;
        }
        $url = $this->config['url'] . 'api/online/create';
        $header = [
            'version' => '1.0',
            'partnerNo' => $this->config['partner_no'],
            'reqMsgId' => $this->generate(),
            'reqTime' => date("Y-m-d\TH:i:s", time()) . '+08:00',
        ];
        // 获取汇率
        $rateResult = $this->rate();
        if (!$rateResult['success']) return_bob($rateResult['message']);
        $totalFee = $order->goods_price * $this->config['rate'] + $order->goods_price;
        $totalFee = intval($totalFee * $rateResult['data']['price'] * 100);

        $body = [
            'storeNo' => $this->config['store_no'],
            'partnerOrderNo' => $order->order_sn,
            'wallet' => $this->config['wallet'],
            'currency' => 'HKD',
            'orderAmount' => $totalFee,
            'notifyUrl' => $order->notify_url,
            // 'returnUrl' => $order->return_url,
        ];
        $req = [
            'header' => $header,
            'body' => $body,
        ];
        $signature = bin2hex(hash('sha256', json_encode($req) . $this->config['key'], true));
        $response = (new CurlService())->postCurl($url, [
            'request' => $req,
            'signature' => $signature,
        ], true);
        $result = @json_decode($response, true);
        if ($result['response']['body']['code'] == 1) {
            $url = $result['response']['body']['payUrl'];
            Cache::tags('scanforpay')->put($cacheKey, $url, now()->addMinutes(20));
            return $url;
        }else{
            return_bob($result['response']['body']['msg']);
        }
    }

    /**
     * @param $params
     *
     * @return array|false
     */
    public function notify(Request $request)
    {
        $param_post = $request->all();

        $header = $param_post['response']['header'];
        $body = $param_post['response']['body'];
        $respSignature = $param_post['signature'];
        $resp = @json_encode([
            'header' => $header,
            'body' => $body
        ], JSON_UNESCAPED_UNICODE);
        $signature = bin2hex(hash('sha256', $resp . $this->config['key'], true));

        if ($respSignature !== $signature) return false;

        if ($body['code'] == 1 && $body['status'] == 1) {
            return [
                'trade_no' => $body['partnerOrderNo'],
                'callback_no' => $body['orderNo']
            ];
        } else {
            return false;
        }
    }

    private function rate()
    {
        $cacheKey = 'CNYtoHKD_RATE';
        if ($rate = Cache::get($cacheKey)) {
            return ['success' => true, 'data' => $rate];
        }
        $url = 'https://www.mxnzp.com/api/exchange_rate/aim?from=CNY&to=HKD&app_id=fqknuiqhhqihlosl&app_secret=MlBXeFJzY0ZWNm01V3gvWjI1SXJJdz09';
        $result = @json_decode(file_get_contents($url), true);
        if (!isset($result['code']) || $result['code'] != 1) {
            return ['success' => false, 'message' => '请求接口失败'];
        }
        Cache::put($cacheKey, $result['data'], now()->addMinutes(30));
        return ['success' => true, 'data' => $result['data']];
    }

    /**
     * 生成唯一请求id
     *
     * @return String
     */
    public function generate()
    {
        // 使用session_create_id()方法创建前缀
        $prefix = session_create_id(date('YmdHis'));
        // 使用uniqid()方法创建唯一id
        $request_id = strtoupper(md5(uniqid($prefix, true)));
        // 格式化请求id
        return $this->format($request_id);
    }

    /**
     * 格式化请求id
     *
     * @param string $request_id 请求id
     * @param array  $format     格式
     *
     * @return string
     */
    private function format($request_id, $format = '8,4,4,4,12')
    {
        $tmp = array();
        $offset = 0;
        $cut = explode(',', $format);
        // 根据设定格式化
        if ($cut) {
            foreach ($cut as $v) {
                $tmp[] = substr($request_id, $offset, $v);
                $offset += $v;
            }
        }
        // 加入剩余部分
        if ($offset < strlen($request_id)) {
            $tmp[] = substr($request_id, $offset);
        }
        return implode('-', $tmp);
    }
}

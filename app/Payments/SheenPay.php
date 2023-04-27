<?php
/**
 * SheenPay
 */

namespace App\Payments;

use App\Services\SheenPayApiService\SheenPayApi;
use App\Services\SheenPayApiService\SheenPayParams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SheenPay
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function config()
    {
        return [
            'name' => 'SheenPay',
            'show_name' => 'SheenPay',
            'config' => [
                'partner_code' => [
                    'name' => '商户编码',
                    'type' => 'text',
                    'note' => '商户编码',
                ],
                'credential_code' => [
                    'name' => '校验码',
                    'type' => 'text',
                    'note' => '校验码（key）',
                ],
                'request_url' => [
                    'name' => '请求域名',
                    'type' => 'text',
                    'note' => '请求域名（以/结尾）',
                ]
            ]
        ];
    }

    public function purchase($order)
    {
        if ($url = Cache::tags('sheen_pay')->get($order->order_sn, '')) return $url;

        // 标题
        $titles = explode(PHP_EOL, getConfig('order_goods_name'));
        $title = $titles[rand(0, count($titles) - 1)];

        // 构建支付参数
        $sheenPayParams = new SheenPayParams();
        $sheenPayParams->setOrderId($order->order_sn);
        $sheenPayParams->setDescription($title);
        $sheenPayParams->setPrice($order->goods_price * 100);
        $sheenPayParams->setCurrency('CNY');
        $sheenPayParams->setChannel('Wechat');
        // $sheenPayParams->setNotifyUrl(route('notify', ['type' => 'order', 'method' => 'wechat']));
        $sheenPayParams->setNotifyUrl($order->notify_url);
        // 拉支付
        $sheenPayApi = SheenPayApi::getInstance($this->config);
        $result = $sheenPayApi->createQrCodeOrder($sheenPayParams);
        if (false === $result['success']) return_bob($result['message']);

        Cache::tags('sheen_pay')->put($order->order_sn, $result['data']['code_url'], now()->addMinutes(20));

        return $result['data']['code_url'];
    }

    /**
     * 支付结果回调
     *
     * @param Request $request
     *
     * @return array|false
     */
    public function notify(Request $request)
    {
        $params = $request->input();

        $sheenPayParams = new SheenPayParams();
        $sheenPayParams->setPartnerCode($this->config['partner_code']);
        $sheenPayParams->setNonceStr($params['nonce_str']);
        $sheenPayParams->setTime($params['time']);
        $sheenPayParams->setSign($this->config['credential_code']);
        if ($sheenPayParams->getSign() == $params['sign']) {
            return [
                'trade_no' => $params['partner_order_id'],
                'callback_no' => $params['order_id'],
                'return' => @json_encode(['return_code' => 'SUCCESS'], JSON_UNESCAPED_UNICODE)
            ];
        } else {
            return false;
        }
    }
}

<?php

namespace App\Services;

class EPay extends AbstractPayment
{
    /**
     * @var string
     */
    protected $appID;
    /**
     * @var string
     */
    protected $gatewayUri;
    /**
     * @var string
     */
    private $appSecret;

    public function __construct()
    {
        $this->appID = '';
        $this->appSecret = '';
        $this->gatewayUri = "";
    }

    public function purchase($order)
    {
        $params = [
            'money' => $order->total_amount,
            'name' => $order->trade_no,
            'notify_url' => route('notify', ['type' => 'buy_shop', 'method' => 'epay']),
            'return_url' => route('index'),
            'out_trade_no' => $order->trade_no,
            'pid' => $this->appID
        ];
        ksort($params);
        reset($params);
        $str = stripslashes(urldecode(http_build_query($params))) . $this->appSecret;
        $params['sign'] = md5($str);
        $params['sign_type'] = 'MD5';
        $toUrl = $this->gatewayUri . '/submit.php?' . http_build_query($params);
        $order->url_type = 'url';
        $order->pay_url = $toUrl;

        return ['type' => $order->url_type, 'url' => $order->pay_url, 'trade_no' => $order->trade_no];
    }

    /**
     * @throws \App\Exceptions\BobException
     */
    public function notify($request)
    {
        $params = $request->all();
        $sign = $params['sign'];
        unset($params['sign']);
        unset($params['sign_type']);
        ksort($params);
        reset($params);
        $str = stripslashes(urldecode(http_build_query($params))) . $this->appSecret;
        if ($sign !== md5($str)) {
            return response()->json(['code' => 0, 'message' => '验证失败'])->setStatusCode(400);
        }
        BuyShop::postPayment($params['out_trade_no'], $params['trade_no']);

        return response()->json(['code' => 1, 'message' => '成功'])->setStatusCode(200);
    }
}

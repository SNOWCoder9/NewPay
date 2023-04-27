<?php


namespace App\Services;


use Illuminate\Http\Request;

class BobPay extends AbstractPayment
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

    /**
     * @name    准备签名/验签字符串
     */
    public function prepareSign($data)
    {
        ksort($data);
        return http_build_query($data);
    }

    /**
     * @name    生成签名
     * @param sourceData
     * @return    签名数据
     */
    public function sign($data)
    {
        return strtolower(md5($data . $this->appSecret));
    }

    /*
     * @name    验证签名
     * @param   signData 签名数据
     * @param   sourceData 原数据
     * @return
     */
    public function verify($data, $signature)
    {
        unset($data['sign']);
        $mySign = $this->sign($this->prepareSign($data));
        return $mySign === $signature;
    }

    public function post($data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->gatewayUri . '/api/v1/tron');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['User-Agent: BobTronPay']);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    /**
     * @param $order
     * @return mixed
     * @throws \App\Exceptions\BobException
     */
    public function purchase($order)
    {
        $data['app_id'] = $this->appID;
        $data['out_trade_no'] = $order->trade_no;
        $data['total_amount'] = (int)($order->total_amount * 100);
        $data['notify_url'] = route('notify', ['type' => 'buy_shop', 'method' => 'bobpay']);
        $data['return_url'] = route('index');
        $data['type'] = $order->payment;
        $params = $this->prepareSign($data);
        $data['sign'] = $this->sign($params);
        $result = json_decode($this->post($data), true);
        if ($result['code'] === 0) {
            return_bob('支付网关处理失败');
        }
        $order->url_type = 'url';
        $order->pay_url = $result['url'];
        $order->save();

        return ['type' => $order->url_type, 'url' => $order->pay_url, 'trade_no' => $order->trade_no];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|object
     * @throws \App\Exceptions\BobException
     */
    public function notify($request)
    {
        $data = $request->all();
        if (!$this->verify($data, $data['sign'])) {
            return response()->json(['code' => 0, 'message' => '验证失败'])->setStatusCode(400);
        }
        BuyShop::postPayment($data['out_trade_no'], $data['trade_no']);

        return response()->json(['code' => 1, 'message' => '成功'])->setStatusCode(200);
    }
}

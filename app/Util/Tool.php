<?php

namespace App\Util;

use Illuminate\Support\Facades\Cache;

class Tool
{
    /**
     * @param $payment
     * @param $user
     * @param $order
     * @return float
     * @throws \Exception
     */
    public static function calcTokenPrice($payment, $user, $order)
    {
        if ($price = Cache::tags($payment)->get($order->order_sn, '')) {
            return $price;
        }
        if ($payment === 'BTC' && $order->goods_price < 50) {
            return_bob('金额小于100块钱的订单不支持使用BTC支付~', 2, 200);
        }
        if ($payment === 'ETH' && $order->goods_price < 20) {
            return_bob('金额小于50块钱的订单不支持使用ETH支付~', 2, 200);
        }
        if ($payment === 'USDT' && $order->goods_price < 5) {
            return_bob('金额小于5块钱的订单不支持使用USDT支付~', 2, 200);
        }
        if ($data = Cache::tags($payment)->get($order->order_sn, '')){
            return $data;
        }
        $num = $payment === 'USDT' ? 1000 : 100000;
        $scale = $payment === 'USDT' ? 3 : 6;
        switch ($payment){
            case 'BTC':
                $r = 20;break;
            case 'ETH':
                $r = 50;break;
            case 'USDT':
                $r = 100;break;
        }
        $rand = random_int(3, $r) / $num;
        $price = (float)bcdiv($order->goods_price, Cache::get($payment.'_CNY'), $scale);
//        $price = (float)sprintf($spr, $order->goods_price / Cache::get($payment.'_CNY'));
        $token_price = $price + $rand;
        $key = 'pay_' . $user->id . '_' . $token_price * $num;
        if (Cache::tags($payment."_pay")->has($key)) {
            $token_price = $token_price + (random_int(3, $r) / $num);
        }
        Cache::tags($payment."_pay")->put($key, $token_price, now()->addMinutes(20));
        Cache::tags($payment)->put($order->order_sn, $token_price, now()->addMinutes(20));

        return $token_price;
    }

    public static function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;// 找不到为flase,否则为TRUE
        }
        // 判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = [
                'mobile',
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap'
            ];
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT'])) { // 协议法，因为有可能不准确，放到最后判断
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }

        return false;
    }

    public function getPaymentMethods()
    {
        $methods = [];
        foreach (glob(base_path('app//Payments') . '/*.php') as $file) {
            array_push($methods, pathinfo($file)['filename']);
        }
        return response([
            'data' => $methods
        ]);
    }
}

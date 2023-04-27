<?php

namespace App\Services;

use BobCoders9\Cashier\Cashier;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Alipay
{
    public static function setConfig($order = null)
    {
        $config = Cache::get('payment_alipay_config');
        $privateKey = $config['alipay_private_key'];
        $p_key = array();
        //如果私钥是 1行
        if (!stripos($privateKey, "\n")) {
            $i = 0;
            while ($key_str = substr($privateKey, $i * 64, 64)) {
                $p_key[] = $key_str;
                $i++;
            }
        }
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" . implode("\n", $p_key);
        $privateKey = $privateKey . "\n-----END RSA PRIVATE KEY-----";
        $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($config['alipay_public_key'], 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        if (isset($order->notify_method)){
            $notify_url = route('notify', ['type' => 'alipay', 'method' => $order->notify_method]);
        } else {
            $notify_url = route('notify', ['type' => 'alipay']);
        }
        return [
            'app_id' => $config['alipay_app_id'],
            'app_private_key' => $privateKey,
            'alipay_public_key' => $publicKey,
            'notify_url' => $notify_url
        ];
    }

    /**
     * @param $order
     * @param $platform
     * @return array|mixed|null
     * @throws \App\Exceptions\BobException
     */
    public static function pay($order, $platform)
    {
        if ($url = Cache::tags('alipay')->get($order->order_sn, '')) {
            return $url;
        }
        $config = self::setConfig($order);
        $titles = [
            '睡眠遮光透气发热护眼眼罩',
            'LORDE里兜纯豆腐砂经典款猫砂豆腐猫砂',
            '士(LUX)洗护套装 大白瓶 水润丝滑洗发乳750mlx2',
            '舒耐(REXONA)爽身香体止汗喷雾 净纯无香150ml',
            '苏泊尔supor 锅具套装居家不粘炒锅煎锅汤锅三件套装锅',
            '塑料抽屉式收纳柜 卧室床头柜置物柜 儿童衣柜',
            '塑料衣架宽肩无痕晾衣架子加厚防滑晾晒不鼓包西服大衣挂架',
            '春节对联春联大礼包超值18件套',
            '电动车头盔男全盔冬季双镜片揭面盔女',
            '无火香薰精油家用室内香氛空气清新剂'
        ];
        try {
            $cashier = new Cashier($platform === 'web' ? 'alipay_web' : 'alipay_wap', $config);
            $response = $cashier->charge([
                'order_id' => $order->order_sn,
                'subject' => $titles[rand(0, 9)],
                'description' => $titles[rand(0, 9)],
                'return_url' => $order->return_url,
                'amount' => $order->goods_price * 100,
                'currency' => 'CNY',
                'expired_at' => strtotime("+20 minute")
            ]);
        } catch (\Exception $e) {
            return_bob($e->getMessage(), 0, 200);
        }

        $url = $response->get('charge_url');
        Cache::tags('alipay')->put($order->order_sn, $url, now()->addMinutes(20));

        return $url;
    }

    /**
     * @param $params
     * @return array|false
     */
    public static function notify($params)
    {
        $config = self::setConfig();
        $cashier = new Cashier('alipay_web', $config);
        $response = $cashier->notify('charge', $params);
        if ('paid' === $response->get('status')) {
            return [
                'trade_no' => $params['out_trade_no'],
                'callback_no' => $params['trade_no']
            ];
        } else {
            return false;
        }
    }

    /**
     * @param $order
     * @return \BobCoders9\Cashier\Responses\Refund
     */
    public static function refund($order)
    {
        $config = self::setConfig();
        $cashier = new Cashier('alipay_web', $config);
        $response = $cashier->refund([
            'total_amount' => $order->goods_price * 100,
            'order_id' => $order->order_sn,
            'refund_amount' => $order->goods_price * 100,
            'reason' => "正常退款",
            'refund_id' => Str::random(16)
        ]);

        return $response;
    }
}

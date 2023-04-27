<?php

namespace App\Services;

use BobCoders9\Cashier\Cashier;
use Illuminate\Support\Facades\Cache;

class WeChat
{
    private static function config()
    {
        $config = Cache::get('payment_wechat_config');
        return [
            'trade_type'    => 'NATIVE',
            'app_id'         => $config['wechat_app_id'],
            'mch_id'        => $config['wechat_mch_id'],
            'mch_secret'          => $config['wechat_key'],
            'notify_url'    => route('notify', ['type' => 'wechat']),
            'site_url'      => '',
            'site_name'     => '心意服务',
        ];
    }

    /**
     * @throws \App\Exceptions\BobException
     */
    public static function pay($order, $platform)
    {
        if ($url = Cache::tags('wechat')->get($order->order_sn, '')){
            return $url;
        }
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
        $config = self::config();
        $parameters = [
            'subject' => $titles[rand(0, 9)],
            'order_id' => $order->order_sn,
            'currency' => 'CNY',
            'amount' => $order->goods_price * 100,
            'user_ip' => $_SERVER['SERVER_ADDR'],
            'description' => $titles[rand(0, 9)],
        ];

        try {
            $cashier = new Cashier('wechat_h5', $config);
            $response = $cashier->charge($parameters);
        }catch (\Exception $e){
            return_bob($e->getMessage());
        }
        $url = $response->get('charge_url');
        Cache::tags('wechat')->put($order->order_sn, $url, now()->addMinutes(20));

        return $url;
    }
}

<?php

namespace App\Enum;

class TypeEnum
{
    const VIRTUAL = 1;
    const ALIPAY = 2;
    const WECHAT = 3;
    const ALIPAY_HK = 4;
    const WECHAT_HK = 5;

    const type = [
        self::VIRTUAL => '虚拟货币',
        self::ALIPAY => '支付宝',
        self::WECHAT => '微信',
        self::ALIPAY_HK => '支付宝（香港）',
        self::WECHAT_HK => '微信（香港）',
    ];

    public static function toType($payment)
    {
        switch ($payment) {
            case 'alipay':
                return 2;
            case 'wechat':
                return 3;
            case 'alipay_hk':
                return 4;
            case 'wechat_hk':
                return 5;
            default:
                return 1;
        }
    }
}

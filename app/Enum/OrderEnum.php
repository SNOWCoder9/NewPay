<?php

namespace App\Enum;

class OrderEnum
{
    const REFUND = -1;
    const UNPAID = 0;
    const EXPIRED = 1;
    const SUCCESS = 2;
    const NOTICE = 3;
    const NOTICEFAIL = 4;

    const text = [
        self::REFUND => '已退款',
        self::UNPAID => '未支付',
        self::EXPIRED => '已过期',
        self::SUCCESS => '支付成功',
        self::NOTICE => '通知成功',
        self::NOTICEFAIL => '通知失败',
    ];
}

<?php
/**
 * @Title :
 * @Remark:
 */

namespace App\Enum;

class SettleEnum
{
    const NOT = 0;
    const SUCCESS = 1;

    const text = [
        self::NOT => '未结算',
        self::SUCCESS => '已结算'
    ];
}

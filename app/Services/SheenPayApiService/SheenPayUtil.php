<?php
/**
 * 工具类
 */

namespace App\Services\SheenPayApiService;

class SheenPayUtil
{
    /**
     * 获取毫秒级别的时间戳
     */
    public static function getMillisecond()
    {
        list($msec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }

    /**
     *
     * 随机字符串，不长于30位
     *
     * @param int $length
     *
     * @return string $str
     */
    public static function getNonceStr($length = 30)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}

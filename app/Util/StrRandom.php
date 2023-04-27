<?php

namespace App\Util;

class StrRandom
{
    /**
     * 随机生成邮箱
     *
     * @param $domain
     *
     * @return string
     */
    public static function randomEmail($type = '', $domain = '', $mix = 6, $max = 11)
    {
        $len = mt_rand($mix, $max);
        $domain = $domain ?: self::randomDomain();

        return Strs::randString($len, $type) . '@' . $domain;

    }

    /**
     * 获取一个随机的域名
     *
     * @return string
     */
    public static function randomDomain()
    {
        $len = mt_rand(6, 16);

        return strtolower(Strs::randString($len)) . '.' . self::randomTld();
    }

    /**
     * 随机生成一个顶级域名
     *
     * @return string
     */
    public static function randomTld()
    {
        $tldArr = [
            'com', 'cn', 'xin', 'net', 'top', '在线',
            'xyz', 'wang', 'shop', 'site', 'club', 'cc',
            'fun', 'online', 'biz', 'red', 'link', 'ltd',
            'mobi', 'info', 'org', 'edu', 'com.cn', 'net.cn',
            'org.cn', 'gov.cn', 'name', 'vip', 'pro', 'work',
            'tv', 'co', 'kim', 'group', 'tech', 'store', 'ren',
            'ink', 'pub', 'live', 'wiki', 'design', '中文网',
            '我爱你', '中国', '网址', '网店', '公司', '网络', '集团', 'app'
        ];
        shuffle($tldArr);

        return $tldArr[0];
    }

    public static function randomPhone()
    {
        $prefixArr = [
            133, 153, 173, 177, 180, 181, 189, 199, 134, 135,
            136, 137, 138, 139, 150, 151, 152, 157, 158, 159, 172, 178,
            182, 183, 184, 187, 188, 198, 130, 131, 132, 155, 156, 166,
            175, 176, 185, 186, 145, 147, 149, 170, 171
        ];
        shuffle($prefixArr);

        return $prefixArr[0] . Strs::randString(8, 1);
    }
}

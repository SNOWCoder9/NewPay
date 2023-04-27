<?php

use App\Exceptions\BobException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

function returnSuccessQian($data = null, $message = 'Success', $status = true)
{
    return ['success' => $status, 'data' => $data];
}

function returnFailQian($message = 'Failed', $data = null, $status = false)
{
    return ['success' => $status, 'message' => $message];
}

/**
 * 验证是否是中国手机号.
 *
 * @param string $number
 *
 * @return bool
 */
function validateChinaPhoneNumber(string $number): bool
{
    return (bool)preg_match('/^(\+?0?86\-?)?1[3-9]\d{9}$/', $number);
}

/**
 * Combines SQL and its bindings
 *
 * @param \Eloquent $query
 *
 * @return string
 */
function getEloquentSqlWithBindings($query)
{
    return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
        return is_numeric($binding) ? $binding : "'{$binding}'";
    })->toArray());
}

/**
 * Get user login field.
 *
 * @param string $login
 * @param string $default
 *
 * @return string
 */
function username(string $login, string $default = 'id'): string
{
    $map = [
        'email' => filter_var($login, FILTER_VALIDATE_EMAIL),
        'phone' => validateChinaPhoneNumber($login),
        'username' => validateUsername($login),
    ];

    foreach ($map as $field => $value) {
        if ($value) {
            return $field;
        }
    }

    return $default;
}

/**
 * 验证用户名是否合法.
 *
 * @param string $username
 *
 * @return bool
 */
function validateUsername(string $username): bool
{
    return (bool)preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $username);
}

/**
 * @author Bob(bobcoderss@gmail.com)
 *
 * @param int    $code
 * @param int    $StatusCode
 * @param string $message
 *
 * @throws BobException
 */
function return_bob(string $message, int $code = 0, $StatusCode = 400)
{
    throw new App\Exceptions\BobException($message, $code, $StatusCode);
}

/**
 * 将中文、英文、数字切割
 *
 * @author Bob(bobcoderss@gmail.com)
 *
 * @param $str
 *
 * @return array[]|false|string[]
 */
function bobPregSplit($str): array
{
    return preg_split("/([0-9]+)/", $str, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
}

/**
 * 生成菜单树
 *
 * @param $list
 * @param $pid
 *
 * @return array
 */
function generateRuleTree($list, $pid)
{
    $tree = [];
    foreach ($list as $row) {
        if ($row['pid'] === $pid) {
            $children = generateRuleTree($list, $row['id']);
            $row['children'] = $children ?? [];

            $tree[] = $row;
        }
    }

    return $tree;
}

/**
 * 根据出生年月日计算出年龄
 *
 * @param $birthday
 *
 * @return int
 */
function getAgeByBirth($birthday)
{
    $age = strtotime($birthday);
    if ($age === false) {
        return false;
    }
    list($y1, $m1, $d1) = explode("-", date("Y-m-d", $age));
    $now = strtotime("now");
    list($y2, $m2, $d2) = explode("-", date("Y-m-d", $now));
    $age = $y2 - $y1;
    if ((int)($m2 . $d2) < (int)($m1 . $d1))
        $age -= 1;

    return $age;
}

/**
 * 计算BMI
 *
 * @author Bob <bobcoderss@gmail.com>
 *
 * @param $weight
 * @param $height
 *
 * @return float|int
 */
function getBmiByHeightAndWeight(float $height, float $weight)
{
    $height = sprintf('%.2f', ($height / 100));
    $weight = sprintf('%.1f', $weight);
    $bmi = $weight / ($height * $height);

    return sprintf('%.2f', $bmi);
}

/**
 * 判断复合选项逻辑
 *
 * @author Bob <bobcoderss@gmail.com>
 *
 * @param $selectIds
 * @param $logic
 *
 * @return bool
 */
function judgeLogic($logic, $selectIds)
{
    $logic = explode('+', $logic);
    $status = [];
    foreach ($logic as $key => $str) {
        $str = trim($str);
        $status[$key] = false;
        if (strpos($str, '|')) {
            $newStr2 = explode('|', $str);
            foreach ($newStr2 as $a) {
                $a = trim($a);
                if (in_array($a, $selectIds)) {
                    $status[$key] = true;
                    break;
                }
            }
        } elseif (strpos($str, '&')) {
            $newStr2 = explode('&', $str);
            foreach ($newStr2 as $b) {
                $b = trim($b);
                if (in_array($b, $selectIds)) {
                    $status[$key] = true;
                    continue;
                }
                $status[$key] = false;
                break;
            }
        } else {
            $status[$key] = in_array($str, $selectIds) ? true : false;
        }
    }
    $status = array_unique($status);

    if (count($status) == 1 && $status[0]) {
        return true;
    }
    return false;
}

/**
 * 生成菜单树
 *
 * @param $list
 * @param $pid
 *
 * @return array
 */
function generateRuleTree2($list, $pid)
{
    $tree = [];
    foreach ($list as $row) {
        if ($row['pid'] === $pid) {
            $row = [
                'id' => $row['id'],
                'label' => $row['title'],
                'pid' => $row['pid'],
                'path' => $row['name'],
                'icon' => $row['icon'],
                'menu' => $row['menu']
            ];

            $children = generateRuleTree2($list, $row['id']);
            if (!empty($children)) {
                $row['children'] = $children;
            }

            $tree[] = $row;
        }
    }

    return $tree;
}

/**
 * 检查密码
 *
 * @param $user
 * @param $password
 *
 * @return bool
 */
function checkPassword($user, $password)
{
    $row = $user->makeVisible(['password'])->toArray();
    if (empty($row)) return false;

    if (password_verify($password, $row['password'])) {
        return true;
    }

    return false;
}

/**
 * 生成邀请码.
 *
 * @author: BobCoder
 * @return string
 */
function generateInviteCode()
{
    $code = substr(str_shuffle('ZXCVBNMLPOIUYTREWQASDFGHJK'), 0, mt_rand(1, 5));

    return substr(str_shuffle('1234567890'), 0, 6 - strlen($code)) . $code;
}


/**
 * 是否是emoji.
 *
 * @author: hh
 *
 * @param $s
 *
 * @return bool
 */
function isEmoji($s)
{
    $len = mb_strlen($s);

    for ($i = 0; $i < $len; $i++) {
        $word = mb_substr($s, $i, 1);
        if (strlen($word) > 3) {
            return true;
        }
    }

    return false;
}

/**
 * 获取汉子首字母.
 *
 * @author: BobCoder
 *
 * @param $str
 *
 * @return null|string
 */
function getFirstCharter($str)
{
    if (empty($str)) {
        return null;
    }

    if (isEmoji($str)) {
        return '#';
    }

    $fchar = ord($str[0]);

    if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str[0]);
    $s1 = iconv('UTF-8', 'gb2312', $str);
    $s2 = iconv('gb2312', 'UTF-8', $s1);
    $s = $s2 == $str ? $s1 : $str;
    $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
    if ($asc >= -20319 && $asc <= -20284) return 'A';
    if ($asc >= -20283 && $asc <= -19776) return 'B';
    if ($asc >= -19775 && $asc <= -19219) return 'C';
    if ($asc >= -19218 && $asc <= -18711) return 'D';
    if ($asc >= -18710 && $asc <= -18527) return 'E';
    if ($asc >= -18526 && $asc <= -18240) return 'F';
    if ($asc >= -18239 && $asc <= -17923) return 'G';
    if ($asc >= -17922 && $asc <= -17418) return 'H';
    if ($asc >= -17417 && $asc <= -16475) return 'J';
    if ($asc >= -16474 && $asc <= -16213) return 'K';
    if ($asc >= -16212 && $asc <= -15641) return 'L';
    if ($asc >= -15640 && $asc <= -15166) return 'M';
    if ($asc >= -15165 && $asc <= -14923) return 'N';
    if ($asc >= -14922 && $asc <= -14915) return 'O';
    if ($asc >= -14914 && $asc <= -14631) return 'P';
    if ($asc >= -14630 && $asc <= -14150) return 'Q';
    if ($asc >= -14149 && $asc <= -14091) return 'R';
    if ($asc >= -14090 && $asc <= -13319) return 'S';
    if ($asc >= -13318 && $asc <= -12839) return 'T';
    if ($asc >= -12838 && $asc <= -12557) return 'W';
    if ($asc >= -12556 && $asc <= -11848) return 'X';
    if ($asc >= -11847 && $asc <= -11056) return 'Y';
    if ($asc >= -11055 && $asc <= -10247) return 'Z';

    return '#';
}

/**
 * 生成分类树
 *
 * @author Bob<bobcoderss@gmail.com>
 *
 * @param $pid
 * @param $list
 *
 * @return array
 * @Date   2019/5/21
 */
function generateTree($list, $pid)
{
    $tree = [];
    foreach ($list as $row) {
        if ($row['pid'] === $pid) {
            //            $row = [
            //                'id' => $row['id'],
            //                'name' => $row['name'],
            //            ];
            $children = generateTree($list, $row['id']);
            if (!empty($children)) {
                $row['children'] = $children;
            }
            $tree[] = $row;
        }
    }
    return $tree;
}

/**
 * @author   Bob<bobcoderss@gmail.com>
 * @Date     2019/8/19
 *
 * @param $password
 *
 * @return bool|string
 */
function createPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
}


/**
 * 生成订单号
 *
 * @param string $prefix 前缀
 *
 * @return string
 * @Author 姿势就是力量
 */
function createOrderNo($prefix = '')
{
    return $prefix . date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/*
 * 验证用户密码
 * return bool
 */
function validatePassword($password)
{  // /(?!^(\d+|[a-zA-Z]+|[~!@#$%^&*?]+)$)^[\w~!@#$%^&*?]/
    return (bool)preg_match('/(?!^(\d+|[a-zA-Z]+|[~!@#$%^&*?]+)$)^[\w~!@#$%^&*?].{6,20}/', $password);
}

/*
 * 验证用户昵称  (有问题)
 * return bool
 */
function validateNickname($name)
{
    return (bool)preg_match('/^[\x80-\xff0-9a-zA-Z_\-]{4,20}$/', $name);

}

/*
 * 手机号码隐藏中间四位
 *  return string
 */
function ycPhone($str)
{
    return substr_replace($str, '****', 3, 4);
}

/*
 * 去除字符串中所有空格
 *  return string
 */
function trimall($str)
{
    return preg_replace('# #', '', $str);
}

/**
 * 时间判断
 *
 * @param $the_time
 *
 * @return false|string
 */
function time_tran($the_time)
{
    $now_time = time();
    $show_time = strtotime($the_time);
    $dur = $now_time - $show_time;
    if ($dur < 0) {
        return $the_time;
    } else {
        if ($dur < 60) {
            return $dur . '秒前';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 259200) {//3天内
                        return floor($dur / 86400) . '天前';
                    } else {
                        return date('Y-m-d', $show_time);
                    }
                }
            }
        }
    }
}

/**
 * 生成条形码
 *
 * @return string
 */
function generate_auth_code($length = 10, $filed)
{
    $code = randomkeys($length);
    if (\App\Models\User::where($filed, $code)->exists()) {
        generate_auth_code($length, $filed);
    }

    return $code;
}

/**
 * 二维数组根据某个字段排序
 *
 * @param array  $array 要排序的数组
 * @param string $keys  要排序的键字段
 * @param int    $sort  排序类型  SORT_ASC     SORT_DESC
 *
 * @return array 排序后的数组
 */
function arraySort(array $array, string $keys, $sort = SORT_DESC)
{
    $keysValue = [];
    foreach ($array as $k => $v) {
        $v = (array)$v;
        if (isset($v[$keys])) {
            $keysValue[$k] = $v[$keys];
        }
    }
    array_multisort($keysValue, $sort, $array);
    return $array;
}

function randomkeys($length)
{
    $pattern = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $pattern[mt_rand(0, 61)];    //生成php随机数
    }

    return $key;
}

/**
 * 根据两个经纬度计算距离
 *
 * @param     $lng1
 * @param     $lat1
 * @param     $lng2
 * @param     $lat2
 * @param int $unit
 * @param int $decimal
 *
 * @return float
 */
function gl_GetDistance($lng1, $lat1, $lng2, $lat2, $unit = 2, $decimal = 2)
{
    $EARTH_RADIUS = 6370.996; // 地球半径系数
    $PI = 3.1415926;
    $radLat1 = $lat1 * $PI / 180.0;
    $radLat2 = $lat2 * $PI / 180.0;

    $radLng1 = $lng1 * $PI / 180.0;
    $radLng2 = $lng2 * $PI / 180.0;

    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;

    $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $distance = $distance * $EARTH_RADIUS * 1000;

    if ($unit == 2) {
        $distance = $distance / 1000;
    }

    return round($distance, $decimal);
}


/**
 * 多维数组去重
 *
 * @param $array2D
 *
 * @return array
 */
function array_unique_fb($array2D)
{
    foreach ($array2D as $v) {
        $v = join(",", $v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
        $temp[] = $v;
    }
    $temp = array_unique($temp);//去掉重复的字符串,也就是重复的一维数组
    foreach ($temp as $k => $v) {
        $temp[$k] = explode(",", $v);//再将拆开的数组重新组装
    }

    return $temp;
}

function getConfig($key, $default = null)
{
    if (Cache::has($key)) {
        return Cache::get($key);
    }
    if ($value = \App\Models\Config::query()->where('key', $key)->value('value')) {
        Cache::forever($key, $value);
        return $value;
    }

    return $default;
}

/**
 * @param $length
 *
 * @return string
 * 随机字符串
 */
function GetRandStr($length = 8)
{
    //字符组合
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $len = strlen($str) - 1;
    $randstr = '';
    for ($i = 0; $i < $length; $i++) {
        $num = mt_rand(0, $len);
        $randstr .= $str[$num];
    }
    return $randstr;
}

function getDomain($url)
{
    $arr = parse_url($url);
    $host = $arr['host'];
    if (isset($arr['port']) && $arr['port'] != 80 && $arr['port'] != 443) {
        $host .= ':' . $arr['port'];
    }

    return $host;
}

if (!function_exists('user_admin_config')) {
    function user_admin_config($key = null, $value = null)
    {
        $session = \cache();
        if (!$config = $session->get('admin.config')) {
            $config = config('admin');
        }

        if (is_array($key)) {
            // 保存
            foreach ($key as $k => $v) {
                Arr::set($config, $k, $v);
            }

            $session->put('admin.config', $config);

            return;
        }

        if ($key === null) {
            return $config;
        }

        return Arr::get($config, $key, $value);
    }
}

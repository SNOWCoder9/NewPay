<?php
/**
 * 请求类
 */

namespace App\Services\SheenPayApiService;

use Illuminate\Http\Request;

class SheenPayRequest
{
    /**
     * 以put方式提交json到对应的接口url
     *
     * @param string         $url
     * @param SheenPayParams $paramsObj
     * @param int            $second url执行超时时间，默认30s
     *
     * @return array
     */
    public static function putJsonCurl($url, $paramsObj, $second = 30)
    {
        $url .= '?' . $paramsObj->getQueryParams();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);// 设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);// 严格校验
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));// 设置header
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');// PUT提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsObj->getBodyParams());
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return ['success' => true, 'data' => @json_decode($data, true)];
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return ['success' => false, 'message' => 'curl出错，错误码:' . $error];
        }
    }

    /**
     * 以get方式提交json到对应的接口url
     *
     * @param string         $url
     * @param SheenPayParams $paramsObj
     * @param int            $second url执行超时时间，默认30s
     *
     * @return array
     */
    public static function getJsonCurl($url, $paramsObj, $second = 30)
    {
        $url .= '?' . $paramsObj->getQueryParams();
        $userAgent = (new Request)->userAgent() ?: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);// 设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);// 严格校验
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'User-Agent: ' . $userAgent));// 设置header
        curl_setopt($ch, CURLOPT_HTTPGET, true);// GET提交方式
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return ['success' => true, 'data' => @json_decode($data, true)];
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return ['success' => false, 'message' => 'curl出错，错误码:' . $error];
        }
    }
}

<?php

namespace App\Services;

use Curl\Curl;

class CurlService
{
    /**
     * 发送POST请求
     *
     * @param string $url     请求地址
     * @param array  $params  请求参数
     * @param false  $json    是否json
     * @param array  $headers 请求头
     *
     * @return array|string
     * @throws \Exception
     */
    public function postCurl(string $url, array $params = [], bool $json = false, array $headers = [])
    {
        try {
            $curl = new Curl();
            if ($json) {
                $curl->setHeader('Content-Type', 'application/json');
                $params = @json_encode($params, JSON_UNESCAPED_UNICODE);
            }
            if ($headers) {
                $curl->setHeaders($headers);
            }
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);// 对认证证书来源的检查
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);// 从证书中检查SSL加密算法是否存在
            $curl->setTimeout(30);// 从连接完成到接收完成的超时时间，秒
            $curl->post($url, $params);
            if ($curl->error) {
                return [
                    'curl_req' => false,
                    'error_msg' => "ErrorCode：{$curl->errorCode}，ErrorMessage：{$curl->errorMessage}"
                ];
            } else {
                return $curl->rawResponse;
            }
        } catch (\Exception $e) {
            return [
                'curl_req' => false,
                'error_msg' => "Curl未知异常：{$e->getMessage()}"
            ];
        }
    }

    /**
     * 发送GET请求
     *
     * @param string $url    请求地址
     * @param array  $params 请求参数
     *
     * @return array|string
     */
    public function getCurl(string $url, array $params = [])
    {
        try {
            $curl = new Curl();
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);// 对认证证书来源的检查
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);// 从证书中检查SSL加密算法是否存在
            $curl->setTimeout(30);// 从连接完成到接收完成的超时时间，秒
            $curl->get($url, $params);
            if ($curl->error) {
                return [
                    'curl_req' => false,
                    'error_msg' => "ErrorCode：{$curl->errorCode}，ErrorMessage：{$curl->errorMessage}"
                ];
            } else {
                return $curl->rawResponse;
            }
        } catch (\Exception $e) {
            return [
                'curl_req' => false,
                'error_msg' => "Curl未知异常：{$e->getMessage()}"
            ];
        }
    }
}

<?php

namespace App\Services\SheenPayApiService;

class SheenPayApi
{
    protected static $instance;
    protected static $sheenConfig;

    public static function getInstance($sheenConfig)
    {
        self::$sheenConfig = $sheenConfig;

        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 创建QrCode订单
     *
     * @param SheenPayParams $paramsObj
     */
    public function createQrCodeOrder($paramsObj)
    {
        $partnerCode = self::$sheenConfig['partner_code'];
        $orderId = $paramsObj->getOrderId();
        $url = self::$sheenConfig['request_url'] . "api/v1.0/gateway/partners/{$partnerCode}/orders/{$orderId}";

        $paramsObj->setPartnerCode($partnerCode);
        $paramsObj->setTime(SheenPayUtil::getMillisecond());
        $paramsObj->setNonceStr(SheenPayUtil::getNonceStr());
        $paramsObj->setSign(self::$sheenConfig['credential_code']);

        $result = SheenPayRequest::putJsonCurl($url, $paramsObj, 10);
        if (false === $result['success']) return $result;
        // 真实的响应数据
        $response = $result['data'];
        if ($response['return_code'] != 'SUCCESS') return ['success' => false, 'message' => $response['return_msg']];
        if ($response['result_code'] != 'SUCCESS') return ['success' => false, 'message' => '该订单已存在'];
        return ['success' => true, 'data' => $response];
    }

    /**
     * QRCode支付跳转页
     *
     * @param SheenPayParams $paramsObj
     */
    public function qrCodePage($paramsObj)
    {
        $partnerCode = self::$sheenConfig['partner_code'];
        $orderId = $paramsObj->getOrderId();
        $url = self::$sheenConfig['request_url'] . "api/v1.0/gateway/partners/{$partnerCode}/orders/{$orderId}/pay";

        $paramsObj->setRedirect(urlencode('https://baidu.com'));
        $paramsObj->setPartnerCode($partnerCode);
        $paramsObj->setTime(SheenPayUtil::getMillisecond());
        $paramsObj->setNonceStr(SheenPayUtil::getNonceStr());
        $paramsObj->setSign(self::$sheenConfig['credential_code']);

        SheenPayRequest::getJsonCurl($url, $paramsObj, 10);
    }

    /**
     * 获取当前汇率
     *
     * @param SheenPayParams $paramsObj
     */
    public function channelExchangeRate($paramsObj)
    {
        $partnerCode = self::$sheenConfig['partner_code'];
        $url = self::$sheenConfig['request_url'] . "api/v1.0/gateway/partners/{$partnerCode}/channel_exchange_rate";

        $paramsObj->setPartnerCode($partnerCode);
        $paramsObj->setTime(SheenPayUtil::getMillisecond());
        $paramsObj->setNonceStr(SheenPayUtil::getNonceStr());
        $paramsObj->setSign(self::$sheenConfig['credential_code']);

        $result = SheenPayRequest::getJsonCurl($url, $paramsObj, 10);
        if (false === $result['success']) return $result;
        // 真实的响应数据
        $response = $result['data'];
        if ($response['return_code'] != 'SUCCESS') return ['success' => false, 'message' => $response['return_msg']];
        return ['success' => true, 'data' => $response];
    }
}

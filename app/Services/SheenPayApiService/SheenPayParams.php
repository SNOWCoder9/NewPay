<?php
/**
 * 生成参数工具类
 */

namespace App\Services\SheenPayApiService;

class SheenPayParams
{
    private $pathParams = [];

    private $queryParams = [];

    private $bodyParams = [];

    /**
     * 商户编码
     *
     * @param $partnerCode
     */
    public function setPartnerCode($partnerCode)
    {
        $this->pathParams['partner_code'] = $partnerCode;
    }

    public function getPartnerCode()
    {
        return $this->pathParams['partner_code'];
    }

    /**
     * 商户支付订单号，要求同一商户唯一
     *
     * @param $orderId
     */
    public function setOrderId($orderId)
    {
        $this->pathParams['order_id'] = $orderId;
    }

    public function getOrderId()
    {
        return $this->pathParams['order_id'];
    }

    /**
     * 订单标题（最大长度128字符，超出自动截取）
     *
     * @param $description
     */
    public function setDescription($description)
    {
        $this->bodyParams['description'] = $description;
    }

    public function getDescription()
    {
        return $this->bodyParams['description'];
    }

    /**
     * 金额，单位为货币最小单位，例如使用100表示1.00 HKD
     *
     * @param $price
     */
    public function setPrice($price)
    {
        $this->bodyParams['price'] = $price;
    }

    public function getPrice()
    {
        return $this->bodyParams['price'];
    }

    /**
     * 币种代码
     * 允许值: HKD, CNY
     *
     * @param $currency
     */
    public function setCurrency($currency = 'HKD')
    {
        $this->bodyParams['currency'] = $currency;
    }

    public function getCurrency()
    {
        return $this->bodyParams['currency'];
    }

    /**
     * 支付渠道，大小写敏感
     * 允许值: Alipay, Wechat
     *
     * @param $channel
     */
    public function setChannel($channel)
    {
        $this->bodyParams['channel'] = $channel;
    }

    public function getChannel()
    {
        return $this->bodyParams['channel'];
    }

    /**
     * 支付通知url，详见支付通知api，不填则不会推送支付通知
     *
     * @param $notifyUrl
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->bodyParams['notify_url'] = $notifyUrl;
    }

    public function getNotifyUrl()
    {
        return $this->bodyParams['notify_url'];
    }

    /**
     * 操作人员标识
     *
     * @param $operator
     */
    public function setOperator($operator)
    {
        $this->bodyParams['operator'] = $operator;
    }

    public function getOperator()
    {
        return $this->bodyParams['operator'];
    }

    /**
     * 设置支付成功后跳转页面
     *
     * @param $value
     */
    public function setRedirect($value)
    {
        $this->queryParams['redirect'] = $value;
    }

    /**
     * 获取支付成功后跳转页面
     *
     * @return mixed
     */
    public function getRedirect()
    {
        return $this->queryParams['redirect'];
    }

    /**
     * UTC毫秒时间戳
     *
     * @param $time
     */
    public function setTime($time)
    {
        $this->queryParams['time'] = $time;
    }

    public function getTime()
    {
        return $this->queryParams['time'];
    }

    /**
     * 随机字符串
     *
     * @param $nonceStr
     */
    public function setNonceStr($nonceStr)
    {
        $this->queryParams['nonce_str'] = $nonceStr;
    }

    public function getNonceStr()
    {
        return $this->queryParams['nonce_str'];
    }

    /**
     * 签名
     *
     * @param $credentialCode
     *
     * @return string
     */
    public function setSign($credentialCode)
    {
        $sign = $this->makeSign($credentialCode);
        $this->queryParams['sign'] = $sign;
        return $sign;
    }

    public function getSign()
    {
        return $this->queryParams['sign'];
    }

    /**
     * 获取查询字符串参数
     *
     * @return string
     */
    public function getQueryParams()
    {
        $buff = "";
        foreach ($this->queryParams as $k => $v) {
            if ($v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        return trim($buff, "&");
    }

    /**
     * 获取 json body 参数
     *
     * @return false|string
     */
    public function getBodyParams()
    {
        return @json_encode($this->bodyParams, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取签名字符串
     *
     * @param $credentialCode
     *
     * @return string
     */
    public function getSignParams($credentialCode)
    {
        return $this->getPartnerCode() . '&' . $this->getTime() . '&' . $this->getNonceStr() . "&" . $credentialCode;
    }

    /**
     * 生成签名
     *
     * @param $credentialCode
     *
     * @return string
     */
    public function makeSign($credentialCode)
    {
        // 签名步骤一：构造签名参数
        $string = $this->getSignParams($credentialCode);
        // 签名步骤三：SHA256加密
        $string = hash('sha256', utf8_encode($string));
        // 签名步骤四：所有字符转为小写
        return strtolower($string);
    }
}

<?php

namespace App\Services;

use App\Enum\OrderEnum;
use App\Models\Contract;
use App\Models\Order;

class OrderService
{
    /**
     * 更新净额
     *
     * @param Order $order 订单
     *
     * @return array
     */
    public static function updateFinalAmount(Order $order)
    {
        if ($order->withdraw !== 0) return returnFailQian('该订单已结算不允许修改净额');
        if ((int)($order->final_amount * 100) !== 0) return returnFailQian('该订单已存在净额');
        if (!in_array($order->status, [OrderEnum::UNPAID, OrderEnum::EXPIRED])) return returnFailQian('当前状态异常不允许修改净额');
        $contract = Contract::query()->where(['user_id' => $order->user_id, 'token' => $order->token])->first();
        if (!$contract) return returnFailQian('签约记录不存在');
        $order->final_amount = (float)sprintf('%.2f', $order->goods_price * ((100 - $contract->rate) / 100));
        $order->save();
        return returnSuccessQian();
    }

    /**
     * 验证签名
     *
     * @param $data
     * @param $signature
     * @param $appSecret
     *
     * @return bool
     */
    public function verifySign($data, $signature, $appSecret)
    {
        $mySign = $this->sign($this->prepareSign($data), $appSecret);

        return $mySign === $signature;
    }

    /**
     *  准备签名
     *
     * @param $data
     *
     * @return string
     */
    public function prepareSign($data)
    {
        unset($data['sign']);
        ksort($data);
        return http_build_query($data);
    }

    /**
     *  生成签名
     *
     * @param $data
     * @param $appSecret
     *
     * @return    string
     */
    public function sign($data, $appSecret): string
    {
        return strtolower(md5($data . $appSecret));
    }
}

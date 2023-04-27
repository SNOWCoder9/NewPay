<?php
/**
 *
 */

namespace App\Services;

use App\Enum\OrderEnum;
use App\Jobs\TelegramPush;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class OrderNotifyService
{
    /**
     * 发送http回调通知
     *
     * @param Order $order
     * @param bool  $isSendTgNotify
     *
     * @return false|void
     */
    public static function sendHttpNotify(Order $order, bool $isSendTgNotify = true)
    {
        if (!in_array($order->status, [OrderEnum::SUCCESS, OrderEnum::NOTICE, OrderEnum::NOTICEFAIL])) {
            return false;
        }
        $user = User::query()->find($order->user_id);
        $data = [
            'trade_no' => $order->transaction_id,
            'out_trade_no' => $order->out_trade_no,
            'price' => $order->goods_price,
        ];
        // 生成签名
        $orderService = new OrderService();
        $signParams = $orderService->prepareSign($data);
        $data['sign'] = $orderService->sign($signParams, $user->app_secret);
        try {
            // 发送http回调通知
            $response = Http::retry(3, 3000)->post($order->notify_url, $data);
            if ($response->successful()) {
                $order->status = OrderEnum::NOTICE;
                $sendStatus = 'success';
            } else {
                $order->status = OrderEnum::NOTICEFAIL;
                $sendStatus = 'failed';
            }
        } catch (\Exception $e) {
            $order->status = OrderEnum::NOTICEFAIL;
            $sendStatus = 'failed';
        }
        $order->save();

        // 发送TG通知
        if (true === $isSendTgNotify) {
            $adminTgId = getConfig('telegram_admin_id');
            if ($adminTgId && 'true' == getConfig('telegram_admin_push', 'false')) {
                self::sendTelegramNotify($order, $adminTgId, $sendStatus);
            }
            if ($user->telegram_id) {
                self::sendTelegramNotify($order, $user->telegram_id, $sendStatus);
            }
        }
    }

    /**
     * 发送tg通知
     *
     * @param $order
     * @param $telegramId
     * @param $sendStatus
     */
    public static function sendTelegramNotify($order, $telegramId, $sendStatus)
    {
        // 发送tg通知
        $text = '*💰新订单通知*' . PHP_EOL;
        $text .= '------------------------------' . PHP_EOL;
        $text .= '商家订单号: `' . $order->out_trade_no . '`' . PHP_EOL;
        $text .= '商品总价: ' . $order->goods_price . 'CNY' . PHP_EOL;
        $text .= '收入净额: ' . $order->final_amount . 'CNY' . PHP_EOL;
        $text .= '支付场景: ' . ($order->platform === 'web' ? '电脑' : '手机') . PHP_EOL;
        if (isset(parse_url($order->return_url)['host'])) {
            $text .= '网站地址: ' . parse_url($order->return_url)['host'] . PHP_EOL;
        }
        $keyboard = [];
        if ($order->token === 'alipay') {
            $text .= '支付方式: 支付宝' . PHP_EOL;
        } elseif ($order->token === 'alipay_hk') {
            $text .= '支付方式: 支付宝（香港）' . PHP_EOL;
        } elseif ($order->token === 'wechat') {
            $text .= '支付方式: 微信支付' . PHP_EOL;
        } elseif ($order->token === 'USDT') {
            $text .= '支付USDT: ' . $order->token_price . PHP_EOL;
            $text .= '链上哈希: `' . $order->transaction_id . '`' . PHP_EOL;
            $keyboard = [
                [
                    [
                        'text' => '查看链上交易',
                        'url' => "https://tronscan.io/#/transaction/" . $order->transaction_id
                    ]
                ]
            ];
        } elseif ($order->token === 'ETH') {
            $text .= '支付ETH: ' . $order->token_price . PHP_EOL;
            $text .= '链上哈希: `' . $order->transaction_id . '`' . PHP_EOL;
            $keyboard = [
                [
                    [
                        'text' => '查看链上交易',
                        'url' => "https://etherscan.io/tx/" . $order->transaction_id
                    ]
                ]
            ];
        } elseif ($order->token === 'BTC') {
            $text .= '支付BTC: ' . $order->token_price . PHP_EOL;
            $text .= '链上哈希: `' . $order->transaction_id . '`' . PHP_EOL;
            $keyboard = [
                [
                    [
                        'text' => '查看链上交易',
                        'url' => "https://www.blockchain.com/btc/tx/" . $order->transaction_id
                    ]
                ]
            ];
        }
        $text .= '创建时间: ' . $order->created_at . PHP_EOL;
        $text .= '回调时间: ' . $order->notified_at . PHP_EOL;
        if ($sendStatus === 'success') {
            $text .= '状态: *通知成功*';
        } else {
            $text .= '状态: *通知失败*';
        }

        TelegramPush::dispatch([
            'text' => $text,
            'keyboard' => $keyboard,
            'chat_id' => $telegramId
        ]);
    }
}

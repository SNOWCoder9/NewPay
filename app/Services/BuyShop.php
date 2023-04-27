<?php

namespace App\Services;

use App\Enum\CycleEnum;
use App\Models\Contract;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class BuyShop
{
    /**
     * @param $trade_no
     * @param $callback_no
     * @throws \App\Exceptions\BobException
     */
    public static function postPayment($trade_no, $callback_no = null)
    {
        $order = Cache::tags('buy_shop')->get($trade_no, '');
        if (!$order) {
            return_bob('交易已完成', 1, 200);
        }
        $contract = new Contract();
        $contract->rate = $order->rate;
        $contract->user_id = $order->user_id;
        $contract->token = $order->token;
        $contract->type = $order->type;
        $contract->cycle = $order->cycle;
        $contract->expired_at = Carbon::now()->addDays(CycleEnum::cycleDay[$order->cycle]);
        $contract->address = $order->address;
        $contract->status = 1;
        $contract->save();
    }
}

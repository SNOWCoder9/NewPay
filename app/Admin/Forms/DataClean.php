<?php

namespace App\Admin\Forms;

use App\Enum\OrderEnum;
use App\Enum\SettleEnum;
use App\Models\Order;
use App\Models\Settlement;
use Dcat\Admin\Widgets\Form;
use Illuminate\Support\Facades\DB;

class DataClean extends Form
{
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        $orderDay = (int)$input['order_day'];
        $orderStatus = (int)$input['order_status'];
        if ($orderDay > 0) {
            $dateTime = date("Y-m-d 00:00:00", strtotime("-{$orderDay} day"));
            $order = new Order();
            $order = $order->where('created_at', '<', $dateTime);
            switch ($orderStatus) {
                case 1:// 支付成功且已结算
                    $order = $order->whereIn('status', [OrderEnum::SUCCESS, OrderEnum::NOTICE, OrderEnum::NOTICEFAIL])
                        ->where('withdraw', '=', SettleEnum::SUCCESS);
                    break;
                case 2:// 支付成功但未结算
                    $order = $order->whereIn('status', [OrderEnum::SUCCESS, OrderEnum::NOTICE, OrderEnum::NOTICEFAIL])
                        ->where('withdraw', '=', SettleEnum::NOT);
                    break;
                default:
                case 3:// 未支付
                    $order = $order->whereIn('status', [OrderEnum::REFUND, OrderEnum::UNPAID, OrderEnum::EXPIRED]);
                    break;
            }
            $order->delete();
        }

        $settleDay = (int)$input['settle_day'];
        $settleStatus = (int)$input['settle_status'];
        if ($settleDay > 0) {
            $dateTime = date("Y-m-d 00:00:00", strtotime("-{$settleDay} day"));
            $settlement = new Settlement();
            $settlement = $settlement->where('created_at', '<', $dateTime);
            switch ($settleStatus) {
                default:
                case 0:// 未结算
                    $settlement = $settlement->where('status', '=', SettleEnum::NOT);
                    break;
                case 1:// 已结算
                    $settlement = $settlement->where('status', '=', SettleEnum::SUCCESS);
                    break;
            }
            $settlement->delete();
        }

        return $this
            ->response()
            ->success('清理成功');
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->number('order_day', '订单记录')
            ->min(0)
            ->default(0)
            ->help('以创建时间作为删除字段，删除{n}天前的记录，0表示不进行操作');
        $this->select('order_status', '订单状态')
            ->options([
                1 => '支付成功且已结算',
                2 => '支付成功但未结算',
                3 => '未支付',
            ])
            ->default(1)
            ->help('请慎重选择');

        $this->divider();

        $this->number('settle_day', '结算记录')
            ->min(0)
            ->default(0)
            ->help('以创建时间作为删除字段，删除{n}天前的记录，0表示不进行操作');
        $this->select('settle_status', '结算状态')
            ->options(SettleEnum::text)
            ->default(1)
            ->help('请慎重选择');
    }
}

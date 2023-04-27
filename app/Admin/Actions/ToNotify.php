<?php

namespace App\Admin\Actions;

use App\Jobs\OrderNotify;
use App\Models\Order;
use Dcat\Admin\Grid\RowAction;
use Illuminate\Http\Request;

class ToNotify extends RowAction
{
    /**
     * 标题
     *
     * @return string
     */
    public function title()
    {
        return '<i class="feather icon-copy"></i> 点击通知（异步）';
    }

    /**
     * 处理请求
     *
     * @param Request $request
     *
     * @return \Dcat\Admin\Actions\Response
     */
    public function handle(Request $request)
    {
        // 获取当前行ID
        $id = $this->getKey();
        $order = Order::query()->find($id);

        // 回调通知
        OrderNotify::dispatch($order);

        return $this->response()->success("该通知为异步操作，请等待...");
    }
}

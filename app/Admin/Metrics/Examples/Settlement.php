<?php

namespace App\Admin\Metrics\Examples;

use App\Models\Order;
use Carbon\Carbon;
use Dcat\Admin\Widgets\Metrics\Line;
use Illuminate\Http\Request;

class Settlement extends Line
{
    /**
     * 初始化卡片内容
     *
     * @return void
     */
    protected function init()
    {
        parent::init();

        $this->title('待结算');
    }

    /**
     * 处理请求
     *
     * @param Request $request
     *
     * @return mixed|void
     */
    public function handle(Request $request)
    {
        $settle = new \App\Models\Settlement();
        $money = $settle->where('status', 0)->sum('money');
        $usdt = $settle->where('status', 0)->sum('usdt');
        $order = $settle->where('status', 0)->count();
        $this->withContent($money, $usdt, $order);
    }

    /**
     * 设置图表数据.
     *
     * @param array $data
     *
     * @return $this
     */
    public function withChart(array $data)
    {
        return $this->chart([
            'series' => [
                [
                    'name' => $this->title,
                    'data' => $data,
                ],
            ],
        ]);
    }

    /**
     * 设置卡片内容.
     *
     * @param string $content
     *
     * @return $this
     */
    public function withContent($total, $usdt, $order)
    {
        return $this->content(
            <<<HTML
<div class="d-flex justify-content-between align-items-center mt-1" style="margin-bottom: 2px">
    <h2 class="ml-1 font-lg-1">￥{$total}</h2>
    <span class="mb-0 mr-1 text-80">待结算金额</span>
</div>
<div class="ml-1 mt-1 font-weight-bold text-80">
    <span>单数：{$order} 单 </span><span>待结算USDT：{$usdt} </span>
</div>
HTML
        );
    }

    public function getWeekCount()
    {
        // 本周数据
        $this_week = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
        $order_count = Order::whereBetween('created_at', $this_week)->count();
        $order_success_count = Order::success()->whereBetween('created_at', $this_week)->count();

        return [$order_count, $order_success_count];
    }

    public function getTodayCount()
    {
        // 今日数据

        $order_count = Order::whereDay('created_at', Carbon::today())->count();
        $order_success_count = Order::success()->whereDay('created_at', Carbon::today())->count();

        return [$order_count, $order_success_count];
    }

    public function getMonthCount()
    {
        // 本月数据
        $order_count = Order::whereMonth('created_at', Carbon::now()->month)->count();
        $order_success_count = Order::success()->whereMonth('created_at', Carbon::now()->month)->count();

        return [$order_count, $order_success_count];
    }

    public function getLastMonthCount()
    {
        // 上个月数据
        $order_count = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        $order_success_count = Order::success()->whereMonth('created_at', Carbon::now()->subMonth()->month)->count();

        return [$order_count, $order_success_count];
    }
}

<?php

namespace App\Admin\Metrics\Examples;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Dcat\Admin\Widgets\Metrics\Line;
use Illuminate\Http\Request;

class Orders extends Line
{
    /**
     * 初始化卡片内容
     *
     * @return void
     */
    protected function init()
    {
        parent::init();

        $this->title('订单数');
        $this->dropdown([
            '1' => '今日',
            '5' => '昨日',
            '2' => '本周',
            '3' => '本月',
            '4' => '上月',
        ]);
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
        switch ($request->get('option')) {
            case '5':
                $this->withContent($this->getLastDayCount());
                break;
            case '4':
                $this->withContent($this->getLastMonthCount());
                break;
            case '3':
                $this->withContent($this->getMonthCount());
                break;
            case '2':
                $this->withContent($this->getWeekCount());
                break;
            case '1':
                $this->withContent($this->getTodayCount());
                break;
            default:
                // 卡片内容
                $this->withContent($this->getTodayCount());
        }
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
    public function withContent($total)
    {
        return $this->content(
            <<<HTML
<div class="d-flex justify-content-between align-items-center mt-1" style="margin-bottom: 2px">
    <h2 class="ml-1 font-lg-1">{$total[0]} 单</h2>
    <span class="mb-0 mr-1 text-80">总订单</span>
</div>
<div class="ml-1 mt-1 font-weight-bold text-80">
    支付成功：{$total[1]} 单
</div>
HTML
        );
    }

    public function getWeekCount()
    {
        // 本周数据
        $order_count = Order::query()->dateQuery('week')->count();
        $order_success_count = Order::success()->dateQuery('week')->count();

        return [$order_count, $order_success_count];
    }

    public function getTodayCount()
    {
        // 今日数据
        $order_count = Order::query()->dateQuery('today')->count();
        $order_success_count = Order::success()->dateQuery('today')->count();

        return [$order_count, $order_success_count];
    }

    public function getMonthCount()
    {
        // 本月数据
        $order_count = Order::query()->dateQuery('month')->count();
        $order_success_count = Order::success()->dateQuery('month')->count();

        return [$order_count, $order_success_count];
    }

    public function getLastMonthCount()
    {
        // 上个月数据
        $order_count = Order::query()->dateQuery('sub_month')->count();
        $order_success_count = Order::success()->dateQuery('sub_month')->count();

        return [$order_count, $order_success_count];
    }

    public function getLastDayCount()
    {
        // 昨天数据
        $order_count = Order::query()->dateQuery('sub_day')->count();
        $order_success_count = Order::query()->success()->dateQuery('sub_day')->count();

        return [$order_count, $order_success_count];
    }
}

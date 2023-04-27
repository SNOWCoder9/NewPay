<?php

namespace App\Admin\Metrics\Examples;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Dcat\Admin\Widgets\Metrics\Line;
use Illuminate\Http\Request;

class Flow extends Line
{
    /**
     * 初始化卡片内容
     *
     * @return void
     */
    protected function init()
    {
        parent::init();

        $this->title('流水');
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
        $goods_price_total = $total['goods_price_total'] ?? 0;
        $final_amount_total = $total['final_amount_total'] ?? 0;
        return $this->content(
            <<<HTML
<div class="d-flex justify-content-between align-items-center mt-1" style="margin-bottom: 2px">
    <h2 class="ml-1 font-lg-1">￥{$goods_price_total}</h2>
    <span class="mb-0 mr-1 text-80">总金额</span>
</div>
<div class="ml-1 mt-1 font-weight-bold text-80">
    净额：￥{$final_amount_total}
</div>
HTML
        );
    }

    public function getWeekCount()
    {
        // 本周数据
        return Order::selectRaw("sum(goods_price) as goods_price_total, sum(final_amount) as final_amount_total")
            ->success()
            ->dateQuery('week')
            ->first()->toArray();
    }

    public function getTodayCount()
    {
        // 今日数据
        return Order::selectRaw("sum(goods_price) as goods_price_total, sum(final_amount) as final_amount_total")
            ->success()
            ->dateQuery('today')
            ->first()->toArray();
    }

    public function getMonthCount()
    {
        // 本月数据
        return Order::selectRaw("sum(goods_price) as goods_price_total, sum(final_amount) as final_amount_total")
            ->success()
            ->dateQuery('month')
            ->first()->toArray();
    }

    public function getLastMonthCount()
    {
        // 上个月数据
        return Order::selectRaw("sum(goods_price) as goods_price_total, sum(final_amount) as final_amount_total")
            ->success()
            ->dateQuery('sub_month')
            ->first()->toArray();
    }

    public function getLastDayCount()
    {
        // 昨天数据
        return Order::selectRaw("sum(goods_price) as goods_price_total, sum(final_amount) as final_amount_total")
            ->success()
            ->dateQuery('sub_day')
            ->first()->toArray();
    }
}

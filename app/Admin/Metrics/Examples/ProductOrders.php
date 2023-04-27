<?php

namespace App\Admin\Metrics\Examples;

use App\Models\Order;
use App\Enum\OrderEnum;
use Dcat\Admin\Widgets\Metrics\Round;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProductOrders extends Round
{
    /**
     * 初始化卡片内容
     */
    protected function init()
    {
        parent::init();

        $this->title('订单数量');
        $this->chartLabels(['已支付', '待付款', '已关闭']);
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
                $data = $this->getLastDayCount();
                break;
            case '4':
                $data = $this->getLastMonthCount();
                break;
            case '3':
                $data = $this->getMonthCount();
                break;
            case '2':
                $data = $this->getWeekCount();
                break;
            case '1':
                $data = $this->getTodayCount();
                break;
            default:
                $data = $this->getTodayCount();
        }
        // 卡片内容
        $this->withContent($data[0], $data[1], $data[2], $data[3]);
        $total = array_sum($data);
        // 图表数据
        $this->withChart([
            $total > 0 ? ($data[0] / $total) * 100 : 0,
            $total > 0 ? ($data[1] / $total) * 100 : 0,
            $total > 0 ? ($data[2] / $total) * 100 : 0,
            $total > 0 ? ($data[3] / $total) * 100 : 0,
        ]);

        // 总数
        $this->chartTotal('总数', $total);
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
            'series' => $data,
        ]);
    }

    /**
     * 卡片内容.
     *
     * @param int $finished
     * @param int $pending
     * @param int $rejected
     *
     * @return $this
     */
    public function withContent($finished, $pending, $rejected, $returned)
    {
        return $this->content(
            <<<HTML
<div class="col-12 d-flex flex-column flex-wrap text-center" style="max-width: 220px">
    <div class="chart-info d-flex justify-content-between mb-1 mt-2" >
          <div class="series-info d-flex align-items-center">
              <i class="fa fa-circle-o text-bold-700 text-primary"></i>
              <span class="text-bold-600 ml-50">已支付</span>
          </div>
          <div class="product-result">
              <span>{$finished}</span>
          </div>
    </div>

    <div class="chart-info d-flex justify-content-between mb-1">
          <div class="series-info d-flex align-items-center">
              <i class="fa fa-circle-o text-bold-700 text-warning"></i>
              <span class="text-bold-600 ml-50">待付款</span>
          </div>
          <div class="product-result">
              <span>{$pending}</span>
          </div>
    </div>

     <div class="chart-info d-flex justify-content-between mb-1">
          <div class="series-info d-flex align-items-center">
              <i class="fa fa-circle-o text-bold-700 text-danger"></i>
              <span class="text-bold-600 ml-50">已关闭</span>
          </div>
          <div class="product-result">
              <span>{$rejected}</span>
          </div>
    </div>
     <div class="chart-info d-flex justify-content-between mb-1">
          <div class="series-info d-flex align-items-center">
              <i class="fa fa-circle-o text-bold-700 text-light"></i>
              <span class="text-bold-600 ml-50">已退款</span>
          </div>
          <div class="product-result">
              <span>{$returned}</span>
          </div>
    </div>
</div>
HTML
        );
    }

    public function getWeekCount()
    {
        // 已支付
        $finished = Order::query()
            ->dateQuery('week')
            ->success()
            ->count('id');
        // 待付款
        $pending = Order::query()
            ->dateQuery('week')
            ->where('status', OrderEnum::UNPAID)
            ->count('id');
        // 已关闭
        $rejected = Order::query()
            ->dateQuery('week')
            ->where('status', OrderEnum::EXPIRED)
            ->count('id');
        // 已退款
        $returned = Order::query()
            ->dateQuery('week')
            ->where('status', OrderEnum::REFUND)
            ->count('id');

        return [$finished, $pending, $rejected, $returned];
    }

    public function getTodayCount()
    {
        // 今日数据
        // 已支付
        $finished = Order::query()
            ->dateQuery('today')
            ->success()
            ->count('id');
        // 待付款
        $pending = Order::query()
            ->dateQuery('today')
            ->where('status', OrderEnum::UNPAID)
            ->count('id');
        // 已关闭
        $rejected = Order::query()
            ->dateQuery('today')
            ->where('status', OrderEnum::EXPIRED)
            ->count('id');
        // 已退款
        $returned = Order::query()
            ->dateQuery('today')
            ->where('status', OrderEnum::REFUND)
            ->count('id');

        return [$finished, $pending, $rejected, $returned];
    }

    public function getMonthCount()
    {
        // 本月数据
        $date = Carbon::now()->month;
        // 已支付
        $finished = Order::query()
                ->dateQuery('month')
            ->success()
            ->count('id') ?? 0;
        // 待付款
        $pending = Order::query()
                ->dateQuery('month')
            ->where('status', OrderEnum::UNPAID)
            ->count('id') ?? 0;
        // 待付款
        $rejected = Order::query()
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', '>=', $date)
            ->where('status', OrderEnum::EXPIRED)
            ->count('id') ?? 0;
        // 已退款
        $returned = Order::query()
            ->whereMonth('created_at', '>=', $date)
            ->where('status', OrderEnum::REFUND)
            ->count('id');

        return [$finished, $pending, $rejected, $returned];
    }

    public function getLastMonthCount()
    {
        // 上个月数据
        $date = Carbon::now()->subMonth()->month;
        // 已支付
        $finished = Order::query()
                ->dateQuery('sub_month')
                ->success()
                ->count('id') ?? 0;
        // 待付款
        $pending = Order::query()
                ->dateQuery('sub_month')
            ->where('status', OrderEnum::UNPAID)
            ->count('id') ?? 0;
        // 待付款
        $rejected = Order::query()
                ->dateQuery('sub_month')
            ->where('status', OrderEnum::EXPIRED)
            ->count('id') ?? 0;
        // 已退款
        $returned = Order::query()
            ->dateQuery('sub_month')
            ->where('status', OrderEnum::REFUND)
            ->count('id');

        return [$finished, $pending, $rejected, $returned];
    }

    public function getLastDayCount()
    {
        // 已支付
        $finished = Order::query()
                ->dateQuery('sub_day')
                ->success()
                ->count('id') ?? 0;
        // 待付款
        $pending = Order::query()
                ->dateQuery('sub_day')
                ->where('status', OrderEnum::UNPAID)
                ->count('id') ?? 0;
        // 待付款
        $rejected = Order::query()
                ->dateQuery('sub_day')
                ->where('status', OrderEnum::EXPIRED)
                ->count('id') ?? 0;
        // 已退款
        $returned = Order::query()
            ->dateQuery('sub_day')
            ->where('status', OrderEnum::REFUND)
            ->count('id');

        return [$finished, $pending, $rejected, $returned];
    }
}

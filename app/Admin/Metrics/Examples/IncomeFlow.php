<?php

namespace App\Admin\Metrics\Examples;

use App\Models\Order;
use Dcat\Admin\Widgets\Metrics\Bar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class IncomeFlow extends Bar
{
    /**
     * 初始化卡片内容
     */
    protected function init()
    {
        parent::init();

        // 卡片内容宽度
        $this->contentWidth(3, 8);
        // 标题
        $this->title('收入流水');
        // 设置下拉选项
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
        $this->withContent($data[0]);
        // 图表数据
        $this->withChart([
            [
                'name' => '收入',
                'data' => $data[1],
            ],
        ]);
    }

    /**
     * 设置图表数据.
     *
     * @param array $data
     *
     * @return $this
     */
    public function withChart(array $data): IncomeFlow
    {
        return $this->chart([
            'series' => $data
        ]);
    }

    /**
     * 设置卡片内容.
     *
     * @param string $title
     *
     * @return $this
     */
    public function withContent(string $title): IncomeFlow
    {
        // 根据选项显示
        $label = strtolower(
            $this->dropdown[request()->option] ?? '今日'
        );

        $minHeight = '183px';

        return $this->content(
            <<<HTML
<div class="d-flex p-1 flex-column justify-content-center" style="padding-top: 0;width: 100%;height: 100%;min-height: {$minHeight}">
    <div class="text-left">
        <h2 class="font-medium-1 mt-2 mb-0">￥{$title}</h2>
        <h5 class="font-medium-2" style="margin-top: 10px;">
            <span class="text-primary">{$label}收入</span>
        </h5>
    </div>
</div>
HTML
        );
    }

    public function getWeekCount()
    {
        // 本周数据
        $data = Order::query()
            ->dateQuery('week')
            ->success()
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('SUM(goods_price) as total_amount')
            ])
            ->toArray();

        $amount = Order::query()
            ->success()
            ->dateQuery('week')
            ->sum('goods_price');

        return [$amount, array_column($data, 'total_amount')];
    }

    public function getTodayCount()
    {
        // 今日数据
        $data = Order::query()
            ->dateQuery('today')
            ->success()
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('SUM(goods_price) as total_amount')
            ])
            ->toArray();
        // 今日数据
        $amount = Order::query()
            ->success()
            ->dateQuery('today')
            ->sum('goods_price');

        return [$amount, array_column($data, 'total_amount')];
    }

    public function getMonthCount()
    {
        $date = Carbon::now()->month;
        // 本月数据
        $data = Order::query()
            ->dateQuery('month')
            ->success()
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('SUM(goods_price) as total_amount')
            ])
            ->toArray();

        $amount = Order::query()
            ->dateQuery('month')
            ->success()
            ->sum('goods_price');

        return [$amount, array_column($data, 'total_amount')];
    }

    public function getLastMonthCount()
    {
        // 上月数据
        $data = Order::query()
            ->dateQuery('sub_month')
            ->success()
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('SUM(goods_price) as total_amount')
            ])
            ->toArray();

        $amount = Order::query()
            ->dateQuery('sub_month')
            ->success()
            ->sum('goods_price');

        return [$amount, array_column($data, 'total_amount')];
    }

    public function getLastDayCount()
    {
        // 昨天数据
        $data = Order::query()
            ->dateQuery('sub_day')
            ->success()
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('SUM(goods_price) as total_amount')
            ])
            ->toArray();

        $amount = Order::query()
            ->dateQuery('sub_day')
            ->success()
            ->sum('goods_price');

        return [$amount, array_column($data, 'total_amount')];
    }
}

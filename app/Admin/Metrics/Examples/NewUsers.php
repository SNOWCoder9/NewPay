<?php

namespace App\Admin\Metrics\Examples;

use App\Models\User;
use Carbon\Carbon;
use Dcat\Admin\Widgets\Metrics\Line;
use Illuminate\Http\Request;

class NewUsers extends Line
{
    /**
     * 初始化卡片内容
     *
     * @return void
     */
    protected function init()
    {
        parent::init();

        $this->title('用户');
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
        $total = User::query()->count();
        switch ($request->get('option')) {
            case '5':
                $this->withContent($this->getLastDayCount(), $total);
                break;
            case '4':
                $this->withContent($this->getLastMonthCount(), $total);
                break;
            case '3':
                $this->withContent($this->getMonthCount(), $total);
                break;
            case '2':
                $this->withContent($this->getWeekCount(), $total);
                break;
            case '1':
                $this->withContent($this->getTodayCount(), $total);
                break;
            default:
                // 卡片内容
                $this->withContent($this->getTodayCount(), $total);
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
    public function withContent($content, $total)
    {
        return $this->content(
            <<<HTML
<div class="d-flex justify-content-between align-items-center mt-1" style="margin-bottom: 2px">
    <h2 class="ml-1 font-lg-1">{$total} 人</h2>
    <span class="mb-0 mr-1 text-80">总人数</span>
</div>
<div class="ml-1 mt-1 font-weight-bold text-80">
    新增：{$content} 人
</div>
HTML
        );
    }

    public function getWeekCount()
    {
        // 本周数据
        $this_week = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];

        return User::query()->whereBetween('created_at', $this_week)->count();
    }

    public function getTodayCount()
    {
        // 今日数据
        return User::query()->whereDate('created_at', Carbon::today())->count();
    }

    public function getMonthCount()
    {
        // 本月数据
        return User::query()->whereMonth('created_at', Carbon::now()->month)->count();
    }

    public function getLastMonthCount()
    {
        // 上个月数据
        return User::query()->whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
    }

    public function getLastDayCount()
    {
        // 昨天数据
        $between = [Carbon::now()->subDay()->startOfDay(), Carbon::now()->subDay()->endOfMonth()];

        return User::query()->whereBetween('created_at', $between)->count();
    }
}

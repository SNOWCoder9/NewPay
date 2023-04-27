<?php
/**
 * The file was created by Assimon.
 *
 * @author    assimon<ashang@utf8.hk>
 * @copyright assimon<ashang@utf8.hk>
 * @link      http://utf8.hk/
 */

namespace App\Admin\Metrics\Examples;


use App\Models\Order;
use App\Models\Payment;
use App\Models\Shop;
use Dcat\Admin\Widgets\Metrics\RadialBar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentFlow extends RadialBar
{

    /**
     * 初始化卡片内容
     */
    protected function init()
    {
        parent::init();

        $this->title("每个支付");
        $this->dropdown([
            '1' => '今天',
            '5' => '昨天',
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
                $between = [Carbon::now()->subDay()->startOfDay(), Carbon::now()->subDay()->endOfDay()];
                break;
            case '4':
                $between = [Carbon::now()->subMonth()->firstOfMonth(), Carbon::now()->subMonth()->endOfMonth()];
                break;
            case '3':
                $between = [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
                break;
            case '2':
                $between = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
                break;
            case '1':
                $between = [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()];
                break;
            default:
                $between = [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()];
        }
        // 卡片底部
        $this->withFooter($between);
    }

    /**
     * 订单总数
     *
     * @param $count
     *
     * @author    assimon<ashang@utf8.hk>
     * @copyright assimon<ashang@utf8.hk>
     * @link      http://utf8.hk/
     */
    public function withOrderCount($count)
    {
        return $this->content("");
    }

    /**
     * 成交率.
     *
     * @param int $data
     *
     * @return $this
     */
    public function withChart(int $data)
    {
        return $this->chart([
            'series' => [$data],
        ]);
    }

    /**
     * @param $pending
     * @param $processing
     * @param $completed
     * @param $failure
     * @param $abnormal
     *
     * @author    assimon<ashang@utf8.hk>
     * @copyright assimon<ashang@utf8.hk>
     * @link      http://utf8.hk/
     */
    public function withFooter($between)
    {
        $payments = Payment::all()->toArray();
        $html = "";
        foreach ($payments as $payment){
            $sum = Order::query()->where('payment_id', $payment['id'])->success()->whereBetween('created_at', $between)->sum('goods_price');
            $html .= <<<HTML
<div class="col-md-2 text-center m-1">
        <p>{$payment['name']}</p>
        <span class="font-lg-1">{$sum}</span>
    </div>
HTML;

        }
        return $this->footer(
            <<<HTML
<div class="row">
    $html
</div>
HTML
        );
    }
}

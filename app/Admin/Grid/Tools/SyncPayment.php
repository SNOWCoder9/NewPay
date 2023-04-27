<?php

namespace App\Admin\Grid\Tools;

use App\Models\Payment;
use App\Services\PaymentService;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Illuminate\Http\Request;

class SyncPayment extends AbstractTool
{
    /**
     * 按钮样式定义，默认 btn btn-white waves-effect
     *
     * @var string
     */
    protected $style = 'btn btn-primary btn-outline';


    /**
     * 按钮文本
     *
     * @return string|void
     */
    public function title()
    {
        return '刷新支付';
    }

    /**
     *  确认弹窗，如果不需要则返回空即可
     *
     * @return array|string|void
     */
    public function confirm()
    {
        // 只显示标题
//        return '您确定要发送新的提醒消息吗？';

        // 显示标题和内容
        return [];
    }

    /**
     * 处理请求
     * 如果你的类中包含了此方法，则点击按钮后会自动向后端发起ajax请求，并且会通过此方法处理请求逻辑
     *
     * @param Request $request
     */
    public function handle(Request $request)
    {
        foreach (glob(base_path('app//Payments') . '/*.php') as $file) {
            $class = pathinfo($file)['filename'];
            if (Payment::query()->where('payment', $class)->doesntExist()){
                $class = '\\App\Payments\\'.$class;
                $form = (new $class([]))->config();
                $payment = new Payment();
                $payment->name = $form['show_name'];
                $payment->payment = $form['name'];
                $payment->config = array_flip(array_keys($form['config']));
                $payment->sort = 0;
                $payment->period = '0-24';
                $payment->price = '0-9999';
                $payment->save();
            }
        }
        return $this->response()->success('刷新成功')->refresh();
    }

    /**
     * 设置请求参数
     *
     * @return array|void
     */
    public function parameters()
    {
        return [

        ];
    }
}

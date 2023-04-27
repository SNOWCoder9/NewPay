<?php

namespace App\Admin\Actions;

use App\Models\Payment;
use Dcat\Admin\Grid\RowAction;
use Illuminate\Http\Request;

class CopyPayment extends RowAction
{
    protected $model;

    public function __construct()
    {
        $this->model = new Payment();
    }

    /**
     * 标题
     *
     * @return string
     */
    public function title()
    {
        return '<i class="feather icon-copy"></i> 复制  ';
    }

    /**
     * 设置确认弹窗信息，如果返回空值，则不会弹出弹窗
     *
     * 允许返回字符串或数组类型
     *
     * @return array|string|void
     */
    public function confirm()
    {
        return [
            // 确认弹窗 title
            "您确定要复制这行数据吗？",
            // 确认弹窗 content
            $this->row->name,
        ];
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

        // 获取 parameters 方法传递的参数
        $name = $request->get('name');

        // 复制数据
        $this->model::find($id)->replicate()->save();

        // 返回响应结果并刷新页面
        return $this->response()->success("复制成功: [{$name}]")->refresh();
    }

    /**
     * 设置要POST到接口的数据
     *
     * @return array
     */
    public function parameters()
    {
        return [
            // 发送当前行 name 字段数据到接口
            'name' => $this->row->name,
            // 把模型类名传递到接口
            'model' => $this->model,
        ];
    }
}

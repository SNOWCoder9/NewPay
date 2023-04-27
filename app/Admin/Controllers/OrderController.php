<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\ToNotify;
use App\Admin\Repositories\Order;
use App\Enum\OrderEnum;
use App\Models\User;
use App\Services\OrderService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class OrderController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Order(['user', 'payment']), function (Grid $grid) {
            // 开启字段选择器功能
            $grid->showColumnSelector();
            // 设置默认隐藏字段
            $grid->hideColumns(['token_price', 'notify_url', 'return_url', 'payment.name', 'token_price', 'platform']);
            $grid->column('id')->sortable();
            $grid->column('order_sn')->copyable();
            $grid->column('out_trade_no')->copyable();
            $grid->column('user_id', '用户ID')->copyable();
            $grid->column('user.email', '用户邮箱')->copyable();
            $grid->column('goods_price');
            $grid->column('token_price');
            $grid->column('final_amount');
            $grid->column('notify_url')->link();
            $grid->column('return_url')->link();
            $grid->column('payment.name', '支付方式');
            $grid->column('token');
            $grid->column('platform');
            $grid->column('withdraw')->using([0 => '未结算', 1 => '已结算'])->label([
                0 => 'danger',
                1 => 'success',
            ]);
            $grid->column('status')->select(OrderEnum::text, true);
            $grid->column('created_at')->sortable();
            $grid->disableCreateButton();
            $grid->actions([new ToNotify()]);
            $grid->model()->orderByDesc('id');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id')->select(User::query()->pluck('email', 'id'));
                $filter->equal('order_sn');
                $filter->equal('out_trade_no');
                $filter->equal('token');
                $filter->equal('withdraw')->select([0 => '未结算', 1 => '已结算']);
                $filter->equal('status')->select(OrderEnum::text);
                $filter->whereBetween('created_at', function ($q) {
                    $start = $this->input['start'] ?? null;
                    $end = $this->input['end'] ?? null;
                    $q->where('created_at', '>=', $start)
                        ->where('created_at', '<=', $end);
                })->datetime();
            });
            $grid->export()->rows(function ($rows) {
                $statusText = [0 => '待支付', 1 => '已过期', 2 => '已支付', 3 => '通知成功', 4 => '通知失败'];
                foreach ($rows as $index => &$row) {
                    $row['withdraw'] = $row['withdraw'] == 0 ? '未结算' : '已结算';
                    $row['status'] = $statusText[$row['status']];
                }
                return $rows;
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Order(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('order_sn');
            $show->field('out_trade_no');
            $show->field('transaction_id', "回调订单号");
            $show->field('goods_price');
            $show->field('final_amount');
            $show->field('token_price');
            $show->field('notify_url');
            $show->field('return_url');
            $show->field('status')->as(function ($title) {
                return OrderEnum::text[$title];
            });
            $show->field('withdraw')->as(function ($title) {
                $withdrawText = [0 => '未结算', 1 => '已结算'];
                return $withdrawText[$title];
            });
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    protected function form()
    {
        return Form::make(new Order(), function (Form $form) {
            $form->text('id')->disable();
            $form->text('user_id')->disable();
            $form->text('order_sn')->disable();
            $form->text('out_trade_no')->disable();
            $form->text('transaction_id', "回调订单号")->disable();
            $form->text('goods_price')->disable();
            $form->text('final_amount')->disable();
            $form->text('token_price')->disable();
            $form->text('notify_url');
            $form->text('return_url');
            $form->select('status')->options(OrderEnum::text);
            $form->select('withdraw')->options([0 => '未结算', 1 => '已结算']);
            $form->text('token')->disable();
            $form->text('platform');

            $form->saving(function (Form $form) {
                if ($form->isEditing()) {
                    // 当修改为支付成功状态时，更新净额
                    if ($form->status == OrderEnum::SUCCESS) {
                        OrderService::updateFinalAmount($form->model());
                    }
                }
            });
        });
    }
}

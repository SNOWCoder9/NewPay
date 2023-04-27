<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Settlement;
use App\Jobs\TelegramPush;
use App\Models\User;
use App\Services\TronTransfer;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class SettlementController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Settlement(['user']), function (Grid $grid) {
            $grid->addTableClass(['table-text-center']);
            $grid->column('id')->sortable();
            $grid->column('user.email', '用户邮箱');
            $grid->column('money')->display(function () {
                return $this->money . 'CNY';
            });

            $grid->column('usdt')->copyable();
            $grid->column('rate');
            $grid->column('address')->copyable();
            $grid->column('address_qrcode', '二维码')->qrcode(function(){
                return $this->address;
            });
            $grid->column('batch_format', '批处理格式')->display(function(){
                return $this->address . ',' . $this->usdt;
            })->limit(15, '...');
            $grid->column('status')
                ->if(function ($column) {
                    return $column->getValue() == 0;
                })
                ->display(function () {
                    return '<button class="btn btn-custom settlement" data-id="' . $this->id . '">结算</button>';
                })
                ->else()
                ->display(function () {
                    return '<span style="#21b978">结算成功</span>';
                });
            $grid->column('settlement_time')->display(function () {
                return date('Y-m-d', strtotime($this->settlement_time));
            });
            $grid->column('created_at');
            $grid->model()->orderBy('status', 'asc')->orderByDesc('id');
            $grid->disableCreateButton();
            Admin::script(
                <<<JS
$('.settlement').click(function(){
  var id = $(this).data('id');
  $.ajax({
      url:'settlement/order',
      type:'POST',
      data: {id: id},
      dataType: 'json',
      success:function(res){
        if (res.code === 1){
            Dcat.success('结算成功');
            setTimeout(function () {
                Dcat.reload();
            }, 1000);
        } else {
            Dcat.error(res.message);
        }
      }}
  );
})
JS
            );
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id')->select(User::query()->pluck('email', 'id'));
                $filter->equal('id');
                $filter->equal('address');
                $filter->equal('status');
                $filter->whereBetween('created_at', function ($q) {
                    $start = $this->input['start'] ?? null;
                    $end = $this->input['end'] ?? null;
                    $q->where('created_at', '>=', $start)
                        ->where('created_at', '<=', $end);
                })->datetime();
            });
            $grid->export()->rows(function ($rows) {
                foreach ($rows as $index => &$row) {
                    $row['status'] = $row['status'] == 0 ? '未结算' : '已结算';
                    $row['batch_format'] = $row['address'] . ',' . ($row['usdt'] - 1);
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
        return Show::make($id, new Settlement(), function (Show $show) {
            $show->field('id');
            $show->field('money');
            $show->field('usdt');
            $show->field('address');
            $show->field('rate');
            $show->field('settlement_time');
            $show->field('success_time');
            $show->field('status');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Settlement(), function (Form $form) {
            $form->display('id');
            $form->text('money');
            $form->text('usdt');
            $form->text('address');
            $form->text('rate');
            $form->text('settlement_time');
            $form->text('success_time');
            $form->text('status');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function settlement(Request $request)
    {
        $id = $request->post('id');
        $order = \App\Models\Settlement::query()->find($id);
        $order->status = 1;
        $order->success_time = now()->toDateTimeString();
        $order->save();

        $user = User::query()->find($order->user_id);

        $text = '*结算通知*' . PHP_EOL;
        $text .= '-------------------------' . PHP_EOL;
        $text .= '结算账户：`' . $user->email . '`' . PHP_EOL;
        $text .= '结算金额：`' . $order->money . '元`' . PHP_EOL;
        $text .= '结算USDT：`' . $order->usdt . 'U`' . PHP_EOL;
        $text .= 'USDT汇率：`' . $order->rate . '`' . PHP_EOL;
        $text .= '结算地址：`' . $order->address . '`' . PHP_EOL;
        $text .= '结算日：`' . $order->settlement_time . '`' . PHP_EOL;
        $text .= '结算成功：`' . $order->success_time . '`' . PHP_EOL;
        $keyboard = [
            [
                [
                    'text' => '查看链上交易',
                    'url' => "https://tronscan.io/#/transaction/"
                ]
            ]
        ];
        $chat_id = $user->telegram_id;

        TelegramPush::dispatch(compact('text', 'keyboard', 'chat_id'));

        return response()->json(['code' => 1]);
    }
}

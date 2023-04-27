<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CopyPayment;
use App\Admin\Grid\Tools\SyncPayment;
use App\Admin\Repositories\Payment;
use App\Services\PaymentService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PaymentController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Payment(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('payment');
            $grid->column('price','价格段');
            $grid->column('period','时间段');
            $grid->column('created_at')->sortable();
            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->actions([new CopyPayment()]);
            $grid->tools(new SyncPayment());
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
        return Show::make($id, new Payment(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('payment');
            $show->field('price','价格段');
            $show->field('period','时间段');
            $show->field('config')->json();
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
        return Form::make(new Payment(), function (Form $form) {
            $form->text('name');
            $form->text('period', '时间区间')->default("0-24")->help('请输入0-24的时间范围，例：9-20，代表早上9点到晚上8点');
            $form->text('price', '价格区间')->default("0-99999")->help('请输入一个正确的金额范围，例：10-1000');
            $form->embeds('config', function ($form) {
                $payment = (new PaymentService($form->getKey()))->form()['config'];
                $config = $form->model()->config;
                $keys = array_keys($config);
                foreach ($keys as $key){
                    switch ($payment[$key]['type']){
                        case 'text':
                            $form->text($key, $payment[$key]['name'])->help($payment[$key]['note']);
                            break;
                        case 'textarea':
                            $form->textarea($key, $payment[$key]['name'])->help($payment[$key]['note']);
                            break;
                    }
                }
            });
        });
    }
}

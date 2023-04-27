<?php

namespace App\Admin\Controllers;

use App\Admin\Grid\Tools\TestPay;
use App\Admin\Repositories\Shop;
use App\Enum\TypeEnum;
use App\Models\Payment;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ShopController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Shop(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('image')->image("", 60, 60);
            $grid->column('name');
            $grid->column('token');
            $grid->column('type')->using(TypeEnum::type);
            $grid->column('rate', '费率')->display(function ($rate){
                return "%{$rate}";
            });
            $grid->column('sort')->editable();
            $grid->column('status')->switch();
            $grid->column('month_price');
            $grid->column('quarter_price');
            $grid->column('half_year_price');
            $grid->column('year_price');
            $grid->column('three_year_price');
            $grid->column('created_at');
            $grid->tools(new TestPay());
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('name');
                $filter->like('desc');
                $filter->equal('token');
                $filter->equal('rate');
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
        return Show::make($id, new Shop(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('desc');
            $show->field('token');
            $show->field('status');
            $show->field('rate');
            $show->field('sort');
            $show->field('image');
            $show->field('month_price');
            $show->field('quarter_price');
            $show->field('half_year_price');
            $show->field('year_price');
            $show->field('three_year_price');
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
        return Form::make(new Shop(), function (Form $form) {
            $form->column(6, function (Form $form) {
                $form->image('image')->uniqueName()->required();
                $form->text('name')->required();
                $form->text('desc')->required();
                $form->text('token')->required();
                $form->rate('rate', '费率');
                $form->number('sort')->default(0);
                $form->switch('status')->default(1);
            });
            $form->column(6, function (Form $form) {
                $form->currency('month_price');
                $form->currency('quarter_price');
                $form->currency('half_year_price');
                $form->currency('year_price');
                $form->currency('three_year_price');
                $form->radio('type')->options(TypeEnum::type)
                    ->when('>', 1, function ($form) {
                        $methods = Payment::query()->pluck('name', 'id');
                        $form->multipleSelect('payment_ids', '支付插件')->options($methods);
                    })->default(1);
            });
        });
    }
}

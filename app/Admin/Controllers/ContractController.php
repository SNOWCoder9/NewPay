<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Contract;
use App\Enum\CycleEnum;
use App\Enum\TypeEnum;
use App\Models\Shop;
use App\Models\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ContractController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Contract(['user']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('user.email');
            $grid->column('token');
            $grid->column('type')->using(TypeEnum::type);
            $grid->column('rate');
            $grid->column('expired_at');
            $grid->column('status')->switch();
            $grid->column('created_at')->sortable();
            $grid->model()->orderByDesc('id');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id')->select(User::query()->pluck('email', 'id'));
                $filter->equal('id');
                $filter->equal('token');
                $filter->equal('type')->select(TypeEnum::type);
                $filter->equal('cycle')->select(CycleEnum::cycle);
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
        return Show::make($id, new Contract(['user']), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('user.email');
            $show->field('token');
            $show->field('type')->using(TypeEnum::type);
            $show->field('cycle')->using(CycleEnum::cycle);
            $show->field('address');
            $show->field('expired_at');
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
        return Form::make(new Contract(), function (Form $form) {
            $form->select('user_id')->options(
                User::query()->pluck('email', 'id')
            )->required();
            $form->select('token')->options(
                Shop::query()->pluck('name', 'token')
            )->required();
            $form->radio('type')->options(TypeEnum::type)->default(1)->required();
            $form->text('address');
            $form->rate('rate')->default(0);
            $form->radio('cycle')->options(CycleEnum::cycle)->default('month_price')->required();
            // $form->datetime('expired_at')->required();
            $form->hidden('expired_at');
            $form->switch('status')->default(1);
            $form->saving(function (Form $form) {
                $day = CycleEnum::cycleDay[$form->cycle ?? 'three_year_price'];
                $form->expired_at = date("Y-m-d H:i:s", time() + ($day * 86400));
            });
        });
    }
}

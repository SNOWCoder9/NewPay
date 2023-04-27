<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Tron;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class TronController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Tron(['user']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('user.name', '用户');
            $grid->column('token');
            $grid->column('address');
            $grid->column('created_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('user_id');
                $filter->equal('address');
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
        return Show::make($id, new Tron(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('token');
            $show->field('address');
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
        return Form::make(new Tron(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('token');
            $form->text('address');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}

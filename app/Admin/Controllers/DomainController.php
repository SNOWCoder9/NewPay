<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Domain;
use App\Enum\DomainEnum;
use App\Models\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class DomainController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Domain(['user']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('user.email', '邮箱');
            $grid->column('domain')->copyable();
            $grid->column('status')->select(DomainEnum::list);
            $grid->column('created_at')->sortable();
            $grid->model()->orderByDesc('id');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id')->select(User::query()->pluck('email', 'id'));
                $filter->equal('id');
                $filter->like('domain');
                $filter->equal('status')->select(DomainEnum::list);
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
        return Show::make($id, new Domain(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('domain');
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
        return Form::make(new Domain(), function (Form $form) {
            $form->select('user_id')->options(User::query()->pluck('email', 'id'));
            $form->text('domain')->help("请不要输入http,只需要域名.例: www.baidu.com");
            $form->select('status')->options(DomainEnum::list);
        });
    }
}

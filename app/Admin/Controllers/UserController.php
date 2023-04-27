<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\User;
use App\Models\Order;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Carbon;

class UserController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new User(), function (Grid $grid) {
            // 开启字段选择器功能
            $grid->showColumnSelector();
            // 设置默认隐藏字段
            $grid->hideColumns(['app_id', 'app_secret']);
            $grid->column('id')->sortable();
            $grid->column('name')->copyable();
            $grid->column('email')->copyable();
            $grid->column('app_id')->copyable();
            $grid->column('app_secret')->copyable();
            $grid->column('success', '本月成交率')->display(function () {
                $user = \App\Models\User::query()->find($this->id);
                $all = $user->order()->dateQuery('month')->count();
                $success = $user->order()->success()->dateQuery('month')->count();
                return $all === 0 ? 0 : '<span class="text-danger">'.round(($success / $all) * 100) . "%({$success}/{$all})</span>";
            });
            $grid->column('order_price', '本月交易金额')->display(function () {
                $money = Order::query()
                    ->where('user_id', $this->id)
                    ->success()
                    ->dateQuery('month')
                    ->sum('goods_price');

                return '<span class="text-info">'.$money.'CNY</span>';
            });
            $grid->column('telegram_account', 'Telegram Name')->copyable();
            $grid->column('last_ip', '上次登录IP')->copyable();
            $grid->column('last_login', '上次登录时间')->copyable();

            $grid->column('expired_at', '到期时间')->display(function ($expired_at) {
                return date('Y-m-d H:i:s', $expired_at);
            })->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->select(\App\Models\User::query()->pluck('email', 'id'));
                $filter->like('name');
                $filter->like('address');
                $filter->equal('telegram_account');
                $filter->equal('app_id');
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
        return Show::make($id, new User(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('telegram_id');
            $show->field('telegram_account');
            $show->field('app_id');
            $show->field('app_secret');
            $show->field('count');
            $show->field('expired_at');
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
        return Form::make(new User(), function (Form $form) {
            $form->text('name');
            $form->email('email')->required()->rules(function (Form $form) {
                // 如果不是编辑状态，则添加字段唯一验证
                if (!$id = $form->model()->id) {
                    return 'unique:users,email';
                }
            });
            $form->text('address', 'U结算地址');
            $form->text('password')->value('')->placeholder('留空代表不改变');
            $form->text('app_id')->value('')->placeholder('新增的时候可留空');
            $form->text('app_secret')->value('')->placeholder('新增的时候可留空');
            $form->datetime('expired_at', '过期时间')->customFormat(function ($value){
                if ($value){
                    return Carbon::parse($value)->toDateTimeString();
                } else {
                    return Carbon::now()->toDateTimeString();
                }
            });

            $form->saving(function (Form $form) {
                if ($form->isEditing() && $form->password) {
                    $form->password = bcrypt($form->password);
                } elseif ($form->isCreating()) {
                    $form->password = $form->password ? bcrypt($form->password) : bcrypt(123456);
                    $form->app_id = generate_auth_code(12, 'app_id');
                    $form->app_secret = sha1(time());
                } else {
                    $form->deleteInput('password');
                }
                $form->expired_at = strtotime($form->expired_at);
                if (is_null($form->name)) {
                    $form->name = $form->email;
                }
            });
        });
    }
}

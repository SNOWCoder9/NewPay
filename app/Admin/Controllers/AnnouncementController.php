<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Announcement;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class AnnouncementController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Announcement(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('title');
//            $grid->column('content')->width(800);
            $grid->column('status')->switch();
            $grid->column('created_at')->sortable();
            $grid->model()->orderByDesc('id');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('title');
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
        return Show::make($id, new Announcement(), function (Show $show) {
            $show->field('id');
            $show->field('title');
            $show->field('content');
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
        return Form::make(new Announcement(), function (Form $form) {
            $form->display('id');
            $form->text('title');
            $form->editor('content');
            $form->switch('status')->default(1);
        });
    }
}

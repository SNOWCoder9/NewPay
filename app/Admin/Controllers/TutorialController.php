<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Tutorial;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class TutorialController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Tutorial(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('download');
            $grid->column('status')->switch();
            $grid->column('sort')->editable();
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('name');
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
        return Show::make($id, new Tutorial(), function (Show $show) {
            $show->field('id');
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('download');
            $show->field('status');
            $show->field('sort');
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
        return Form::make(new Tutorial(), function (Form $form) {
            $form->text('name');
            $form->markdown('description');
            $form->text('download');
            $form->number('sort')->default(0);
            $form->switch('status')->default(1);
        });
    }
}

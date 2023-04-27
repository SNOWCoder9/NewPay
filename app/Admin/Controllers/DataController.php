<?php
/**
 * @Title :
 * @Remark:
 */

namespace App\Admin\Controllers;

use App\Admin\Forms\DataClean;
use App\Admin\Forms\SystemSetting;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Form;

class DataController extends AdminController
{
    public function dataCleaning(Content $content)
    {
        return $content
            ->title("数据清理")
            ->body(new Card(new DataClean()));
    }

    // protected function form()
    // {
    //     return Form::make(function (Form $form) {
    //         $form->block(8, function (Form\BlockForm $form) {
    //             // 设置标题
    //             $form->title('基本设置');
    //             // 显示底部提交按钮
    //             $form->showFooter();
    //             // 设置字段宽度
    //             $form->width(9, 2);
    //
    //             $form->text('test1', 'hha1');
    //
    //             // $form->column(6, function (Form\BlockForm $form) {
    //             //     $form->text('test1', 'hha1');
    //             // });
    //             // $form->column(6, function (Form\BlockForm $form) {
    //             //     $form->text('test2', 'hha2');
    //             // });
    //         });
    //         $form->block(4, function (Form\BlockForm $form) {
    //             $form->title('分块2');
    //             $form->text('test1', 'hha1');
    //         });
    //     });
    // }
}

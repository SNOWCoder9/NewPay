<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\SystemSetting;
use App\Admin\Repositories\Config;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Card;
use Illuminate\Support\Facades\Cache;

class ConfigController extends AdminController
{
    /**
     * 系统设置
     *
     * @param Content $content
     * @return Content
     *
     * @author    assimon<ashang@utf8.hk>
     * @copyright assimon<ashang@utf8.hk>
     * @link      http://utf8.hk/
     */
    public function systemSetting(Content $content)
    {
        return $content
            ->title("系统配置")
            ->body(new Card(new SystemSetting()));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Config(), function (Form $form) {
            $form->text('name');
            $form->text('key');
            $form->textarea('value');
            $form->disableDeleteButton();
            $form->saved(function (Form $form, $result) {
                $key = $form->model()->key;
                $value = $form->model()->value;
                Cache::forever($key, $value);
            });
        });
    }
}

<?php

namespace App\Admin\Forms;

use App\Enum\PayModelEnum;
use App\Models\Config;
use Dcat\Admin\Widgets\Form;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Form
{
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        foreach ($input as $key => $value) {
            Config::query()->where('key', $key)->update(['value' => $value]);
            Cache::forever($key, $value);
        }

        return $this->response()->success("修改成功");
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $configs = Config::all();
        $tabs = array_unique(array_column($configs->toArray(), 'type'));
        foreach ($tabs as $tab) {
            $this->tab($tab, function () use ($configs, $tab) {
                foreach ($configs as $config) {
                    if ($config->type === $tab) {
                        switch ($config->field) {
                            case 'switch':
                                $that = $this->switch($config->key, $config->name)->value($config->value)->customFormat(function ($v) {
                                    return $v == 'true' ? 1 : 0;
                                })
                                    ->saving(function ($v) {
                                        return $v ? 'true' : 'false';
                                    });
                                break;
                            case "textarea":
                                $that = $this->textarea($config->key, $config->name)->value($config->value);
                                break;
                            case "select":
                                if ($config->key === 'pay_model_setting') {
                                    $options = PayModelEnum::options;
                                }
                                $that = $this->select($config->key, $config->name)->value($config->value)->options($options ?? []);
                                break;
                            default:
                                $that = $this->text($config->key, $config->name)
                                    ->value($config->value);
                        }
                        if ($config->help && $that) {
                            $that->help($config->help);
                        }
                    }
                }
            });
        }
    }
}

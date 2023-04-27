<?php

namespace App\Console\Commands;

use App\Models\Config;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NewPayInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newpay:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'NewPay安装';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->info(" |¯¯¯¯\   |¯||¯¯¯");
            $this->info(" | |¯\ \  | || |  |");
            $this->info(" | |  \ \ | || | /");
            $this->info(" | |   \ \| || |  ");
            $this->info(" |_|    \_|_||_|  ");


            // $this->info("__     ______  ____                      _  ");
            // $this->info("\ \   / /___ \| __ )  ___   __ _ _ __ __| | ");
            // $this->info(" \ \ / /  __) |  _ \ / _ \ / _` | '__/ _` | ");
            // $this->info("  \ V /  / __/| |_) | (_) | (_| | | | (_| | ");
            // $this->info("   \_/  |_____|____/ \___/ \__,_|_|  \__,_| ");
            if (!\File::exists(base_path() . '/.env')) {
                if (!copy(base_path() . '/.env.example', base_path() . '/.env')) {
                    abort(500, '复制环境文件失败，请检查目录权限');
                }
                $this->saveToEnv([
                    'DB_HOST' => $this->ask('请输入数据库地址（默认:localhost）', 'localhost'),
                    'DB_DATABASE' => $this->ask('请输入数据库名'),
                    'DB_USERNAME' => $this->ask('请输入数据库用户名'),
                    'DB_PASSWORD' => $this->ask('请输入数据库密码')
                ]);
            } else {
                try {
                    DB::connection()->getPdo();
                } catch (\Exception $e) {
                    abort(500, '数据库连接失败，请检查 .env 里面的数据库连接是否正确');
                }
            }
            $file = \File::get(base_path() . '/database/install.sql');
            if (!$file) {
                abort(500, '数据库文件不存在');
            }
            $sql = str_replace("\n", "", $file);
            $sql = preg_split("/;/", $sql);
            if (!is_array($sql)) {
                abort(500, '数据库文件格式有误');
            }
            $this->info('正在导入数据库请稍等...');
            foreach ($sql as $item) {
                try {
                    DB::select(DB::raw($item));
                } catch (\Exception $e) {
                }
            }
            $config = Config::all();
            foreach ($config as $item) {
                Cache::forever($item->key, $item->value);
            }
            $this->info('数据库导入完成');
            $this->info('一切就绪，初始账号: admin ,密码: 123456');
            $this->info('访问 http(s)://你的站点/admin 进入管理面板');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function saveToEnv($data = [])
    {
        function set_env_var($key, $value)
        {
            if (!is_bool(strpos($value, ' '))) {
                $value = '"' . $value . '"';
            }
            $key = strtoupper($key);

            $envPath = app()->environmentFilePath();
            $contents = file_get_contents($envPath);

            preg_match("/^{$key}=[^\r\n]*/m", $contents, $matches);

            $oldValue = count($matches) ? $matches[0] : '';

            if ($oldValue) {
                $contents = str_replace("{$oldValue}", "{$key}={$value}", $contents);
            } else {
                $contents = $contents . "\n{$key}={$value}\n";
            }

            $file = fopen($envPath, 'w');
            fwrite($file, $contents);
            return fclose($file);
        }

        foreach ($data as $key => $value) {
            set_env_var($key, $value);
        }
        return true;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Config;
use App\Models\Shop;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SetConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '刷新配置缓存';

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
     * @return int
     */
    public function handle()
    {
        $configs = Config::all();
        foreach ($configs as $config){
            if ($config->value){
                Cache::forever($config->key, $config->value);
            }
        }
        Artisan::call('sync:eth_btc_to_usd');
    }
}

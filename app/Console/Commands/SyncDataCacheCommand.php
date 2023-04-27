<?php

namespace App\Console\Commands;

use App\Enum\DomainEnum;
use App\Models\Config;
use App\Models\Domain;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SyncDataCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步数据缓存';

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
        $domain_list = Domain::query()->where('status', DomainEnum::SUCCESS)->pluck('domain')->toArray();
        Cache::forever('domain_white_list', $domain_list);
        $this->info("授权域名总数: ".count($domain_list));
        $configs = Config::all()->toArray();
        foreach ($configs as $config){
            Cache::forever($config['key'], $config['value']);
            $this->info("配置成功: ".$config['name']);
        }
    }
}

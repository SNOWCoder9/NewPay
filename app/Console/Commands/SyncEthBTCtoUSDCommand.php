<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SyncEthBTCtoUSDCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:eth_btc_to_usd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $result = Http::get("https://aws.okx.com/api/v5/market/exchange-rate")->body();
        $result = json_decode($result , true);
        $usd_cny = $result['data'][0]['usdCny'];
        Cache::forever('USDT_CNY', $usd_cny);
        $result = Http::get("https://aws.okx.com/api/v5/market/ticker?instId=ETH-USD-SWAP")->body();
        $result = json_decode($result , true);
        $etn_usd = $result['data'][0]['last'];
        Cache::forever('ETH_USD', $etn_usd);
        Cache::forever('ETH_CNY', $etn_usd * $usd_cny);
        $now = now()->toDateTimeString();
        Cache::forever('sync_time', $now);
        $this->info("当前更新时间：". $now);
        $this->info("当前USDT_CNY：". $usd_cny);
        $this->info("当前ETH_USD：". $etn_usd);
        $this->info("当前ETH_CNY：". $etn_usd * $usd_cny);
        $result = Http::get("https://aws.okx.com/api/v5/market/ticker?instId=BTC-USD-SWAP")->body();
        $result = json_decode($result , true);
        $btc_usd = $result['data'][0]['last'];
        Cache::forever('BTC_USD', $btc_usd);
        Cache::forever('BTC_CNY', $btc_usd * $usd_cny);
        $this->info("当前BTC_USD：". $btc_usd);
        $this->info("当前BTC_CNY：". $btc_usd * $usd_cny);
    }
}

<?php

namespace App\Console\Commands;

use App\Enum\OrderEnum;
use App\Jobs\OrderNotify;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CheckEtherscanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:etherscan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check etherscan';

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
        $key = getConfig('etherscan_key');
        $orders = Order::query()->where([
            'token'  => 'ETH',
            'status' => 0
        ])->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-20 minute')))->get();
        $orders->each(function ($order) use ($key){
            # https://api.etherscan.io/api?module=account&action=txlist&address=0x2fdD998aC6566eC27c3E9b4CAaB3B04b1111f0E1&startblock=0&endblock=99999999&page=1&offset=10&sort=desc&apikey=D646GG89868ZMBXTT9I2WFX7YQIVVCYAGC
            $client = new \Etherscan\Client($key);
            $result = $client->api('account')->transactionListByAddress($order->address, 0, 99999999, 'desc', 1, 10);
            if (is_array($result['result']) && count($result['result']) > 0){
                foreach ($result['result'] as $item){
                    // 支付的值大于0 & 已成功 & 支付时间+30分钟 > 当前时间
                    if ($item['value'] > 0
                        && $item['isError'] == 0
                        && $item['timeStamp'] + 1200 > time()
                    ){
                        $token = (float)sprintf('%.6f', $item['value'] / 1000000000000000000);
                        if ($token == floatval($order->token_price)){
                            $order->final_amount = $order->goods_price;
                            $order->transaction_id = $item['hash'];
                            $order->notified_at = now()->toDateTimeString();
                            $order->status = OrderEnum::SUCCESS; // 支付成功
                            $order->save();
                            OrderNotify::dispatch($order);
                        }
                    }
                }
            }
        });
    }
}

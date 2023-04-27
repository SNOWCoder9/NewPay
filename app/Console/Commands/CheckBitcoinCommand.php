<?php

namespace App\Console\Commands;

use App\Enum\OrderEnum;
use App\Jobs\OrderNotify;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckBitcoinCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:bitcoin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check bitcoin';

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
        $orders = Order::query()->where([
            'token'  => 'BTC',
            'status' => 0
        ])->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-20 minute')))->get();
        $orders->each(function ($order) {
            # https://blockchain.info/rawaddr/34xp4vRoCGJym3xR7yCVPFHoCNxv4Twseo?page=1&limit=10
            $result = Http::get("https://blockchain.info/rawaddr/{$order->address}?page=1&limit=10")->body();
            $result = json_decode($result, true);
            if (isset($result['txs']) && count($result['txs']) > 0){
                foreach ($result['txs'] as $item){
                    if ($item['result'] > 0 && $item['time'] + 1200 > time()){
                        $token = (float)sprintf('%.6f', $item['result'] / 100000000);
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

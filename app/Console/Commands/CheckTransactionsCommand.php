<?php

namespace App\Console\Commands;

use App\Enum\OrderEnum;
use App\Jobs\OrderNotify;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查TRON-USDT交易记录';

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
        Order::query()
            ->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-10 minute')))
            ->where('status', 0)
            ->where('token', 'USDT')
            ->chunk(100, function ($orders) {
                foreach ($orders as $order) {
                    $start = time() - 600;
                    $data = $this->getData('GET', "/v1/accounts/{$order->address}/transactions/trc20?only_to=true&limit=10&min_timestamp={$start}000", []);
                    if (count($data['data']) > 0) {
                        foreach ($data['data'] as $datum) {
                            if($datum['token_info']['name'] == "Tether USD" && $datum['token_info']['symbol'] == "USDT"){
                                $result = $this->getData('POST', "/wallet/gettransactioninfobyid",
                                    ['value' => $datum['transaction_id']]);
                                if (isset($result['receipt']['result']) && $result['receipt']['result'] == 'SUCCESS') {
                                    $usdt = sprintf('%.3f', $datum['value'] / 1000000);
                                    if ($usdt == $order->token_price) {
                                        $order->final_amount = $order->goods_price;
                                        $order->transaction_id = $datum['transaction_id'];
                                        $order->status = OrderEnum::SUCCESS; // 支付成功
                                        $order->save();
                                        OrderNotify::dispatch($order);
                                    }
                                }
                            }
                        }
                    }
                }
            });
    }

    public function getData($method, $url, $body)
    {
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://api.trongrid.io']);

        $response = $client->request($method, $url, [
            'body' => json_encode($body),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'TRON-PRO-API-KEY' => getConfig('trongrid_token'),
            ],
        ]);
        $data = $response->getBody()->getContents();

        return json_decode($data, true);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CheckOrderExpireCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查订单是否过期';

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
        Order::query()
            ->where('created_at', '<', date('Y-m-d H:i:s', strtotime('-20 minute')))
            ->where('status', 0)
            ->update(['status' => 1]);
    }
}

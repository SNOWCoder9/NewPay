<?php

namespace App\Console\Commands;

use App\Jobs\TelegramPush;
use App\Models\Contract;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;

class UserContractExpireCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:user_contract_expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查签约到期时间';

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
        Contract::query()
            ->where('status', 1)
            ->where('expired_at', '<', now()->toDateTimeString())
            ->update(['status' => 0]);
    }
}

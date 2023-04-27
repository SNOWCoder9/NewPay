<?php

namespace App\Console\Commands;

use App\Jobs\TelegramPush;
use App\Models\Contract;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;

class UserExpireCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:user_expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查检查用户到期时间';

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
        $three_day = strtotime('+3 day');
        $contracts = Contract::query()
            ->with(['user'])
            ->where('expired_at', '>', now()->toDateTimeString())
            ->where('expired_at', '<', date('Y-m-d H:i:s', $three_day))
            ->get();

        $contracts->each(function ($contract){
            $text = '尊敬的【'.$contract->user->email.'】用户你好，您的授权即将到期，请及时续费！' . PHP_EOL;
            $text .= '到期时间：'.date('Y-m-d H:i:s', $contract->expired_at) . PHP_EOL;
            $keyboard = [
                [
                    [
                        'text' => '授权续费',
                        'url'  => route('index')
                    ]
                ]
            ];
            $chat_id = $contract->user->telegram_id;
            TelegramPush::dispatch(compact('text', 'keyboard', 'chat_id'));
        });
    }
}

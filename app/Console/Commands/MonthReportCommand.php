<?php

namespace App\Console\Commands;

use App\Jobs\TelegramPush;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonthReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每月财务报表';

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
        $users = User::query()->where('expired_at', '>', time())->get();
        $users->each(function ($user) {
            $chat_id = $user->telegram_id;
            $beginThisWeek = Carbon::now()->subMonth()->firstOfMonth();
            $endLastWeek = Carbon::now()->subMonth()->lastOfMonth();
            $goods_price = $user->order()
                ->whereIn('status', [2, 3, 4])
                ->where('created_at', '>', $beginThisWeek)
                ->where('created_at', '<', $endLastWeek)
                ->sum('goods_price');
            $count = $user->order()
                ->whereIn('status', [2, 3, 4])
                ->where('created_at', '>', $beginThisWeek)
                ->where('created_at', '<', $endLastWeek)
                ->count('id');
            $text = '*新鲜出炉的财务月报：*' . PHP_EOL .
                "上月总收入笔数: `{$count}笔`" . PHP_EOL .
                "上月总收入金额: `{$goods_price}元`" . PHP_EOL .PHP_EOL .
                "月初也在努力工作~";
            $keyboard = null;
            TelegramPush::dispatch(compact('text', 'keyboard', 'chat_id'));
        });
    }
}

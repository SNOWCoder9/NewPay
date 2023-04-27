<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class OrderSettlementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:settlement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单结算';

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
        // 结算周期
        $day = getConfig('settlement_day', 1);
        // 结算U价
        $usdt_price = getConfig('settlement_usdt_price', 7);
        // 结算日
        $date = date('Y-m-d 23:59:59', strtotime("-{$day} day"));
        $users = User::query()->whereHas('contract', function (Builder $query) {
            $query->where('status', 1);
        })->where('status', 1)->get();
        $count = 0;
        $money = 0;
        // 最低结算金额
        $settlement_money = getConfig('settlement_money');
        foreach ($users as $user) {
            $final_amount = Order::getSettleData($user->id, $date)->sum('final_amount');
            if ($final_amount >= $settlement_money) {
                $settle_no = Str::random();
                $settle = new Settlement();
                $settle->user_id = $user->id;
                $settle->money = $final_amount;
                $settle->usdt = (float)bcdiv($final_amount, $usdt_price, 3);
                $settle->address = $user->address;
                $settle->rate = $usdt_price;
                $settle->settlement_time = $date;
                $settle->settle_no = $settle_no;
                $settle->status = 0;
                $settle->save();
                $count++;
                $money += $final_amount;
                Order::getSettleData($user->id, $date)->update(['settle_no' => $settle_no, 'withdraw' => 1]);
            }
        }

        $this->info("今日待结算用户：" . $count . "人，待结算金额：" . $money);
    }
}

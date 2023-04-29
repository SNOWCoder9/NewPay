<?php

namespace App\Console\Commands;

use App\Models\Settlement;
use Illuminate\Console\Command;

class SetSettleRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settle:rate
                            {date : 结算日，例如：2023-01-01}
                            {rate : 汇率，例如：7.2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修改结算汇率';

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
        $date = $this->argument('date');
        $rate = $this->argument('rate');
        $settlement = Settlement::where('status', 0)
            ->where('settlement_time', '>=', $date . " 00:00:00")
            ->where('settlement_time', '<=', $date . " 23:59:59")
            ->get();
        foreach ($settlement as $settle) {
            $settle->rate = $rate;
            $settle->usdt = (float)bcdiv($settle->money, $rate, 3);
            $settle->save();
        }

        $this->info("修改完成\n结算日期：{$date}\n汇率：{$rate}");
    }
}

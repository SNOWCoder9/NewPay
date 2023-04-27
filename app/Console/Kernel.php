<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // USDT钱包交易监听
        $schedule->command('check:transactions')->withoutOverlapping()->everyMinute();
        // ETH钱包交易监听
        $schedule->command('check:etherscan')->everyMinute();
        // BTC钱包交易监听
        $schedule->command('check:bitcoin')->everyMinute();
        // 订单检测
        $schedule->command('order:check')->everyMinute();
        // 签约到期时间检测
        $schedule->command('check:user_contract_expire')->everyMinute();
        // 每日结算
        $schedule->command('order:settlement')->dailyAt('00:05');
        // 用户到期检测
        $schedule->command('check:user_expire')->dailyAt('22:00');
        // 周报
        $schedule->command('report:weekly')->weeklyOn(1, '00:10');
        // 月报
        $schedule->command('report:month')->monthlyOn(1, '00:20');
        // 同步USDT/BTC/ETH交易所市价
        $schedule->command('sync:eth_btc_to_usd')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

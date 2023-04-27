<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderNotifyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderNotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $telegramNotify;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $telegramNotify = true)
    {
        $this->order = $order;
        $this->telegramNotify = $telegramNotify;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        OrderNotifyService::sendHttpNotify($this->order, $this->telegramNotify);
    }
}

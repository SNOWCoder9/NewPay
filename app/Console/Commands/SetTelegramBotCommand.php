<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;

class SetTelegramBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '配置Telegram机器人';

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
        $token = getConfig('telegram_bot_token');
        $telegram = new Api($token);
        $response = $telegram->setWebhook([
            'url'               => url('/telegram/webhook'),
            'max_connections'   => 80,
            'drop_pending_updates' => true
        ]);

        $this->info($response);
    }
}

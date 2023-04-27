<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;


class TelegramPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 30;


    public $data = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws TelegramSDKException
     */
    public function handle()
    {
        $token = getConfig('telegram_bot_token');
        $telegram = new Api($token);
        $telegram->sendMessage([
            'chat_id' => $this->data['chat_id'],
            'text' => $this->data['text'],
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => false,
            'reply_to_message_id' => null,
            'reply_markup' => $this->data['keyboard'] ? json_encode(['inline_keyboard' => $this->data['keyboard']]) : null
        ]);
    }
}

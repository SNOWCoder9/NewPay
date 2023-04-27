<?php


namespace App\Http\Controllers;

use App\Services\Telegram\Callback\Callback;
use App\Services\Telegram\Callback\ReplyMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        $token = getConfig('telegram_bot_token');
        $telegram = new Api($token);
        $telegram->addCommands([
            \App\Services\Telegram\HelpCommand::class,
            \App\Services\Telegram\BindCommand::class,
            // \App\Services\Telegram\MenuCommand::class,
            \App\Services\Telegram\MyCommand::class,
        ]);
        $update = $telegram->commandsHandler(true);
        $Message = $update->getMessage();
        // Log::info(json_encode($Message));
        if ($Message && Cache::has($Message->getChat()->getId())) {
            $data = Cache::get($Message->getChat()->getId());
            $msg = new ReplyMessage($telegram, $Message);
            switch ($data[0]){
                case 'user':
                    $msg->userMessage($data[1]);
                    break;
            }

        } else if ($update->getCallbackQuery()) {
            new Callback($telegram, $update->getCallbackQuery());
        }
    }
}

<?php


namespace App\Services\Telegram;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\Command;

class BindCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "bind";

    /**
     * @var string Command Description
     */
    protected $description = "绑定授权码.";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $Update = $this->getUpdate();
        $commandParts = explode(' ', $Update->message->text);
        $arguments = $commandParts[1] ?? null;
        $Message = $Update->getMessage();
        // 消息 ID
        $MessageID = $Message->getMessageId();
        // 消息会话 ID
        $ChatID = $Message->getChat()->getId();
        // 发送 '输入中' 会话状态
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $send_user_id = $Message->getFrom()->getId();
        $user = User::getUser($send_user_id);
        if ($user) {
            // 已经绑定授权
            $this->replyWithMessage([
                'text' => "你的账号已经绑定授权，无需重复绑定",
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => null
            ]);
        } else {
            if ($arguments) {
                $user = User::query()->where('app_id', $arguments)->first();
                if (!$user){
                    return $this->noAuth($ChatID);
                }
                $user->telegram_id = $Message->getFrom()->getId();
                $user->telegram_account = $Message->getFrom()->getUsername() ?? null;
                $user->save();
                $this->replyWithMessage([
                    'text' => "恭喜，授权成功！",
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null
                ]);
            } else {
                return $this->noAuth($arguments);
            }
        }
    }

    public function noAuth($arguments)
    {
        return $this->replyWithMessage([
            'text' => "请输入正确的App ID" . PHP_EOL. PHP_EOL . "绑定格式：`/bind xxxxx`".$arguments,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => false,
            'reply_to_message_id' => null,
            'reply_markup' => null
        ]);
    }
}

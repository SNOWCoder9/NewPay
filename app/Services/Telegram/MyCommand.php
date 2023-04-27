<?php


namespace App\Services\Telegram;

use App\Models\Tron;
use App\Models\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class MyCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "info";

    /**
     * @var string Command Description
     */
    protected $description = "查看个人信息.";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $Update = $this->getUpdate();
        $Message = $Update->getMessage();
        $send_user_id = $Message->getFrom()->getId();
        $user = User::getUser($send_user_id);
        if ($user){
            $text  = '尊敬的用户【'.$user->name.'】您好';
            $text .= PHP_EOL . PHP_EOL;
            $text .= 'AppID：`'.$user->app_id.'`'. PHP_EOL;
            $text .= '结算地址：'.$user->address. PHP_EOL;
            $text .= '到期时间：' . date('Y-m-d H:i:s', $user->expired_at). PHP_EOL;
            $response = $this->replyWithMessage(
                [
                    'text'                      => $text,
                    'parse_mode'                => 'Markdown',
                    'disable_web_page_preview'  => false,
                    'reply_to_message_id'       => null,
                    'reply_markup'              => null
                ]
            );
        } else {
            $text = '游客您好，以下是 BOT 菜单：';
            $reply = [
                'text'     => $text,
                'keyboard' => null,
            ];
            $response = $this->replyWithMessage(
                [
                    'text'                      => $reply['text'],
                    'parse_mode'                => 'Markdown',
                    'disable_web_page_preview'  => false,
                    'reply_to_message_id'       => null,
                    'reply_markup'              => null,
                ]
            );
        }

        return $response;
    }
}

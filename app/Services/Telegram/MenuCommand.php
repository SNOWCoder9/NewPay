<?php


namespace App\Services\Telegram;

use App\Models\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class MenuCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "menu";

    /**
     * @var string Command Description
     */
    protected $description = "用户菜单.";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $Update = $this->getUpdate();
        $Message = $Update->getMessage();
        $send_user_id = $Message->getFrom()->getId();
        $user = User::getUser($send_user_id);
        if ($user){
            $Keyboard = [
                [
                    [
                        'text'          => '配置TRC20地址',
                        'callback_data' => 'user.tron'
                    ],
                    [
                        'text'          => '查询所有地址',
                        'callback_data' => 'user.show_tron'
                    ],
                ],
                [
                    [
                        'text'          => '费率设置',
                        'callback_data' => 'user.usdt_rate'
                    ],
                    [
                        'text'          => '授权续费',
                        'url'           => "https://faka.bob1.xyz/buy/14"
                    ]
                ],
                [
                    [
                        'text'          => '对接教程',
                        'callback_data' => "user.secret"
                    ],
                ]
            ];
            $text = '【'.$user->name.'】您好，以下是 BOT 菜单：';
            $reply = [
                'text'     => $text,
                'keyboard' => $Keyboard,
            ];
        } else {
            $Keyboard = [
                [
                    [
                        'text' => '购买授权',
                        'url'  => "https://faka.bob1.xyz/buy/13"
                    ]
                ]
            ];
            $text = '游客您好，以下是 BOT 菜单：';
            $reply = [
                'text'     => $text,
                'keyboard' => $Keyboard,
            ];
        }

        $this->replyWithMessage(
            [
                'text'                      => $reply['text'],
                'parse_mode'                => 'Markdown',
                'disable_web_page_preview'  => false,
                'reply_to_message_id'       => null,
                'reply_markup'              => json_encode(
                    [
                        'inline_keyboard' => $reply['keyboard']
                    ]
                ),
            ]
        );
    }
}

<?php


namespace App\Services\Telegram\Callback;


use App\Models\Tron;
use App\Models\User;
use App\Util\TelegramTools;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\CodeCoverage\Report\PHP;

class Callback
{
    /**
     * Bot
     */
    protected $bot;

    /**
     * 触发用户
     */
    protected $User;

    /**
     * 触发用户TG信息
     */
    protected $triggerUser;

    /**
     * 回调
     */
    protected $callback;

    /**
     * 回调数据内容
     */
    protected $callbackData;

    /**
     * 消息会话 ID
     */
    protected $ChatID;

    /**
     * 触发源信息 ID
     */
    protected $MessageID;

    /**
     * 源消息是否可编辑
     */
    protected $AllowEditMessage;

    /**
     * @param \Telegram\Bot\Api $api
     * @param \Telegram\Bot\Objects\CallbackQuery $callback
     */
    public function __construct($api, $callback)
    {
        $this->bot = $api;
        $this->triggerUser = [
            'id' => $callback->getFrom()->getId(),
            'name' => $callback->getFrom()->getFirstName() . ' ' . $callback->getFrom()->getLastName(),
            'username' => $callback->getFrom()->getUsername(),
        ];
        $this->User = User::getUser($this->triggerUser['id']);
        $this->ChatID = $callback->getMessage()->getChat()->getId();
        $this->Callback = $callback;
        $this->MessageID = $callback->getMessage()->getMessageId();
        $this->CallbackData = $callback->getData();
        $this->AllowEditMessage = time() < $callback->getMessage()->getDate() + 172800;
        $callback_data = $callback->getData();
        $call = explode('.', $callback_data);
        switch ($call[0]) {
            case 'shop':
                break;
            case 'user':
                $this->userCallback($call, $callback);
        }
        Log::info("callback_data:" . $callback_data);
    }

    /**
     * @param string $key
     * @param \Telegram\Bot\Objects\CallbackQuery $callback
     */
    public function userCallback($key, $callback)
    {
        switch ($key[1]) {
            case 'tron':
                // 添加缓存
                Cache::put($this->ChatID, $key, now()->addMinutes(5));
                $this->replyWithMessage([
                    'text' => "请输入你的TRC20地址",
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null
                ]);
                break;
            case 'show_tron':
                $formatText = '*所有TRC20地址:*' . PHP_EOL;
                $Keyboard = [];
                foreach ($this->User->tron as $key => $item) {
                    array_push($Keyboard, [
                        [
                            'text' => $item->address,
                            'callback_data' => "user.edit_tron." . $item->address
                        ]
                    ]);
                }
                $this->replyWithMessage([
                    'text' => $formatText,
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(['inline_keyboard' => $Keyboard])
                ]);
                break;
            case 'edit_tron':
                $formatText = '*地址:*' . '`' . $key[2] . '`' . PHP_EOL;
                $Keyboard = [
                    [
                        [
                            'text' => "点击删除",
                            'callback_data' => "user.delete_tron." . $key[2]
                        ]
                    ]
                ];
                $this->replyWithMessage([
                    'text' => $formatText,
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(['inline_keyboard' => $Keyboard])
                ]);
                break;
            case 'delete_tron':
                $this->User->tron()->where('address', $key[2])->delete();
                $this->replyWithMessage([
                    'text' => "删除成功!",
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                ]);
                break;
            case 'secret':
                $text = '您的AppID: `' . $this->User->app_id . '`' . PHP_EOL;
                $text .= '您的AppSecret: `' . $this->User->app_secret . '`' . PHP_EOL . PHP_EOL;
                $text .= '*请妥善保管，勿泄漏给他人！*';
                $keyboard = [
                    [
                        [
                            'text' => 'SSPanel-Metron对接教程',
                            'url' => "https://mega.nz/file/L911nIbb#rRUxarIU6tZ8bOKrvv-mirIZAWxOPwp8E3YIvuomoJo"
                        ]
                    ],
                    [
                        [
                            'text' => 'SSPanel-Malio对接教程',
                            'url' => "https://mega.nz/file/DldSzTSY#9xHvVeFugod-khmWf1zJ23O-__mXGNVW6etua1R7Q_c"
                        ]
                    ],
                    [
                        [
                            'text' => 'SSPanel-UIM对接教程',
                            'url' => "https://mega.nz/file/mwciULYC#TvSHWwsCqd3Iil5d0Spy7507ItCgUkoMyK29uThhPNA"
                        ]
                    ],
                    [
                        [
                            'text' => 'V2board对接教程',
                            'url' => "https://mega.nz/file/Kh8AALzI#a_snZ4mAUhZcuZgPhjrDN2CXrTZvABRjuuLsuSgb-48"
                        ]
                    ],
                    [
                        [
                            'text' => 'WHCMS',
                            'url' => "https://mega.nz/file/7p13XJKR#Een7Lkg_8gbf572koPhfdVh_-W93OFwr2hFYQVYM76s"
                        ]
                    ],
                    [
                        [
                            'text' => '独角发卡对接教程',
                            'url' => "https://mega.nz/file/ytFEQJgJ#ZVhNsDiBEw8poXlU0PQCbtpW49hfeVCmBOhMo0G7mJY"
                        ]
                    ],
                    [
                        [
                            'text' => '风铃发卡对接教程',
                            'url' => "https://mega.nz/file/D18XlARY#IjTwJpoc9HhD7hfJBnMfOrhTfqPPxoG5IbzLVGablaM"
                        ]
                    ],
                ];
                $this->replyWithMessage([
                    'text' => $text,
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
                ]);
                break;
            case 'usdt_rate':
                // 添加缓存
                $this->replyWithMessage([
                    'text' => "当前账号USDT费率是 - *".$this->User->usdt_rate."*".PHP_EOL.PHP_EOL.'请输入 `/set_rate 倍率` 设置倍率,范围在1-10以内，最多两位小数',
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null
                ]);
                break;
        }
    }

    /**
     *
     * 响应回调查询 | 默认已添加 chat_id 和 message_id
     *
     * @param array $sendMessage
     *
     * @return void
     */
    public function replyWithMessage(array $sendMessage): void
    {
        $sendMessage = array_merge(
            [
                'chat_id' => $this->ChatID,
                'message_id' => $this->MessageID,
            ],
            $sendMessage
        );
        if ($this->AllowEditMessage) {
            TelegramTools::SendPost('editMessageText', $sendMessage);
        } else {
            $this->bot->sendMessage($sendMessage);
        }
    }
}

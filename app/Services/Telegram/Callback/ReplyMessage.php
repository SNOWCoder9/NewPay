<?php


namespace App\Services\Telegram\Callback;


use App\Models\Tron;
use App\Models\User;
use App\Util\TronApi;
use Illuminate\Support\Facades\Cache;

class ReplyMessage
{
    /**
     * Bot
     */
    protected $bot;

    /**
     *  @var \App\Models\User
     * 触发用户
     */
    protected $User;

    /**
     * 触发用户TG信息
     */
    protected $triggerUser;

    /**
     * 消息会话 ID
     */
    protected $ChatID;

    /**
     * 触发源信息 ID
     */
    protected $MessageID;

    protected $Message;

    /**
     * @param \Telegram\Bot\Api $bot
     * @param \Telegram\Bot\Objects\Message $message
     */
    public function __construct($bot, $message)
    {
        $this->bot = $bot;
        $this->triggerUser = [
            'id' => $message->getFrom()->getId(),
            'name' => $message->getFrom()->getFirstName() . ' ' . $message->getFrom()->getLastName(),
            'username' => $message->getFrom()->getUsername(),
        ];
        $this->User = User::getUser($this->triggerUser['id']);
        $this->ChatID = $message->getChat()->getId();
        $this->Message = $message;
        $this->MessageID = $message->getMessageId();
    }

    public function userMessage($key)
    {
        $text = $this->Message->getText();
        switch ($key) {
            case 'tron':
                $count = Tron::query()->where('user_id', $this->User->id)->count();
                if ($count >= $this->User->count) {
                    Cache::forget($this->ChatID);
                    $this->sendMessage("你的授权码只能绑定" . $this->User->count . "个TRC20地址！");
                } else {
                    // 验证波场地址
                    $res = TronApi::validateAddress($text);
                    if ($res['result']){
                        if ($this->User->tron()->where('address', $text)->exists()){
                            return $this->sendMessage("该TRC20地址已绑定其他授权，请从新输入！");
                        }
                        $this->User->tron()->create(['address' => $text]);
                        Cache::forget($this->ChatID);
                        $this->sendMessage("绑定成功");
                    } else {
                        $this->sendMessage("TRC20地址不正确，请填写正确的地址");
                    }
                }
                break;
            case 'usdt_rate':
                Cache::forget($this->ChatID);
                if (!is_numeric($text) || $text < 1 || $text > 10){
                    $this->sendMessage("请输入正确的数字,范围在1-10以内，最多两位小数");
                } else {
                    $rate = (float)sprintf('%.2f', $text);
                    $this->User->update(['usdt_rate' => $rate]);
                    $this->sendMessage("修改成功");
                }
                break;
        }
    }

    /**
     * @param $text
     * @return \Telegram\Bot\Objects\Message
     */
    public function sendMessage($text)
    {
        return $this->bot->sendMessage([
            'chat_id' => $this->ChatID,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => false,
            'reply_to_message_id' => null,
            'reply_markup' => null
        ]);
    }

    /**
     * @param $text
     * @return \Telegram\Bot\Objects\Message
     */
    public function replyWithMessage($text)
    {
        return $this->bot->sendMessage([
            'chat_id' => $this->ChatID,
            'message_id' => $this->MessageID,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => false,
            'reply_to_message_id' => null,
            'reply_markup' => null
        ]);
    }
}

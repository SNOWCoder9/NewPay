<?php

namespace App\Observers;

use App\Enum\DomainEnum;
use App\Jobs\TelegramPush;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class DomainObserver
{
    public function updated(Domain $domain): void
    {
        $changes = $domain->getChanges();
        if (Arr::exists($changes, 'status')) {
            if ($user = User::query()->find($domain->user_id)){
                $text = '*💡域名审核通知*' . PHP_EOL;
                $text .= "您的域名【{$domain->domain}】审核".DomainEnum::list[$changes['status']].",感谢使用!" . PHP_EOL;
                $keyboard = [];
                $chat_id = $user->telegram_id;
                TelegramPush::dispatch(compact('text', 'keyboard', 'chat_id'));
                $domain_list = Domain::query()->where('status', DomainEnum::SUCCESS)->pluck('domain')->toArray();
                Cache::forever('domain_white_list', $domain_list);
            }
        }
    }

}

<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Order extends Model
{
	use HasDateTimeFormatter;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopeNoWithdraw($query)
    {
        return $query->whereNull('settle_no')->where('withdraw', 0);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', '>', 1);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', date('Y-m-d', time()));
    }

    public function scopeDateQuery($query, $key)
    {
        switch ($key) {
            case 'sub_day':
                $between = [Carbon::now()->subDay()->startOfDay(), Carbon::now()->subDay()->endOfDay()];
                break;
            case 'sub_month':
                $between = [Carbon::now()->subMonth()->firstOfMonth(), Carbon::now()->subMonth()->endOfMonth()];
                break;
            case 'month':
                $between = [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
                break;
            case 'week':
                $between = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
                break;
            case 'today':
                $between = [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()];
                break;
            default:
                $between = [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()];
        }

        return $query->whereBetween('created_at', $between);
    }

    public function scopeByToken($query, $token)
    {
        return $query->where('token', $token);
    }

    public function scopeGetSettleData($query, $user_id, $date)
    {
        return $query->where('user_id', $user_id)
            ->where('status', '>', 1)
            ->where('type', '>',1)
            ->where('withdraw', 0)
            ->where('created_at', '<=', $date)
            ->whereNull('settle_no');
    }
}

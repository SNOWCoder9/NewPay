<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'settlement';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDeal($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', date('Y-m-d', time()));
    }
}

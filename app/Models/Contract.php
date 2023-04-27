<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'contract';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'token', 'token');
    }
}

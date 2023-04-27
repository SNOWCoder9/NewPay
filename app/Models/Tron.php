<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Tron extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'tron';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

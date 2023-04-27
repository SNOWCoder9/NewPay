<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
	use HasDateTimeFormatter;

    protected $casts = [
        'config' => 'json'
    ];
}

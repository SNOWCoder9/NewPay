<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'shop';
    protected $casts = [
        'payment_ids' => 'json'
    ];
}

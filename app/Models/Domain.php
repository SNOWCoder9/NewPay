<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'domain';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

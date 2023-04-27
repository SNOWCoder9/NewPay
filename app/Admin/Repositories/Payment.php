<?php

namespace App\Admin\Repositories;

use App\Models\Payment as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Payment extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}

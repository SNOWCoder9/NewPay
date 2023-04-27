<?php

namespace App\Admin\Repositories;

use App\Models\Contract as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Contract extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}

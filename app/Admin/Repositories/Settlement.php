<?php

namespace App\Admin\Repositories;

use App\Models\Settlement as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Settlement extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}

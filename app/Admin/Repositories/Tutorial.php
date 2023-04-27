<?php

namespace App\Admin\Repositories;

use App\Models\Tutorial as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Tutorial extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}

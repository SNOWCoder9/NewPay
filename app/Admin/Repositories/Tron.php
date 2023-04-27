<?php

namespace App\Admin\Repositories;

use App\Models\Tron as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Tron extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}

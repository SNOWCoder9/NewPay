<?php

namespace App\Admin\Repositories;

use App\Models\Shop as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Shop extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}

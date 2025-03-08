<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $attribute)
    {
        return $this->model->create($attribute);
    }

    public function update($id, array $attribute)
    {
        return $this->model->findOrFail($id)->update($attribute);
    }

    public function updateReturn($id, array $attributes)
    {
        $model = $this->model->findOrFail($id);
        $model->update($attributes);
        return $model;
    }

    public function findBy($param = [], $columns = '*', $relations = [])
    {
        $eloquent = $this->model->select($columns)->with($relations)->where($param);
        return $eloquent->first();
    }
}
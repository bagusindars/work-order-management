<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository
{
    public function __construct(Model $model = new User)
    {
        parent::__construct($model);
    }

    public function getOperator($param = [], $column = "*", $relations = [])
    {
        return User::query()
            ->with($relations)
            ->select($column)
            ->where($param)
            ->whereHas('roles', function ($query) {
                return $query->where('key', 'operator');
            })->get();
    }
}

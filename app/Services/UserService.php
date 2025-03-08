<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    public function __construct(
        protected UserRepository $user_repository,
    ) {
        //
    }

    public function getOperator($param = [], $column = "*", $relations = [])
    {
        return $this->user_repository->getOperator(
            param: $param,
            column: $column,
            relations: $relations,
        );
    }

    public function findBy($param = [], $column, $relations)
    {
        return $this->user_repository->findBy(
            param: $param,
            columns: $column,
            relations: $relations
        );
    }
}
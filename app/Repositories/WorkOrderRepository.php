<?php

namespace App\Repositories;

use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Model;

class WorkOrderRepository extends BaseRepository
{
    public function __construct(Model $model = new WorkOrder)
    {
        parent::__construct($model);
    }
}

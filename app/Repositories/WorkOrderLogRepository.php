<?php

namespace App\Repositories;

use App\Models\WorkOrderLog;
use Illuminate\Database\Eloquent\Model;

class WorkOrderLogRepository extends BaseRepository
{
    public function __construct(Model $model = new WorkOrderLog)
    {
        parent::__construct($model);
    }
}

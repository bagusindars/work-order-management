<?php

namespace App\Models;

use App\Enums\WorkOrderStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    protected $guarded = [];

    public $casts = [
        'status' => WorkOrderStatusEnum::class
    ];

    protected function restQuantity(): Attribute
    {
       return Attribute::make(
            get: fn (mixed $value, $attribute) => $attribute['quantity'] - $attribute['completed_quantity']
        );
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WorkOrderLog::class, 'work_order_id', 'id')->latest();
    }
}

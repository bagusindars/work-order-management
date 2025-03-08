<?php

namespace App\Services;

use App\Enums\WorkOrderStatusEnum;
use App\Models\WorkOrder;
use App\Repositories\UserRepository;
use App\Repositories\WorkOrderLogRepository;
use App\Repositories\WorkOrderRepository;
use App\Utilities\Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorkOrderService
{
    public function __construct(
        protected WorkOrderRepository $work_order_repository,
        protected UserRepository $user_repository,
        protected WorkOrderLogRepository $work_order_log_repository,
    ) {
        //
    }

    public function findBy($param = [], $column = "*", $relations = [])
    {
        return $this->work_order_repository->findBy(
            param: $param,
            columns: $column,
            relations: $relations
        );
    }

    public function create($product_name, $quantity, $due_date, $user_id, $status)
    {
        try {
            DB::beginTransaction();

            $code = 'WO-' . date('ymd') . '-' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

            if (!$this->isWorkOrderAssignedToOperator(user_id: $user_id)) {
                return Helpers::responseCms(status: false, message: 'Only operator user can be assigned');
            }

            $work_order = $this->work_order_repository->create(
                attribute: [
                    'code' => $code,
                    'product_name' => $product_name,
                    'quantity' => $quantity,
                    'due_date' => $due_date,
                    'user_id' => $user_id,
                    'status' => $status,
                ]
            );

            if ($status === 'completed' || $status === 'canceled') {
                $this->work_order_log_repository->create(
                    attribute: [
                        'quantity' => $quantity,
                        'duration_in_minute' => 0,
                        'note' => null,
                        'status' => $status,
                        'work_order_id' => $work_order->id,
                        'user_id' => $user_id,
                    ]
                );
            }

            DB::commit();
            return Helpers::responseCms(status: true, message: 'Work order created successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return Helpers::responseCms(status: false, message: $th->getMessage());
        }
    }

    public function update($work_order_id, $user_id, $status)
    {
        try {
            DB::beginTransaction();

            if (!auth()->user()->is_production_manager) {
                return Helpers::responseCms(status: false, message: 'Only production manager can access this');
            }

            $work_order = $this->work_order_repository->findBy(
                param: ['id' => $work_order_id],
                relations: [
                    'logs' => function($q) {
                        return $q->where('status', 'completed');
                    }
                ]
            );

            if (!$work_order) {
                return Helpers::responseCms(status: false, message: 'Work order not found');
            }

            if (!$this->isStatusEditable(work_order: $work_order)) {
                return Helpers::responseCms(status: false, message: "Status is not allowed");
            }

            if (!$this->isWorkOrderAssignedToOperator(user_id: $user_id)) {
                return Helpers::responseCms(status: false, message: "Only operator user can be assigned");
            }

            $work_order->update([
                'user_id' => $user_id,
                'status' => $status
            ]);

            if ($status === 'completed' || $status === 'canceled') {
                $this->work_order_log_repository->create(
                    attribute: [
                        'quantity' => $quantity,
                        'duration_in_minute' => 0,
                        'note' => null,
                        'status' => $status,
                        'work_order_id' => $work_order->id,
                        'user_id' => $user_id,
                    ]
                );
            }

            DB::commit();
            return Helpers::responseCms(status: true, message: "Work order updated successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            return Helpers::responseCms(status: false, message: $th->getMessage());
        }
    }

    public function createLog($work_order_id, $quantity, $note)
    {
        try {
            DB::beginTransaction();

            if (!auth()->user()->is_operator) {
                return Helpers::responseCms(status: false, message: "Only for operator can access this operation");
            }

            $work_order = $this->work_order_repository->findBy(
                param: ['id' => $work_order_id],
                relations: [
                    'logs' => function ($q) {
                        $q->whereIn('status', ['in_progress', 'completed']);
                    }
                ]
            );

            if (!$work_order) {
                return Helpers::responseCms(status: false, message: "Work order not found");
            }

            if (!$this->isStatusEditable(work_order: $work_order)) {
                return Helpers::responseCms(status: false, message: "Status is not allowed");
            }

            $proceded_quantity = $work_order->logs->sum('quantity');

            if ($quantity + $proceded_quantity > $work_order->quantity) {
                return Helpers::responseCms(status: false, message: "Over completed/in progress quantity. Rest quantity is " . $work_order->quantity - $proceded_quantity);
            }

            $work_order->update([
                'status' => WorkOrderStatusEnum::IN_PROGRESS->value,
            ]);

            $this->work_order_log_repository->create(
                attribute: [
                    'quantity' => $quantity,
                    'duration_in_minute' => 0,
                    'note' => $note,
                    'status' => WorkOrderStatusEnum::IN_PROGRESS->value,
                    'work_order_id' => $work_order->id,
                    'user_id' => auth()->user()->id,
                ]
            );

            DB::commit();
            return Helpers::responseCms(status: true, message: "Work order updated successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            return Helpers::responseCms(status: false, message: $th->getMessage());
        }
    }

    public function completeLog(int $id)
    {
        try {
            DB::beginTransaction();

            $log = $this->work_order_log_repository->findBy(
                param: ['id' => $id],
                relations: [
                    'work_orders.logs' => function ($q) {
                        $q->where('status', 'completed');
                    }
                ]
            );

            if (!$log) {
                return Helpers::responseCms(status: false, message: "Log not found");
            }

            if ($log->user_id != auth()->user()->id) {
                return Helpers::responseCms(status: false, message: "Only same log creator that can edit");
            }

            if ($log->status === WorkOrderStatusEnum::COMPLETED) {
                return Helpers::responseCms(status: false, message: "Cannot update the log. It's already completed");
            }

            if (!$this->isStatusEditable(work_order: $log->work_orders)) {
                return Helpers::responseCms(status: false, message: "Status is not allowed");
            }

            $log->update([
                'status' => WorkOrderStatusEnum::COMPLETED->value,
                'duration_in_minute' => round(Carbon::parse($log->created_at)->diffInMinutes(now()))
            ]);

            $completed_quantity = $log->work_orders->logs->sum('quantity') + $log->quantity;

            if ($completed_quantity === $log->work_orders->quantity) {
                $log->work_orders->update([
                    'status' => WorkOrderStatusEnum::COMPLETED->value
                ]);
            }

            DB::commit();
            return Helpers::responseCms(status: true, message: "Log updated successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            return Helpers::responseCms(status: false, message: $th->getMessage());
        }
    }


    public function isWorkOrderAssignedToOperator($user_id)
    {
        $user = $this->user_repository->findBy(
            param: ['id' => $user_id],
            relations: ['roles']
        );

        if ($user?->roles?->key === 'operator') return true;

        return false;
    }

    public function isStatusEditable(WorkOrder $work_order)
    {
        if ($work_order->status === 'completed') return false;

        if ($work_order->status === 'canceled') return false;

        return true;
    }
}

<?php

namespace App\Livewire\Reports;

use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class OperatorRecap extends Component
{
    use WithPagination;

    public int $perPage = 10;

    public array $sortBy = ['column' => 'users.name', 'direction' => 'asc'];

    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'product_name', 'label' => 'Product Name'],
            ['key' => 'total_quantity_completed', 'label' => 'Completed Quantity', 'class' => 'text-center'],
        ];
    }

    public function work_orders()
    {
        return WorkOrder::select([
            'users.id as user_id',
            'work_orders.product_name',
            'users.name',
            DB::raw('COALESCE(SUM(CASE WHEN work_order_logs.user_id = users.id  THEN work_order_logs.quantity ELSE 0  END), 0) as total_quantity_completed')
        ])
            ->leftJoin('work_order_logs', function ($join) {
                $join->on('work_orders.id', '=', 'work_order_logs.work_order_id')
                    ->where('work_order_logs.status', '=', 'completed');
            })
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'work_order_logs.user_id')
                    ->orOn('users.id', '=', 'work_orders.user_id');
            })
            ->groupBy('users.id', 'work_orders.product_name', 'users.name')
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.reports.operator-recap', [
            'headers' => $this->headers(),
            'work_orders' => $this->work_orders(),
        ])->layoutData([
            'title' => 'Operator Recap',
            'breadcrumbs' => [
                [
                    'route' => route('reports.operator-recap'),
                    'label' => 'Operator Recap'
                ],
            ]
        ]);
    }
}

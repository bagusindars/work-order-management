<?php

namespace App\Livewire\Reports;

use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class WorkOrderRecap extends Component
{
    use WithPagination;

    public int $perPage = 10;

    public array $sortBy = ['column' => 'work_orders.product_name', 'direction' => 'desc'];

    public function headers(): array
    {
        return [
            ['key' => 'product_name', 'label' => 'Product Name'],
            ['key' => 'quantity', 'label' => 'Pending', 'class' => 'text-center', 'sortable' => false],
            ['key' => 'in_progress_count', 'label' => 'In Progress', 'class' => 'text-center'],
            ['key' => 'completed_count', 'label' => 'Completed', 'class' => 'text-center'],
            ['key' => 'canceled_count', 'label' => 'Canceled', 'class' => 'text-center'],
        ];
    }

    public function workOrders()
    {
        return WorkOrder::select([
            'work_orders.product_name',
            'work_orders.quantity',
            DB::raw("
                    SUM(CASE WHEN work_order_logs.status = 'in_progress' THEN work_order_logs.quantity ELSE 0 END) as in_progress_count,
                    SUM(CASE WHEN work_order_logs.status = 'completed' THEN work_order_logs.quantity ELSE 0 END) as completed_count,
                    SUM(CASE WHEN work_order_logs.status = 'canceled' THEN work_order_logs.quantity ELSE 0 END) as canceled_count
                ")
        ])
            ->leftJoin('work_order_logs', 'work_orders.id', '=', 'work_order_logs.work_order_id')
            ->groupBy('work_orders.product_name', 'work_orders.quantity')
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.reports.work-order-recap', [
            'headers' => $this->headers(),
            'work_orders' => $this->workOrders(),
        ])->layoutData([
            'title' => 'Work Order Recap',
            'breadcrumbs' => [
                [
                    'route' => route('reports.work-order-recap'),
                    'label' => 'Work Order Recap'
                ],
            ]
        ]);
    }
}

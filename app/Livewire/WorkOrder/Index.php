<?php

namespace App\Livewire\WorkOrder;

use App\Enums\WorkOrderStatusEnum;
use App\Models\WorkOrder;
use App\Services\UserService;
use App\Services\WorkOrderService;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Index extends Component
{
    use Toast;

    use WithPagination;

    public $is_pm;

    public $work_order_selected = [];

    public $operator;

    public $selected_operator;

    public $status;

    public $quantity;

    public $note;

    public array $filter = [];

    public int $perPage = 10;

    public bool $workOrderModal = false;

    protected UserService $user_service;

    protected WorkOrderService $work_order_service;

    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];

    public bool $drawerFilter = false;

    public function boot(
        WorkOrderService $work_order_service,
        UserService $user_service,
    ) {
        $this->work_order_service = $work_order_service;
        $this->user_service = $user_service;
    }

    public function mount()
    {
        $this->operator = $this->user_service->getOperator();
        $this->is_pm = auth()->user()->is_production_manager;
    }

    public function headers(): array
    {
        return [
            ['key' => 'code', 'label' => 'Code'],
            ['key' => 'product_name', 'label' => 'Product Name', 'class' => 'w-24'],
            ['key' => 'quantity', 'label' => 'Quantity'],
            ['key' => 'due_date', 'label' => 'Due Date'],
            ['key' => 'users_name', 'label' => 'Operator', 'sortBy' => 'users_name'],
            ['key' => 'status', 'label' => 'Status'],
        ];
    }

    public function workOrders()
    {
        return WorkOrder::query()
            ->withAggregate('users', 'name')
            ->when(!$this->is_pm, function ($query) {
                return $query->where('user_id', auth()->user()->id);
            })
            ->when($this->filter['status'] ?? false, function($query) {
                return $query->where('status', $this->filter['status']);
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.work-order.index', [
            'headers' => $this->headers(),
            'work_orders' => $this->workOrders(),
            'work_order_enums' => $this->is_pm ? WorkOrderStatusEnum::except([]) : WorkOrderStatusEnum::except(['pending', 'canceled'])
        ])->layoutData([
            'title' => 'Work Order',
            'breadcrumbs' => [
                [
                    'route' => route('work-order.create'),
                    'label' => 'Work Order'
                ],
            ]
        ]);
    }

    public function modalUpdateStatus($id)
    {
        $work_order = $this->work_order_service->findBy(
            param: ['id' => $id],
            relations: ['users', 'logs.users.roles']
        );

        if (!$work_order) {
            return $this->error('Work order not found');
        }

        $this->reset(['status', 'quantity', 'note', 'selected_operator', 'work_order_selected']);
        $this->workOrderModal = true;
        $this->selected_operator = $work_order->user_id;
        $this->work_order_selected = $work_order;
    }

    public function submitUpdateWorkOrder()
    {
        $this->validate();

        if ($this->is_pm) {
            $payload = $this->work_order_service->update(
                work_order_id: $this->work_order_selected['id'],
                status: $this->status,
                user_id: $this->selected_operator
            );
        } else {
            $payload = $this->work_order_service->createLog(
                work_order_id: $this->work_order_selected['id'],
                note: $this->note,
                quantity: $this->quantity,
            );
        }

        if ($payload['status']) {
            $this->workOrderModal = false;
            return $this->success(title: $payload['message']);
        }

        return $this->error(title: $payload['message']);
    }

    public function updateLog($id)
    {
        $payload = $this->work_order_service->completeLog(id: $id);

        if ($payload['status']) {
            $this->work_order_selected = $this->refreshDetailWorkOrder();
            return $this->success(title: $payload['message']);
        }

        $this->error(title: $payload['message']);
    }

    public function refreshDetailWorkOrder()
    {
        $work_order = $this->work_order_service->findBy(
            param: ['id' => $this->work_order_selected['id']],
            relations: ['users', 'logs.users.roles']
        );

        return $work_order;
    }

    public function clearFilter()
    {
        $this->reset(['filter']);
    }

    public function submitFilter()
    {
        $this->drawerFilter = false;
    }

    protected function rules()
    {
        $is_pm = $this->is_pm;

        return [
            'status' => [$is_pm ? 'required' : 'nullable', 'string', Rule::enum(WorkOrderStatusEnum::class)],
            'selected_operator' => [$is_pm ? 'required' : 'nullable', 'numeric', 'exists:users,id'],
            'note' => [$is_pm ? 'nullable' : 'required', 'string', 'max:255'],
            'quantity' => [$is_pm ? 'nullable' : 'required', 'numeric', 'min:1'],
        ];
    }
}

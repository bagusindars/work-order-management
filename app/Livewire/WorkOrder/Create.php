<?php

namespace App\Livewire\WorkOrder;

use App\Enums\WorkOrderStatusEnum;
use App\Services\UserService;
use App\Services\WorkOrderService;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

class Create extends Component
{
    use Toast;

    public $product_name;

    public $quantity;

    public $due_date;

    public $operator;

    public $status;

    public $selected_operator;

    protected UserService $user_service;

    protected WorkOrderService $work_order_service;

    public function boot(
        UserService $user_service,
        WorkOrderService $work_order_service
    ) {
        $this->user_service = $user_service;
        $this->work_order_service = $work_order_service;
    }

    public function mount()
    {
        $this->operator = $this->user_service->getOperator();
    }

    public function render()
    {
        return view('livewire.work-order.create')->layoutData([
            'title' => 'Add Work Order',
            'breadcrumbs' => [
                [
                    'route' => route('work-order.index'),
                    'label' => 'Work Order'
                ],
                [
                    'route' => route('work-order.create'),
                    'label' => 'Add Work Order'
                ],
            ]
        ]);
    }

    public function submit()
    {
        $this->validate();

        $payload = $this->work_order_service->create(
            product_name: $this->product_name,
            quantity: $this->quantity,
            due_date: $this->due_date,
            user_id: $this->selected_operator,
            status: $this->status
        );

        if ($payload['status']) {
            return $this->success(title: $payload['message'], redirectTo: route('work-order.index'));
        }

        $this->error(title: $payload['message']);
    }

    protected function rules()
    {
        return [
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'due_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'selected_operator' => ['required', 'numeric', 'exists:users,id'],
            'status' => ['required', 'string', Rule::enum(WorkOrderStatusEnum::class)]
        ];
    }
}

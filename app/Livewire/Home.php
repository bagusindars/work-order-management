<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use Illuminate\Support\Facades\Concurrency;
use Livewire\Component;

class Home extends Component
{
    public $operator_count;

    public $work_order_count;

    public $log_completed;

    public function mount()
    {
        // i don't know if access it direct to service/function it won't work
        [$this->operator_count, $this->work_order_count, $this->log_completed] = Concurrency::run([
            fn () => User::whereHas('roles', function ($query) {
                    return $query->where('key', 'operator');
                })->count(),
            fn () => WorkOrder::count(),
            fn () => WorkOrderLog::where('status', 'completed')->sum('quantity')
        ]);
    }

    public function render()
    {
        return view('livewire.home')->layoutData([
            'title' => 'Welcome',
            'breadcrumbs' => []
        ]);
    }
}

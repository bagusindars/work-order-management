<div>
    @if (session()->has('error'))
        <x-alert title="Warning!" description="{{ session()->get('error') }}" class="alert-error mb-5"
            icon="o-exclamation-triangle" dismissible />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <x-stat title="Operator" description="Total of operator" value="{{ $operator_count }}" icon="o-user" />
        <x-stat title="Work Order" description="All work order" value="{{ $work_order_count }}" icon="o-archive-box" />
        <x-stat title="Completed" description="Completed quantity" value="{{ $log_completed }}" icon="o-check" />
    </div>
</div>

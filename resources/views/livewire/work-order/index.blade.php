<div>
    @if ($is_pm)
        <div class="flex items-center justify-end gap-2">
            <x-button label="Filter" class="btn-sm" badge="{{ count($filter) ?: '' }}"
                wire:click="$toggle('drawerFilter')" />
            <x-button label="Add Work Order" icon="s-plus" link="{{ route('work-order.create') }}"
                class="btn btn-primary btn-sm" />
        </div>
    @endif

    <x-drawer wire:model="drawerFilter" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div>
            <label class="font-semibold text-sm">Status</label>
            <x-select placeholder="Select status" :options="\App\Enums\WorkOrderStatusEnum::options()" option-value="value" option-label="label"
                wire:model="filter.status" class=" mt-2" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" spinner wire:click="clearFilter" />
            <x-button label="Done" icon="o-check" class="btn-primary" spiiner wire:click="submitFilter" spinner />
        </x-slot:actions>
    </x-drawer>

    <div class="bg-white rounded-md shadow-md mt-5">
        <x-table :headers="$headers" :rows="$work_orders" :sort-by="$sortBy" with-pagination per-page="perPage"
            :per-page-values="[10, 50, 100]" class="border border-dotted">
            @scope('cell_due_date', $work_orders)
                <p>{{ \App\Utilities\Helpers::formatDate($work_orders->due_date) }}</p>
            @endscope
            @scope('cell_status', $work_orders)
                <x-badge :value="$work_orders->status->label()" class="text-xs" />
            @endscope
            @scope('actions', $work_orders)
                <x-button icon="o-pencil" wire:click="modalUpdateStatus({{ $work_orders->id }})" spinner class="btn-sm" />
            @endscope
            <x-slot:empty>
                <p class="font-semibold text-base">No data found. Try to broaden your search.</p>
            </x-slot:empty>
        </x-table>
    </div>

    <x-modal wire:model="workOrderModal" title="Work Order" separator>
        @if ($work_order_selected)
            <p class="font-semibold mb-2">Information</p>
            <p class="text-sm">Code : {{ $work_order_selected->code }}</p>
            <p class="text-sm">Product Name : {{ $work_order_selected->product_name }}</p>
            <p class="text-sm">Quantity :
                {{ $work_order_selected->logs->where('status', 'completed')->sum('quantity') }} /
                {{ $work_order_selected->quantity }}</p>
            <p class="text-sm">Due Date : {{ \App\Utilities\Helpers::formatDate($work_order_selected->due_date) }}</p>
            <p class="text-sm">Operator : {{ $work_order_selected->users->name }}</p>
            <p class="text-sm">Current Status : {{ $work_order_selected->status }}</p>
            <p class="font-semibold my-3">Log</p>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Created at</th>
                            <th>Created By</th>
                            <th>Role</th>
                            <th>Quantity</th>
                            <th>Duration (minute)</th>
                            <th>Note</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($work_order_selected->logs as $log)
                            <tr>
                                <td class="w-[200px]">
                                    {{ \App\Utilities\Helpers::formatDate($work_order_selected->created_at, true) }}
                                </td>
                                <td>{{ $log->users->name }}</td>
                                <td>{{ $log->users->roles->label }}</td>
                                <td>{{ $log->quantity }}</td>
                                <td class="text-center">{{ $log->duration_in_minute }}</td>
                                <td>{{ $log->note ?? '-' }}</td>
                                <td><x-badge value="{{ $log->status }}" /></td>
                                <td>
                                    @if ($log->user_id === auth()->user()->id && $log->status->value === 'in_progress')
                                        <x-dropdown class="btn-sm">
                                            <x-menu-item title="Completed"
                                                wire:click.stop="updateLog({{ $log->id }})"
                                                spinner="updateLog({{ $log->id }})" />
                                        </x-dropdown>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td>
                                    <p class="text-center font-semibold">No log</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($work_order_selected->status->value !== 'completed' && $work_order_selected->status->value !== 'canceled')
                <p class="font-semibold my-3">{{ $is_pm ? 'Update Work Order' : 'Add Log' }}</p>
                @if ($is_pm)
                    <div class="mt-2">
                        <label class="font-semibold text-sm" for="due_date">Operator</label>
                        <x-select placeholder="Select operator" :options="$operator" wire:model="selected_operator"
                            class=" mt-2" />
                    </div>
                    <div class="mt-2">
                        <label class="font-semibold text-sm" for="status">Status</label>
                        <x-select placeholder="Select status" :options="$work_order_enums" option-value="value"
                            option-label="label" wire:model="status" class=" mt-2" />
                    </div>
                @else
                    <div class="mt-2">
                        <label class="font-semibold text-sm" for="note">Quantity</label>
                        <x-input wire:model="quantity" type="number" class=" mt-2" />
                    </div>
                    <div class="mt-2">
                        <label class="font-semibold text-sm" for="note">Note</label>
                        <x-input wire:model="note" class=" mt-2" />
                    </div>
                @endif
                <x-slot:actions>
                    <x-button label="Cancel" @click="$wire.workOrderModal = false" />
                    <x-button label="Confirm" wire:click="submitUpdateWorkOrder" spinner class="btn-primary" />
                </x-slot:actions>
            @else
                <p class="text-error">Cannot update for status {{ $work_order_selected->status->label() }}</p>
            @endif
        @endif
    </x-modal>
</div>

<div>
    <div class="bg-white rounded-md shadow-md mt-5">
        <x-table :headers="$headers" :rows="$work_orders" :sort-by="$sortBy" with-pagination per-page="perPage"
            :per-page-values="[10, 50, 100]" class="border border-dotted">
            @scope('cell_quantity', $work_orders)
                <p>{{ $work_orders->quantity - ($work_orders->in_progress_count + $work_orders->completed_count + $work_orders->canceled_count) }}</p>
            @endscope
            <x-slot:empty>
                <p class="font-semibold text-base">No data found. Try to broaden your search.</p>
            </x-slot:empty>
        </x-table>
    </div>
</div>

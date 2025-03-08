<div>
    <div class="bg-white rounded-md shadow-md mt-5">
        <x-table :headers="$headers" :rows="$work_orders" :sort-by="$sortBy" with-pagination per-page="perPage"
            :per-page-values="[10, 50, 100]" class="border border-dotted">
            @scope('cell_product_name', $work_orders)
                <p>{{ $work_orders->product_name}}</p>
            @endscope
            <x-slot:empty>
                <p class="font-semibold text-base">No data found. Try to broaden your search.</p>
            </x-slot:empty>
        </x-table>
    </div>
</div>

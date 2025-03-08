<div>
    <div class="bg-white rounded-md shadow-md mt-5 p-5">
        <x-form wire:submit="submit" no-separator>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="mt-2">
                    <label class="font-semibold text-sm" for="product_name">Product Name</label>
                    <x-input wire:model="product_name" class=" mt-2" />
                </div>
                <div class="mt-2">
                    <label class="font-semibold text-sm" for="quantity">Quantity</label>
                    <x-input type="number" wire:model="quantity" class=" mt-2" />
                </div>
                <div class="mt-2">
                    <label class="font-semibold text-sm" for="due_date">Due Date</label>
                    <x-datetime wire:model="due_date" class=" mt-2" />
                </div>
                <div class="mt-2">
                    <label class="font-semibold text-sm" for="due_date">Operator</label>
                    <x-select placeholder="Select operator" :options="$operator" wire:model="selected_operator"
                        class=" mt-2" />
                </div>
                <div class="mt-2">
                    <label class="font-semibold text-sm" for="status">Status</label>
                    <x-select placeholder="Select status" :options="\App\Enums\WorkOrderStatusEnum::options()" option-value="value" option-label="label"
                        wire:model="status" class=" mt-2" />
                </div>
            </div>
            <x-button wire:click="submit" spinner label="Submit" class="btn-primary mt-10 w-full" />
        </x-form>
    </div>
</div>

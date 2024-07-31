<x-blocks.flex-block-end>
    <x-inputs.input-with-label name="name" field="" value="{{$warehouse->warehouse->name}}" disabled></x-inputs.input-with-label>
    <x-dropdown-select name="warehouseId" field="moyskladWarehouseId" :options="$moyskladWarehouses"></x-dropdown-select>
    <x-primary-button wire:click="updateStocks">Обновить остатки</x-primary-button>
    <div wire:loading wire:target="updateStocks">
        <x-loader/>
    </div>
</x-blocks.flex-block-end>

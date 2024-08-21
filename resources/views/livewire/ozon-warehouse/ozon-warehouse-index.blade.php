<div>
    <x-blocks.main-block>
        <x-layouts.title name="Склады"/>
    </x-blocks.main-block>
    <x-blocks.flex-block-end>
        <x-dropdown-select name="warehouse"
                           field="selectedWarehouse"
                           :options="$apiWarehouses"
                           value="warehouse_id">
            Выберите склад
        </x-dropdown-select>
        <div class="self-center">
            <x-success-button wire:click="addWarehouse">Добавить</x-success-button>
        </div>
    </x-blocks.flex-block-end>
    @foreach($market->warehouses as $warehouse)
        <livewire:ozon-warehouse.ozon-warehouse-edit :warehouse="$warehouse" wire:key="{{$warehouse->getKey()}}"/>
    @endforeach
</div>

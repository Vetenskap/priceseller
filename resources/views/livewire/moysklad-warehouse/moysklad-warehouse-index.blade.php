<div>
    <x-blocks.main-block>
        <x-layouts.title name="Склады"/>
    </x-blocks.main-block>
    <x-blocks.flex-block-end>
        <x-dropdown-select name="warehouse"
                           field="selectedWarehouse"
                           :options="$apiWarehouses">
            Выберите склад
        </x-dropdown-select>
        <x-success-button wire:click="addWarehouse">Добавить</x-success-button>
    </x-blocks.flex-block-end>
    @foreach($moysklad->warehouses as $warehouse)
        <livewire:moysklad-warehouse.moysklad-warehouse-edit :warehouse="$warehouse"/>
    @endforeach
</div>

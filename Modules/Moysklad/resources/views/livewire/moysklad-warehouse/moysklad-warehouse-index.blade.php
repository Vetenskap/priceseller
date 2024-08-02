<x-layouts.module-container>
    <x-blocks.main-block>
        <x-information>
            Вы можете привязать свои склады с Моего Склада к своим существующим.
        </x-information>
    </x-blocks.main-block>
    <x-blocks.flex-block-end>
        <x-dropdown-select name="warehouses" field="warehouseId" :options="auth()->user()->warehouses">Ваши склады</x-dropdown-select>
        <x-success-button wire:click="add">Добавить</x-success-button>
    </x-blocks.flex-block-end>
    @if($moysklad->warehouses->count())
        <x-blocks.main-block>
            <x-success-button wire:click="save">Сохранить</x-success-button>
        </x-blocks.main-block>
    @endif
    @foreach($moysklad->warehouses as $warehouse)
        <livewire:moysklad::moysklad-warehouse.moysklad-warehouse-edit :warehouse="$warehouse" wire:key="{{$warehouse->id}}" :moysklad-warehouses="$moyskladWarehouses"/>
    @endforeach
    <x-blocks.main-block>
        <x-layouts.title name="Вебхук" />
    </x-blocks.main-block>
    <x-success-button wire:click="addWebhook">Добавить</x-success-button>
</x-layouts.module-container>

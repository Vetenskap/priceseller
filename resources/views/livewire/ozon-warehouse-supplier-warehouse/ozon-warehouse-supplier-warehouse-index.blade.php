<div>
    <x-blocks.main-block>
        <x-layouts.title name="Склады"/>
        <x-titles.sub-title name="Привязка складов"/>
        <x-information>Склады поставщика с которых нужно выгружать остатки. Если добавить несколько складов, то они будут складываться</x-information>
    </x-blocks.main-block>
    <div x-data="{ open: false }">
        <x-blocks.main-block>
            <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
        </x-blocks.main-block>
        <div x-show="open">
            <x-blocks.flex-block>
                <x-dropdown-select name="supplier_warehouse_id" field="supplier_warehouse_id" :options="$supplier->supplier->warehouses->all()">
                    Выберите склад
                </x-dropdown-select>
                <div class="self-center">
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </div>
            </x-blocks.flex-block>
        </div>
    </div>
    <x-blocks.main-block>
        <x-layouts.title name="Все склады"/>
    </x-blocks.main-block>
    @if($supplier->warehouses->isNotEmpty())
        @foreach($supplier->warehouses as $warehouse)
            <x-blocks.flex-block>
                <x-layouts.title :name="$warehouse->supplierWarehouse->name"/>
                <div class="self-center">
                    <x-danger-button wire:click="destroy({{$warehouse->id}})">Удалить</x-danger-button>
                </div>
            </x-blocks.flex-block>
        @endforeach
    @else
        <x-blocks.main-block>
            <x-information>Вы пока ещё не добавляли склады</x-information>
        </x-blocks.main-block>
    @endif
</div>

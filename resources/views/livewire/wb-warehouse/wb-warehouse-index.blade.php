<div>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Склады</flux:heading>
            <flux:input.group>
                <flux:select variant="listbox" searchable placeholder="Выберите склад..."
                             wire:model="selectedWarehouse">
                    <x-slot name="search">
                        <flux:select.search placeholder="Поиск..."/>
                    </x-slot>

                    @foreach($apiWarehouses as $apiWarehouse)
                        <flux:option :value="$apiWarehouse['id']">{{$apiWarehouse['name']}}</flux:option>
                    @endforeach
                </flux:select>

                <flux:button icon="plus" wire:click="store">Добавить</flux:button>
            </flux:input.group>
            <flux:heading size="xl">Список</flux:heading>
            @foreach($market->warehouses as $warehouse)
                <livewire:wb-warehouse.wb-warehouse-edit :warehouse="$warehouse" :key="$warehouse->getKey()"/>
            @endforeach
        </flux:card>
    </x-blocks.main-block>
</div>

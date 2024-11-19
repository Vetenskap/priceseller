<div>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Склады</flux:heading>
            @if($this->user()->can('update-wb'))
                <flux:input.group>
                    <flux:select variant="combobox" placeholder="Выберите склад..."
                                 wire:model="selectedWarehouse" wire:click="getWarehouses">
                        <flux:icon.loading wire:loading
                                           wire:target="getWarehouses"/>
                        @foreach($apiWarehouses as $apiWarehouse)
                            <flux:option :value="$apiWarehouse['id']">{{$apiWarehouse['name']}}</flux:option>
                        @endforeach
                    </flux:select>

                    <flux:button icon="plus" wire:click="store">Добавить</flux:button>
                </flux:input.group>
            @endif
            <flux:heading size="xl">Список</flux:heading>
            @foreach($market->warehouses as $warehouse)
                <livewire:wb-warehouse.wb-warehouse-edit :warehouse="$warehouse" :key="$warehouse->getKey()"/>
            @endforeach
        </flux:card>
    </x-blocks.main-block>
</div>

<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:heading size="xl">Добавление нового склада</flux:heading>
                <div class="flex gap-6 w-1/4">
                    <flux:select variant="combobox" placeholder="Выберите склад поставщика..."
                                 wire:model="name">

                        @foreach($configWarehouses as $configWarehouse)
                            <flux:option :value="$configWarehouse['name']">{{$configWarehouse['label']}}</flux:option>
                        @endforeach
                    </flux:select>
                    <flux:select variant="combobox" placeholder="Выберите ваш склад поставщика..."
                                 wire:model="supplier_warehouse_id">

                        @foreach($voshodApi->supplier->warehouses as $warehouse)
                            <flux:option :value="$warehouse->getKey()">{{$warehouse->name}}</flux:option>
                        @endforeach
                    </flux:select>
                    <flux:button wire:click="store">Добавить</flux:button>
                </div>
            </flux:card>
        </x-blocks.main-block>
    </x-layouts.main-container>
    @if($voshodApi->warehouses->isNotEmpty())
        <x-layouts.main-container>
            <x-blocks.main-block>
                <flux:card class="space-y-6">
                    <flux:heading size="xl">Список</flux:heading>
                    <flux:table :paginate="$this->warehouses">
                        <flux:columns>
                            <flux:column>Склад поставщик</flux:column>
                            <flux:column>Ваш склад поставщика</flux:column>
                            <flux:column>Создан</flux:column>
                        </flux:columns>
                        <flux:rows>
                            @foreach($this->warehouses as $warehouse)
                                <flux:row :key="$warehouse->getKey()">
                                    <flux:cell>{{collect($configWarehouses)->firstWhere('name', $warehouse->name)['label']}}</flux:cell>
                                    <flux:cell>{{$warehouse->supplierWarehouse->name}}</flux:cell>
                                    <flux:cell>{{$warehouse->created_at}}</flux:cell>
                                    <flux:cell align="right">
                                        <flux:icon.trash wire:click="destroy({{ json_encode($warehouse->getKey()) }})"
                                                         wire:loading.remove
                                                         wire:target="destroy({{ json_encode($warehouse->getKey()) }})"
                                                         class="cursor-pointer hover:text-red-400"/>
                                        <flux:icon.loading wire:loading wire:target="destroy({{ json_encode($warehouse->getKey()) }})"/>
                                    </flux:cell>
                                </flux:row>
                            @endforeach
                        </flux:rows>
                    </flux:table>
                </flux:card>
            </x-blocks.main-block>
        </x-layouts.main-container>
    @endif
</div>

<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:heading size="xl">Добавление нового склада</flux:heading>
                <div class="flex gap-6 items-end">
                    <flux:input wire:model="name" label="Наименование" required/>
                    <flux:input wire:model="warehouse_id" label="Идентификатор" type="number" required/>
                    <flux:select variant="listbox" searchable placeholder="Выберите ваш склад поставщика..." label="Склад"
                                 wire:model="supplier_warehouse_id">
                        <x-slot name="search">
                            <flux:select.search placeholder="Поиск..."/>
                        </x-slot>

                        @foreach($bergApi->supplier->warehouses as $warehouse)
                            <flux:option :value="$warehouse->getKey()">{{$warehouse->name}}</flux:option>
                        @endforeach
                    </flux:select>
                    <flux:button wire:click="store" e>Добавить</flux:button>
                </div>
            </flux:card>
        </x-blocks.main-block>
    </x-layouts.main-container>
    @if($this->warehouses->isNotEmpty())
        <x-layouts.main-container>
            <x-blocks.main-block>
                <flux:card class="space-y-6">
                    <flux:heading size="xl">Список</flux:heading>
                    <flux:table :paginate="$this->warehouses">
                        <flux:columns>
                            <flux:column>Склад</flux:column>
                            <flux:column>Идентификатор</flux:column>
                            <flux:column>Ваш склад поставщика</flux:column>
                            <flux:column>Создан</flux:column>
                        </flux:columns>
                        <flux:rows>
                            @foreach($this->warehouses as $warehouse)
                                <flux:row :key="$warehouse->getKey()">
                                    <flux:cell>{{$warehouse->name}}</flux:cell>
                                    <flux:cell>{{$warehouse->warehouse_id}}</flux:cell>
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

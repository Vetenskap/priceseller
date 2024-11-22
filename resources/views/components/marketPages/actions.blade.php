@props(['market'])

<div>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <div class="flex gap-6">
                <flux:button wire:click="testPrice"
                             wire:confirm="Вы действительно хотите пересчитать цены? Действие происходит в реальном времени, не перезагружайте страницу.">
                    Пересчитать цены
                </flux:button>
                <flux:button wire:click="testStocks"
                             wire:confirm="Вы действительно хотите пересчитать остатки? Действие происходит в реальном времени, не перезагружайте страницу.">
                    Пересчитать остатки
                </flux:button>
                <flux:button wire:click="nullStocks"
                             wire:confirm="Вы действительно хотите занулить кабинет? Действие нельзя будет отменить.">
                    Занулить кабинет
                </flux:button>
            </div>
            <div>
                <flux:dropdown>
                    <flux:button icon-trailing="chevron-down">Склады</flux:button>

                    <flux:menu>
                        @foreach($market->warehouses as $warehouse)
                            @foreach($warehouse->suppliers as $supplier)
                                <flux:menu.group :heading="$supplier->supplier->name">
                                    @if($supplier->warehouses->isEmpty())
                                        <flux:menu.checkbox
                                            wire:model.live="testWarehouses.{{$supplier->supplier->getKey()}}.userWarehouses">Только ваши
                                            склады
                                        </flux:menu.checkbox>
                                    @else
                                        @foreach($supplier->warehouses as $warehouse)
                                            <flux:menu.checkbox
                                                wire:model.live="testWarehouses.{{$supplier->supplier->getKey()}}.{{$warehouse->supplierWarehouse->getKey()}}">{{$warehouse->supplierWarehouse->name}}</flux:menu.checkbox>
                                        @endforeach
                                    @endif
                                </flux:menu.group>
                            @endforeach
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
            </div>
        </flux:card>
    </x-blocks.main-block>
    <livewire:market-action-reports.market-action-reports-index :market="$market"/>
</div>

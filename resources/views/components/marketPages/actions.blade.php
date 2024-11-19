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
                                            wire:model.live="testWarehouses.{{$supplier->getKey()}}.userWarehouses">Только ваши
                                            склады
                                        </flux:menu.checkbox>
                                    @else
                                        @foreach($supplier->warehouses as $warehouse)
                                            <flux:menu.checkbox
                                                wire:model.live="testWarehouses.{{$supplier->getKey()}}.{{$warehouse->getKey()}}">{{$warehouse->name}}</flux:menu.checkbox>
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
    <x-blocks.main-block>
        <flux:card>
            <flux:table>
                <flux:columns>
                    <flux:column>Статус</flux:column>
                    <flux:column>Действие</flux:column>
                    <flux:column>Начало</flux:column>
                    <flux:column>Конец</flux:column>
                </flux:columns>
                <flux:rows>
                    @foreach($market->actionReports as $report)
                        <flux:row :key="$report->getKey()">
                            <flux:cell>
                                <flux:badge size="sm"
                                            :color="$report->status == 2 ? 'yellow' : ($report->status == 1 ? 'red' : 'lime')"
                                            inset="top bottom">{{ $report->message }}</flux:badge>
                            </flux:cell>
                            <flux:cell>{{$report->action}}</flux:cell>
                            <flux:cell>{{$report->created_at}}</flux:cell>
                            <flux:cell>{{$report->updated_at}}</flux:cell>
                        </flux:row>
                    @endforeach
                </flux:rows>
            </flux:table>
        </flux:card>
    </x-blocks.main-block>
</div>

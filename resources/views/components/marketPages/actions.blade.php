@props(['market'])

<div>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <div class="flex gap-6">
                <flux:button wire:click="testPrice"
                             wire:confirm="Вы действительно хотите пересчитать цены? Действие происходит в реальном времени, не перезагружайте страницу.">
                    Пересчитать цены
                </flux:button>
                <flux:button wire:click="nullStocks"
                             wire:confirm="Вы действительно хотите занулить кабинет? Действие нельзя будет отменить.">
                    Занулить кабинет
                </flux:button>
            </div>
            <div class="flex gap-6">
                <div>
                    <flux:dropdown>
                        <flux:button icon-trailing="chevron-down">Поставщики</flux:button>

                        <flux:menu>
                            @foreach($market->suppliers() as $supplier)
                                    <flux:menu.checkbox>{{$supplier->name}}</flux:menu.checkbox>
                            @endforeach
                        </flux:menu>
                    </flux:dropdown>
                </div>
                <flux:button wire:click="testStocks"
                             wire:confirm="Вы действительно хотите пересчитать остатки? Действие происходит в реальном времени, не перезагружайте страницу.">
                    Пересчитать остатки (почта)
                </flux:button>
                <flux:button wire:click="testStocks"
                             wire:confirm="Вы действительно хотите пересчитать остатки? Действие происходит в реальном времени, не перезагружайте страницу.">
                    Пересчитать остатки (апи)
                </flux:button>
                <flux:button wire:click="testStocks"
                             wire:confirm="Вы действительно хотите пересчитать остатки? Действие происходит в реальном времени, не перезагружайте страницу.">
                    Пересчитать остатки (только мои склады)
                </flux:button>
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

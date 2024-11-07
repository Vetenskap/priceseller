<x-layouts.module-index-layout :modules="$modules">
    <x-blocks.main-block>
        <flux:navbar>
            <flux:navbar.item :href="route('orders.index', ['page' => 'main'])" :current="$page === 'main'">
                Основное
            </flux:navbar.item>
            <flux:navbar.item :href="route('orders.index', ['page' => 'states'])"
                              :current="$page === 'states'">Не менять статус
            </flux:navbar.item>
        </flux:navbar>
    </x-blocks.main-block>
    @if($page === 'main')
        <x-blocks.main-block>
            <flux:navbar>
                @foreach($organizations as $org)
                    <flux:navbar.item :href="route('orders.index', ['page' => 'main', 'organizationId' => $org->id])"
                                      :current="$organizationId === $org->id">
                        {{$org->name}}
                    </flux:navbar.item>
                @endforeach
            </flux:navbar>
        </x-blocks.main-block>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <div class="flex">
                    <flux:switch wire:model.live="automatic" label="Автоматическая выгрузка"/>
                </div>
                <div>
                    <flux:dropdown>
                        <flux:button icon-trailing="chevron-down">Склады</flux:button>

                        <flux:menu>
                            @foreach($warehouses as $warehouse)
                                <flux:menu.checkbox
                                    wire:model.live="selectedWarehouses.{{$warehouse->getKey()}}">{{$warehouse->name}}</flux:menu.checkbox>
                            @endforeach
                        </flux:menu>
                    </flux:dropdown>
                </div>
                <flux:subheading>С указанных складов будут списаны остатки при автоматической выгрузке и
                    ручной
                </flux:subheading>
            </flux:card>
        </x-blocks.main-block>
        @if($organization)
            <x-blocks.main-block>
                <flux:card class="space-y-6">
                    <flux:button wire:click="startAllActions">Выполнить все действия</flux:button>
                    <flux:heading size="xl">Ручная выгрузка</flux:heading>
                    <flux:button wire:click="getOrders">Получить заказы</flux:button>
                    <flux:subheading>Получить заказы с ОЗОН и ВБ в которых указана данная организация</flux:subheading>
                    @if($orders->count())
                        <flux:button variant="danger" wire:click="clear">Очистить</flux:button>
                        <flux:subheading>При очистке текущие заказы больше не будут учитываться при выгрузке прайса
                        </flux:subheading>
                        <flux:button wire:click="writeOffBalance">Списать остатки со складов</flux:button>
                        <flux:subheading>Списать остатки в счёт заказов с указанных выше складов</flux:subheading>
                        @if($writeOff)
                            <flux:button wire:click="downloadWriteOffBalance">Скачать списанные</flux:button>
                            <flux:button variant="danger" wire:click="writeOffBalanceRollback">Отменить списание
                            </flux:button>
                        @endif
                        <flux:button wire:click="purchaseOrder">Сформировать заказы поставщикам</flux:button>
                        <flux:subheading>Сформировать заказы поставщикам в формате EXCEL</flux:subheading>
                        <div class="flex gap-6">
                            @foreach($organization->supplierOrderReports as $report)
                                <flux:button wire:click="downloadPurchaseOrder({{$report}})">{{$report->supplier->name}}
                                    ({{$orders->where('orderable.item.supplier_id', $report->supplier_id)->where('count', '>', 0)->groupBy('orderable.item.id')->count()}}
                                    )
                                </flux:button>
                            @endforeach
                        </div>
                        <flux:heading size="xl">Управление кабинетами</flux:heading>
                        <flux:button wire:click="writeOffMarketsStocks">Списать остатки с остальных кабинетов
                        </flux:button>
                        <flux:button wire:click="setOrdersState">Поменять статус заказов</flux:button>
                        <flux:subheading>Установить статусы в ОЗОН на "Готово к отгрузке"</flux:subheading>
                    @endif
                </flux:card>
            </x-blocks.main-block>
        @endif
        @if($orders->count())
            <x-blocks.main-block>
                <flux:card>
                    <flux:table>
                        <flux:columns>
                            <flux:column>Номер заказа</flux:column>
                            <flux:column>Код клиента</flux:column>
                            <flux:column>Код магазина</flux:column>
                            <flux:column>Кабинет</flux:column>
                            <flux:column>Количество</flux:column>
                            <flux:column>Цена</flux:column>
                        </flux:columns>
                        <flux:rows>
                            @foreach($orders as $order)
                                <flux:row :key="$order->getKey()">
                                    <flux:cell>{{$order->number}}</flux:cell>
                                    <flux:cell>{{$order->orderable?->itemable->code}}</flux:cell>
                                    <flux:cell>{{$order->orderable?->offer_id ?? $order->orderable?->vendor_code}}</flux:cell>
                                    <flux:cell>{{$order->orderable?->market->name}}</flux:cell>
                                    <flux:cell>{{$order->count}}</flux:cell>
                                    <flux:cell>{{$order->price . (' ' . $order->currency_code ?: '')}}</flux:cell>
                                </flux:row>
                            @endforeach
                        </flux:rows>
                    </flux:table>
                </flux:card>
            </x-blocks.main-block>
        @endif
    @endif
    @if($page === 'states')
        <x-layouts.module-container>
            <x-blocks.main-block>
                <x-blocks.main-block>
                    <x-layouts.title name="Экспорт"/>
                </x-blocks.main-block>
                <x-blocks.center-block>
                    <x-secondary-button wire:click="export">Экспортировать</x-secondary-button>
                </x-blocks.center-block>
                <x-blocks.main-block>
                    <x-layouts.title name="Загрузить товары"/>
                </x-blocks.main-block>
                <form wire:submit="import">
                    <div
                        x-data="{ uploading: false, progress: 0 }"
                        x-on:livewire-upload-start="uploading = true"
                        x-on:livewire-upload-finish="uploading = false"
                        x-on:livewire-upload-cancel="uploading = false"
                        x-on:livewire-upload-error="uploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                    >
                        <x-blocks.main-block>
                            <x-file-input wire:model="file"/>
                        </x-blocks.main-block>

                        <x-blocks.main-block x-show="uploading">
                            <x-file-progress x-bind:style="{ width: progress + '%' }"/>
                        </x-blocks.main-block>

                        @if($file)
                            <x-blocks.main-block class="text-center">
                                <x-success-button wire:click="import">Загрузить</x-success-button>
                            </x-blocks.main-block>
                        @endif
                    </div>
                </form>
            </x-blocks.main-block>
        </x-layouts.module-container>
    @endif
</x-layouts.module-index-layout>

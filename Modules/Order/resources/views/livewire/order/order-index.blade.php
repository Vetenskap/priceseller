<div>
    <x-layouts.header name="Заказы"/>
    <x-navigate-pages>
        <x-links.tab-link href="{{route('orders.index', ['page' => 'main'])}}" :active="$page === 'main'">Основное
        </x-links.tab-link>
        <x-links.tab-link href="{{route('orders.index', ['page' => 'states'])}}" :active="$page === 'states'">Не менять
            статус
        </x-links.tab-link>
    </x-navigate-pages>
    @if($page === 'main')
        <x-layouts.module-container class="flex p-6">
            <div class="p-6 dark:bg-gray-700 bg-gray-100 w-1/4 overflow-hidden shadow-sm sm:rounded-lg mr-6">
                <a href="{{route('modules.index')}}">
                    <x-secondary-button class="mb-6">Назад</x-secondary-button>
                </a>
                <div class="mb-6">
                    <x-layouts.title name="Организации"/>
                </div>
                @foreach($organizations as $org)
                    <a href="{{route('orders.index', ['page' => 'main', 'organizationId' => $org->id])}}">
                        <div
                            class="mb-6 text-center shadow-sm sm:rounded-lg p-4 dark:text-white {{$organizationId === $org->id ? 'dark:bg-gray-600 bg-gray-300' : 'dark:bg-gray-500 bg-gray-200'}}">
                            {{$org->name}}
                        </div>
                    </a>
                @endforeach
            </div>
            @if($organization)
                <div class="w-3/4">
                    <div class="p-6 dark:bg-gray-700 bg-gray-100 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <x-layouts.title :name="$organization->name"/>
                        @if($orders->count())
                            <x-blocks.flex-block-end>
                                <x-danger-button wire:click="clear">Очистить</x-danger-button>
                            </x-blocks.flex-block-end>
                        @endif
                        <x-blocks.flex-block-end>
                            <x-primary-button wire:click="getOrders">Получить заказы</x-primary-button>
                        </x-blocks.flex-block-end>
                        @if($orders->count())
                            <x-titles.sub-title name="Списать остатки"/>
                            <x-blocks.flex-block-end>
                                <x-dropdowns.dropdown-checkboxes :options="$warehouses"
                                                                 :selected-options="$selectedWarehouses"
                                                                 wire-func="selectWarehouse"
                                                                 :active="$openSelectedWarehouses"
                                >
                                    Склады
                                </x-dropdowns.dropdown-checkboxes>
                                <x-primary-button wire:click="writeOffBalance">Списать остатки</x-primary-button>
                            </x-blocks.flex-block-end>
                            @if($writeOff)
                                <x-blocks.flex-block-end>
                                    <x-success-button wire:click="downloadWriteOffBalance">Скачать списанные
                                    </x-success-button>
                                </x-blocks.flex-block-end>
                                <x-blocks.flex-block-end>
                                    <x-danger-button wire:click="writeOffBalanceRollback">Отменить списание
                                    </x-danger-button>
                                </x-blocks.flex-block-end>
                            @endif
                            <x-titles.sub-title name="Сформировать заказы поставщикам"/>
                            <x-blocks.flex-block>
                                <x-primary-button wire:click="purchaseOrder">Сформировать</x-primary-button>
                            </x-blocks.flex-block>
                            <x-blocks.flex-block-end>
                                @foreach($organization->supplierOrderReports as $report)
                                    <x-success-button
                                        wire:click="downloadPurchaseOrder({{$report}})">{{$report->supplier->name}}
                                        ({{$orders->where('orderable.item.supplier_id', $report->supplier_id)->where('count', '>', 0)->groupBy('orderable.item.id')->count()}})
                                    </x-success-button>
                                @endforeach
                            </x-blocks.flex-block-end>
                            <x-layouts.title name="Управление кабинетами"/>
                            <x-blocks.flex-block-end>
                                <x-primary-button wire:click="writeOffMarketsStocks">Списать остатки с остальных
                                    кабинетов
                                </x-primary-button>
                            </x-blocks.flex-block-end>
                            <x-blocks.flex-block-end>
                                <x-primary-button wire:click="setOrdersState">Поменять статус заказов</x-primary-button>
                            </x-blocks.flex-block-end>
                        @endif
                    </div>
                    @if($orders->count())
                        <div class="p-6 dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                            <x-layouts.title :name="'Заказы ' . '(' . $orders->count() . ')'"/>
                            <x-table.table-layout>
                                <x-table.table-header>
                                    <x-table.table-child>
                                        <x-layouts.simple-text name="Номер заказа"/>
                                    </x-table.table-child>
                                    <x-table.table-child>
                                        <x-layouts.simple-text name="Код клиента"/>
                                    </x-table.table-child>
                                    <x-table.table-child>
                                        <x-layouts.simple-text name="Код магазина"/>
                                    </x-table.table-child>
                                    <x-table.table-child>
                                        <x-layouts.simple-text name="Кабинет"/>
                                    </x-table.table-child>
                                    <x-table.table-child>
                                        <x-layouts.simple-text name="Количество"/>
                                    </x-table.table-child>
                                    <x-table.table-child>
                                        <x-layouts.simple-text name="Цена"/>
                                    </x-table.table-child>
                                </x-table.table-header>
                                @foreach($orders as $order)
                                    <x-table.table-item :status="$order->writeOffStocks()->count() ? 2 : -1" wire:key="{{$order->getKey()}}">
                                        <x-table.table-child>
                                            <x-layouts.simple-text :name="$order->number"/>
                                        </x-table.table-child>
                                        <x-table.table-child>
                                            <x-layouts.simple-text :name="$order->orderable?->item->code"/>
                                        </x-table.table-child>
                                        <x-table.table-child>
                                            <x-layouts.simple-text
                                                :name="$order->orderable?->offer_id ?? $order->orderable?->vendor_code"/>
                                        </x-table.table-child>
                                        <x-table.table-child>
                                            <x-layouts.simple-text :name="$order->orderable?->market->name"/>
                                        </x-table.table-child>
                                        <x-table.table-child>
                                            <x-layouts.simple-text
                                                :name="$order->count"/>
                                        </x-table.table-child>
                                        <x-table.table-child>
                                            <x-layouts.simple-text :name="$order->price"/>
                                        </x-table.table-child>
                                    </x-table.table-item>
                                @endforeach
                            </x-table.table-layout>
                        </div>
                    @endif
                </div>
            @endif
        </x-layouts.module-container>
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
    <div wire:loading
         wire:target="setOrdersState, export, import, getOrders, writeOffBalance, purchaseOrder, clear, writeOffBalanceRollback, writeOffMarketsStocks">
        <x-loader/>
    </div>
</div>

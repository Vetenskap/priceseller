<x-layouts.module-index-layout :modules="$modules" wire:poll>
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('orders.index', ['page' => 'main'])}}" :active="$page === 'main'">Основное
            </x-links.tab-link>
            <x-links.tab-link href="{{route('orders.index', ['page' => 'states'])}}" :active="$page === 'states'">Не
                менять
                статус
            </x-links.tab-link>
        </x-navigate-pages>
        <x-blocks.center-block>
            <x-layouts.title name="Организации"/>
        </x-blocks.center-block>
        <x-blocks.flex-block>
            @foreach($organizations as $org)
                <a href="{{route('orders.index', ['page' => 'main', 'organizationId' => $org->id])}}">
                    <div
                        class="w-[250px] mb-6 text-center shadow-sm sm:rounded-lg p-4 dark:text-white {{$organizationId === $org->id ? 'dark:bg-gray-600 bg-gray-300' : 'dark:bg-gray-500 bg-gray-200'}}">
                        {{$org->name}}
                    </div>
                </a>
            @endforeach
        </x-blocks.flex-block>
    </x-layouts.main-container>
    <x-layouts.main-container>
        @if($organization)

            <x-blocks.main-block>
                <x-layouts.title :name="$organization->name"/>
            </x-blocks.main-block>
            <x-blocks.flex-block>
                <x-success-button wire:click="store">Сохранить</x-success-button>
            </x-blocks.flex-block>
            @if($orders->count())
                <x-blocks.main-block>
                    <x-danger-button wire:click="clear">Очистить</x-danger-button>
                    <x-information>При очистке текущие заказы больше не будут учитываться при выгрузке
                        прайса
                    </x-information>
                </x-blocks.main-block>
            @endif
            <x-blocks.flex-block>
                <x-inputs.switcher :checked="$automatic" wire:model="automatic"/>
                <x-layouts.simple-text name="Автоматическая выгрузка"/>
            </x-blocks.flex-block>
            <x-blocks.main-block>
                <x-dropdowns.dropdown-checkboxes :options="$warehouses"
                                                 :selected-options="$selectedWarehouses"
                                                 wire-func="selectWarehouse"
                >
                    Склады
                </x-dropdowns.dropdown-checkboxes>
                <x-information>С указанных складов будут списаны остатки при автоматической выгрузке и
                    ручной
                </x-information>
            </x-blocks.main-block>
            <x-blocks.flex-block-end>
                <x-primary-button wire:click="startAllActions">Выполнить все действия</x-primary-button>
            </x-blocks.flex-block-end>
            <x-blocks.main-block>
                <x-layouts.title name="Ручная выгрузка"/>
            </x-blocks.main-block>
            <x-blocks.main-block>
                <x-primary-button wire:click="getOrders">Получить заказы</x-primary-button>
                <x-information>Получить заказы с ОЗОН и ВБ в которых указана данная организация
                </x-information>
            </x-blocks.main-block>
            @if($orders->count())
                <x-blocks.main-block>
                    <x-primary-button wire:click="writeOffBalance">Списать остатки со складов
                    </x-primary-button>
                    <x-information>Списать остатки в счёт заказов с указанных выше складов</x-information>
                </x-blocks.main-block>
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
                <x-blocks.main-block>
                    <x-primary-button wire:click="purchaseOrder">Сформировать заказы поставщикам
                    </x-primary-button>
                    <x-information>Сформировать заказы поставщикам в формате EXCEL</x-information>
                </x-blocks.main-block>
                <x-blocks.flex-block-end>
                    @foreach($organization->supplierOrderReports as $report)
                        <x-success-button
                            wire:click="downloadPurchaseOrder({{$report}})">{{$report->supplier->name}}
                            ({{$orders->where('orderable.item.supplier_id', $report->supplier_id)->where('count', '>', 0)->groupBy('orderable.item.id')->count()}}
                            )
                        </x-success-button>
                    @endforeach
                </x-blocks.flex-block-end>
                <x-blocks.main-block>
                    <x-layouts.title name="Управление кабинетами"/>
                </x-blocks.main-block>
                <x-blocks.flex-block-end>
                    <x-primary-button wire:click="writeOffMarketsStocks">Списать остатки с остальных
                        кабинетов
                    </x-primary-button>
                </x-blocks.flex-block-end>
                <x-blocks.main-block>
                    <x-primary-button wire:click="setOrdersState">Поменять статус заказов</x-primary-button>
                    <x-information>Установить статусы в ОЗОН на "Готово к отгрузке"</x-information>
                </x-blocks.main-block>
            @endif
            @if($orders->count())

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
                        <x-table.table-item :status="$order->writeOffStocks()->count() ? 2 : -1"
                                            wire:key="{{$order->getKey()}}">
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
                                <x-layouts.simple-text
                                    :name="$order->price . (' ' . $order->currency_code ?: '')"/>
                            </x-table.table-child>
                        </x-table.table-item>
                    @endforeach
                </x-table.table-layout>

            @endif

        @endif
    </x-layouts.main-container>
    <div wire:loading
         wire:target="startAllActions, setOrdersState, export, import, getOrders, writeOffBalance, purchaseOrder, clear, writeOffBalanceRollback, writeOffMarketsStocks">
        <x-loader/>
    </div>
</x-layouts.module-index-layout>

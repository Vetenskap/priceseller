<div>
    <x-layouts.header name="Заказы"/>
    <x-layouts.module-container class="flex p-6">
        <div class="p-6 dark:bg-gray-700 w-1/4 overflow-hidden shadow-sm sm:rounded-lg mr-6">
            @foreach(auth()->user()->organizations as $org)
                <a href="{{route('orders.index', ['organizationId' => $org->id])}}">
                    <div class="mb-6 text-center shadow-sm sm:rounded-lg p-6 dark:text-white {{$organizationId === $org->id ? 'dark:bg-gray-600' : 'dark:bg-gray-500'}}">
                        {{$org->name}}
                    </div>
                </a>
            @endforeach
        </div>
        @if($organization)
            <div class="w-3/4">
                <div class="p-6 dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <x-layouts.title :name="$organization->name"/>
                    <x-blocks.flex-block-end>
                        <x-success-button>Получить заказы</x-success-button>
                    </x-blocks.flex-block-end>
                    <x-titles.sub-title name="Списать остатки"/>
                    <x-blocks.flex-block-end>
                        <x-dropdown-select name="warehouse"
                                           field="selectedWarehouse"
                                           :options="auth()->user()->warehouses"
                        >
                            Выберите склад
                        </x-dropdown-select>
                        <x-success-button>Списать остатки</x-success-button>
                    </x-blocks.flex-block-end>
                    <x-titles.sub-title name="Сформировать заказы поставщикам"/>
                    <x-blocks.flex-block>
                        <x-primary-button>Сформировать</x-primary-button>
                    </x-blocks.flex-block>
                    <x-titles.sub-title name="Поменять статус заказов"/>
                    <x-blocks.flex-block>
                        <x-primary-button>Озон АксАвто</x-primary-button>
                        <x-primary-button>Вб Ивико</x-primary-button>
                    </x-blocks.flex-block>
                </div>
                <div class="p-6 dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                    <x-layouts.title name="Заказы"/>
                    <x-table.table-layout>
                        <x-table.table-header>
                            <x-table.table-child>
                                <x-layouts.simple-text name="Номер заказа" />
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-layouts.simple-text name="Код товара" />
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-layouts.simple-text name="Количество" />
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-layouts.simple-text name="Цена" />
                            </x-table.table-child>
                        </x-table.table-header>
                        @foreach($organization->orders as $order)
                            <x-table.table-item>
                                <x-table.table-child>
                                    <x-layouts.simple-text :name="$order->number" />
                                </x-table.table-child>
                                <x-table.table-child>
                                    <x-layouts.simple-text :name="$order->orderable->offer_id ?? $order->orderable->vendor_code" />
                                </x-table.table-child>
                                <x-table.table-child>
                                    <x-layouts.simple-text :name="$order->count" />
                                </x-table.table-child>
                                <x-table.table-child>
                                    <x-layouts.simple-text :name="$order->price" />
                                </x-table.table-child>
                            </x-table.table-item>
                        @endforeach
                    </x-table.table-layout>
                </div>
            </div>
        @endif
    </x-layouts.module-container>
    <div wire:loading wire:target="export, import">
        <x-loader/>
    </div>
</div>

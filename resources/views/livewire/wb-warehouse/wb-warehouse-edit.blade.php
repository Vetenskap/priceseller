<div>
    <x-blocks.main-block>
        <x-layouts.title :name="$name"/>
    </x-blocks.main-block>
    @if(!$warehouse->suppliers()->count())
        <x-blocks.center-block class="w-full bg-yellow-200 p-6 dark:bg-yellow-400">
            <x-layouts.simple-text class="dark:text-gray-900"
                                   name="Ни один поставщик не добавлен. Остатки не будут выгружаться"/>
        </x-blocks.center-block>
    @endif
    <x-layouts.actions>
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="$parent.destroy({{$warehouse}})">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container class="border-4">
        <x-navigate-pages>
            <x-links.tab-link name="Основное" :active="$selectedTab === 'main'"
                              wire:click="$set('selectedTab', 'main')"/>
            <x-links.tab-link name="Поставщики" :active="$selectedTab === 'suppliers'"
                              wire:click="$set('selectedTab', 'suppliers')"/>
            <x-links.tab-link name="Мои склады" :active="$selectedTab === 'warehouses'"
                              wire:click="$set('selectedTab', 'warehouses')"/>
        </x-navigate-pages>
        @if($selectedTab === 'main')
            <x-blocks.flex-block>
                <x-inputs.input-with-label name="name"
                                           field="name"
                >
                    Наименование
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="warehouse_id"
                                           field="warehouse_id"
                                           disabled
                >
                    Идентификатор
                </x-inputs.input-with-label>
            </x-blocks.flex-block>
        @endif
        @if($selectedTab === 'suppliers')
            <x-blocks.flex-block-end>
                <x-dropdown-select name="supplier" field="selectedSupplier" :options="auth()->user()->suppliers">
                    Выберите поставщика
                </x-dropdown-select>
                <div class="self-center">
                    <x-success-button wire:click="addSupplier">Добавить</x-success-button>
                </div>
            </x-blocks.flex-block-end>
        @if($warehouse->suppliers()->count())
                <x-table.table-layout>
                    <x-table.table-header>
                        <x-table.table-child>
                            <x-layouts.simple-text name="Наименование"/>
                        </x-table.table-child>
                        <x-table.table-child>

                        </x-table.table-child>
                    </x-table.table-header>
                    @foreach($warehouse->suppliers as $supplier)
                        <x-table.table-item wire:key="{{$supplier->getKey()}}">
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$supplier->supplier->name"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-danger-button wire:click="deleteSupplier({{$supplier}})">Удалить</x-danger-button>
                            </x-table.table-child>
                        </x-table.table-item>
                    @endforeach
                </x-table.table-layout>
        @endif
        @endif
        @if($selectedTab === 'warehouses')
            <x-blocks.flex-block-end>
                <x-dropdown-select name="warehouse" field="selectedWarehouse" :options="auth()->user()->warehouses">
                    Выберите склад
                </x-dropdown-select>
                <div class="self-center">
                    <x-success-button wire:click="addWarehouse">Добавить</x-success-button>
                </div>
            </x-blocks.flex-block-end>
        @if($warehouse->userWarehouses()->count())
                <x-table.table-layout>
                    <x-table.table-header>
                        <x-table.table-child>
                            <x-layouts.simple-text name="Наименование"/>
                        </x-table.table-child>
                        <x-table.table-child>

                        </x-table.table-child>
                    </x-table.table-header>
                    @foreach($warehouse->userWarehouses as $userWarehouse)
                        <x-table.table-item wire:key="{{$userWarehouse->getKey()}}">
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$userWarehouse->warehouse->name"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-danger-button wire:click="deleteWarehouse({{$userWarehouse}})">Удалить</x-danger-button>
                            </x-table.table-child>
                        </x-table.table-item>
                    @endforeach
                </x-table.table-layout>
        @endif
        @endif
    </x-layouts.main-container>
</div>

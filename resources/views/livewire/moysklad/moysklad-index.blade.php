<div>
    <x-layouts.header name="Мой склад"/>
    <x-layouts.main-container>
        <div class="bg-white dark:bg-gray-700">
            <nav class="flex flex-col sm:flex-row">
                <x-links.tab-link name="Основное" :active="$selectedTab === 'main'"
                                  wire:click="$set('selectedTab', 'main')"/>
                <x-links.tab-link name="Склады" :active="$selectedTab === 'warehouses'"
                                  wire:click="$set('selectedTab', 'warehouses')"/>
                <x-links.tab-link name="Товары" :active="$selectedTab === 'items'"
                                  wire:click="$set('selectedTab', 'items')"/>
                <x-links.tab-link name="Поставщики" :active="$selectedTab === 'suppliers'"
                                  wire:click="$set('selectedTab', 'suppliers')"/>
                <x-links.tab-link name="Заказы" :active="$selectedTab === 'orders'"
                                  wire:click="$set('selectedTab', 'orders')"/>
            </nav>
        </div>
        @switch($selectedTab)
            @case('main')
                <x-blocks.flex-block>
                    <x-success-button wire:click="save">Сохранить</x-success-button>
                </x-blocks.flex-block>
                <x-blocks.flex-block>
                    <x-inputs.input-with-label name="name"
                                               type="text"
                                               field="form.name"
                    >Наименование
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="api_key"
                                               type="text"
                                               field="form.api_key"
                    >АПИ ключ
                    </x-inputs.input-with-label>
                </x-blocks.flex-block>
                @break
            @case('warehouses')
                <livewire:moysklad-warehouse.moysklad-warehouse-index :api-warehouses="$apiWarehouses"
                                                                      :moysklad="$form->moysklad"/>
                @break
            @case('items')

                @break
            @case('orders')
                <x-blocks.flex-block>
                    <x-inputs.switcher :checked="$unloadOrders"/>
                    <x-layouts.simple-text name="Учитывать заказы при выгрузке остатков" />
                </x-blocks.flex-block>
                @break
            @case('suppliers')
                <x-blocks.flex-block-end>
                    <x-dropdown-select name="supplier"
                                       field="selectedSupplier"
                                       :options="$suppliers"
                    >
                        Выберите поставщика
                    </x-dropdown-select>
                    <x-success-button wire:click="addSupplier">Добавить</x-success-button>
                </x-blocks.flex-block-end>
                <livewire:supplier.supplier-index />
                @break
        @endswitch
    </x-layouts.main-container>
</div>

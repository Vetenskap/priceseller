<div>
    <x-blocks.main-block>
        <x-layouts.title :name="$name"/>
    </x-blocks.main-block>
    @if(!$warehouse->suppliers()->count())
        <x-blocks.center-block class="w-full bg-yellow-200 p-6">
            <x-layouts.simple-text name="Ни один поставщик не добавлен. Остатки не будут выгружаться"/>
        </x-blocks.center-block>
    @endif
    <x-layouts.actions>
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="$parent.destroy({{$warehouse}})">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container class="border-4">
        <div class="bg-white dark:bg-gray-700">
            <nav class="flex flex-col sm:flex-row">
                <x-links.tab-link name="Основное" :active="$selectedTab === 'main'"
                                  wire:click="$set('selectedTab', 'main')"/>
                <x-links.tab-link name="Поставщики" :active="$selectedTab === 'suppliers'"
                                  wire:click="$set('selectedTab', 'suppliers')"/>
            </nav>
        </div>
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
                <x-success-button wire:click="addSupplier">Добавить</x-success-button>
            </x-blocks.flex-block-end>
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Наименование"/>
                    </x-table.table-child>
                    <x-table.table-child>

                    </x-table.table-child>
                </x-table.table-header>
                @foreach($warehouse->suppliers as $supplier)
                    <x-table.table-item>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$supplier->supplier->name" />
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-danger-button wire:click="deleteSupplier({{$supplier}})">Удалить</x-danger-button>
                        </x-table.table-child>
                    </x-table.table-item>
                @endforeach
            </x-table.table-layout>
        @endif
    </x-layouts.main-container>
</div>

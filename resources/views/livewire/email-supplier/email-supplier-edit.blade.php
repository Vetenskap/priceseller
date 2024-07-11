<div>
    <x-blocks.main-block>
        <x-layouts.title :name="$emailSupplier->supplier->name"/>
    </x-blocks.main-block>
    <x-layouts.actions>
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="$parent.delete({{$emailSupplier->supplier}})">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container class="border-2 dark:border-gray-500">
        <x-navigate-pages>
            <x-links.tab-link name="Основное" :active="$selectedTab === 'main'"
                              wire:click="$set('selectedTab', 'main')"/>
            <x-links.tab-link name="Файл" :active="$selectedTab === 'file'"
                              wire:click="$set('selectedTab', 'file')"/>
            <x-links.tab-link name="Остатки" :active="$selectedTab === 'stocks'"
                              wire:click="$set('selectedTab', 'stocks')"/>
        </x-navigate-pages>
        @if($selectedTab === 'main')
            <x-blocks.flex-block>
                <x-inputs.input-with-label name="email"
                                           type="email"
                                           field="form.email"
                >Почта
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="filename"
                                           type="text"
                                           field="form.filename"
                >Наименование файла
                </x-inputs.input-with-label>
            </x-blocks.flex-block>
        @elseif($selectedTab === 'file')
            <x-blocks.flex-block>
                <x-inputs.input-with-label name="header_article"
                                           type="text"
                                           field="form.header_article"
                >Артикул
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="header_brand"
                                           type="text"
                                           field="form.header_brand"
                >Бренд
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="header_price"
                                           type="text"
                                           field="form.header_price"
                >Цена
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="header_count"
                                           type="text"
                                           field="form.header_count"
                >Остаток
                </x-inputs.input-with-label>
            </x-blocks.flex-block>
        @elseif($selectedTab === 'stocks')
            <x-blocks.main-block>
                <x-success-button wire:click="addEmailSupplierStockValue">Добавить</x-success-button>
            </x-blocks.main-block>
            @foreach($emailSupplier->stockValues as $stockValue)
                <livewire:email-supplier-stock-value wire:key="{{$stockValue->getKey()}}" :stock-value="$stockValue"/>
            @endforeach
        @endif
    </x-layouts.main-container>
</div>

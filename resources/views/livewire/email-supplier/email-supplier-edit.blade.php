<x-blocks.child-block>
    <x-layouts.actions>
        <x-success-button wire:click="update">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>


    <x-blocks.main-block>
        <x-dropdowns.dropdown-select :items="auth()->user()->suppliers"
                                     :current-id="$form->supplier_id"
                                     name="supplier_id"
                                     field="form.supplier_id"
        >Поставщик
        </x-dropdowns.dropdown-select>
    </x-blocks.main-block>
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
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="header_article"
                                   type="number"
                                   field="form.header_article"
        >Артикул
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="header_brand"
                                   type="number"
                                   field="form.header_brand"
        >Бренд
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="header_price"
                                   type="number"
                                   field="form.header_price"
        >Цена
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="header_count"
                                   type="number"
                                   field="form.header_count"
        >Остаток
        </x-inputs.input-with-label>
    </x-blocks.flex-block>
    {{--        @elseif($selectedTab === 'stocks')--}}
    {{--            <x-blocks.main-block>--}}
    {{--                <x-success-button wire:click="addEmailSupplierStockValue">Добавить</x-success-button>--}}
    {{--            </x-blocks.main-block>--}}
    {{--            @foreach($emailSupplier->stockValues as $stockValue)--}}
    {{--                <livewire:email-supplier-stock-value wire:key="{{$stockValue->getKey()}}" :stock-value="$stockValue"/>--}}
    {{--            @endforeach--}}
</x-blocks.child-block>

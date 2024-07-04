<div>
    <x-layouts.header :name="$form->name"/>
    @error('error')
    <x-notify-top>
        <div class="bg-red-400 w-full p-2">
            <x-layouts.simple-text :name="$message"/>
        </div>
    </x-notify-top>
    @enderror
    <x-layouts.actions>
        <a href="{{route('wb')}}" wire:navigate.hover>
            <x-primary-button>Назад</x-primary-button>
        </a>
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-marketPages.index route="wb-market-edit" :market="$market" :page="$page"/>
        @switch($page)
            @case('main')
                <x-blocks.flex-block-end>
                    <x-inputs.switcher :disabled="$market->close" :checked="$form->open" wire:model="form.open"/>
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
                </x-blocks.flex-block-end>
                <x-blocks.flex-block-end>
                    <x-dropdown-select name="organization"
                                       field="form.organization_id"
                                       :options="auth()->user()->organizations">
                        Организация
                    </x-dropdown-select>
                </x-blocks.flex-block-end>
                @break
            @case('prices')
                <x-blocks.flex-block>
                    <x-inputs.input-with-label name="coefficient"
                                               type="number"
                                               field="form.coefficient"
                    >Коэффициент
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="basic_logistics"
                                               type="number"
                                               field="form.basic_logistics"
                    >Базовая цена логистики
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="price_one_liter"
                                               type="number"
                                               field="form.price_one_liter"
                    >Цена за литр
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="volume"
                                               type="number"
                                               field="form.volume"
                    >Объем (л)
                    </x-inputs.input-with-label>
                </x-blocks.flex-block>
                @break
            @case('stocks_warehouses')
                <x-marketPages.stocks-warehouses :market="$market" :api-warehouses="$apiWarehouses" />
                <livewire:wb-warehouse.wb-warehouse-index :market="$market" :api-warehouses="$apiWarehouses"/>
                @break
            @case('export')
                <x-marketPages.export :market="$market"/>
                @break
            @case('relationships_commissions')
                <x-marketPages.relationships-commissions :market="$market" :items="$items" :status-filters="$statusFilters">
                    <x-inputs.input-with-label name="sales_percent"
                                               type="number"
                                               field="sales_percent"
                    >Комиссия
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="min_price"
                                               type="number"
                                               field="min_price"
                    >Минимальная цена продажи
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="retail_markup_percent"
                                               type="number"
                                               field="retail_markup_percent"
                    >Наценка
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="package"
                                               type="number"
                                               field="package"
                    >Упаковка
                    </x-inputs.input-with-label>
                </x-marketPages.relationships-commissions>
                @break
            @case('actions')
                <x-marketPages.actions />
                @break
        @endswitch
    </x-layouts.main-container>
    <div wire:loading
         wire:target="export, import, relationshipsAndCommissions, getWarehouses, clearRelationships, testPrice, nullStocks">
        <x-loader/>
    </div>
</div>

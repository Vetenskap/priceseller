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
        <a href="{{url()->previous()}}" wire:navigate.hover>
            <x-primary-button>Закрыть</x-primary-button>
        </a>
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-marketPages.index route="ozon-market-edit" :market="$market" :page="$page" />
        @switch($page)
            @case('main')
                <x-blocks.flex-block-end>
                    <x-inputs.switcher :disabled="$market->close" :checked="$form->open" wire:model="form.open"/>
                    <x-inputs.input-with-label name="name"
                                               type="text"
                                               field="form.name"
                    >Наименование
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="client_id"
                                               type="text"
                                               field="form.client_id"
                    >Идентификатор клиента
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
                    <x-inputs.switcher :checked="$form->seller_price" wire:model="form.seller_price"/>
                    <x-layouts.simple-text name="Учитывать цену конкурента"/>
                </x-blocks.flex-block>
                <x-blocks.flex-block>
                    <x-inputs.input-with-label name="min_price_percent"
                                               type="number"
                                               field="form.min_price_percent"
                                               tooltip="Итоговая минимальная цена умноженная на этот коэффициент"
                    >Процент увел. мин. цены
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="max_price_percent"
                                               type="number"
                                               field="form.max_price_percent"
                                               tooltip="Минимальная цена * %"
                    >Цена до скидки, %
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="seller_price_percent"
                                               type="number"
                                               field="form.seller_price_percent"
                                               tooltip="Минимальная цена * %"
                    >Цена продажи, %
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="acquiring"
                                               type="number"
                                               field="form.acquiring"
                    >Эквайринг
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="last_mile"
                                               type="number"
                                               field="form.last_mile"
                                               tooltip="Считается 5,5 % от цены на сайте"
                    >Последняя миля
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="max_mile"
                                               type="number"
                                               field="form.max_mile"
                                               tooltip="Берет комиссию мили не выше этой"
                    >Максимальная миля
                    </x-inputs.input-with-label>
                </x-blocks.flex-block>
                @break
            @case('stocks_warehouses')
                <x-marketPages.stocks-warehouses :market="$market" :api-warehouses="$apiWarehouses" />
                <livewire:ozon-warehouse.ozon-warehouse-index :market="$market" :api-warehouses="$apiWarehouses"/>
                @break
            @case('export')
                <x-marketPages.export :market="$market" />
                @break
            @case('relationships_commissions')
                <x-marketPages.relationships-commissions :market="$market" :items="$items" :status-filters="$statusFilters">
                    <x-inputs.input-with-label name="min_price_percent"
                                               type="number"
                                               field="min_price_percent"
                                               tooltip="Какой процент добавить к цене закупки для получения чистой прибыли"
                    >Минимальная цена, %
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="min_price"
                                               type="number"
                                               field="min_price"
                                               tooltip="Ниже этой цены в маркет не выгрузится"
                    >Минимальная цена продажи
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="shipping_processing"
                                               type="number"
                                               field="shipping_processing"
                    >Обработка отправления
                    </x-inputs.input-with-label>
                </x-marketPages.relationships-commissions>
                @break
            @case('actions')
                <x-marketPages.actions />
                @break
        @endswitch
    </x-layouts.main-container>
    <div wire:loading
         wire:target="import, relationshipsAndCommissions, clearRelationships, getWarehouses, testPrice, nullStocks">
        <x-loader/>
    </div>
</div>

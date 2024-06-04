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
        <div class="bg-white">
            <nav class="flex flex-col sm:flex-row">
                <x-links.tab-link name="Основное" :active="$selectedTab === 'main'"
                                  wire:click="$set('selectedTab', 'main')"/>
                <x-links.tab-link name="Цены" :active="$selectedTab === 'prices'"
                                  wire:click="$set('selectedTab', 'prices')"/>
                <x-links.tab-link name="Остатки и склады" :active="$selectedTab === 'stocks_warehouses'"
                                  wire:click="$set('selectedTab', 'stocks_warehouses')"/>
                <x-links.tab-link name="Связи и комиссии" :active="$selectedTab === 'relationships_commissions'"
                                  wire:click="$set('selectedTab', 'relationships_commissions')"/>
                <x-links.tab-link name="Экспорт" :active="$selectedTab === 'export'"
                                  wire:click="$set('selectedTab', 'export')"/>
                <x-links.tab-link name="Действия" :active="$selectedTab === 'actions'"
                                  wire:click="$set('selectedTab', 'actions')"/>
            </nav>
        </div>
        @switch($selectedTab)
            @case('main')
                <x-blocks.flex-block-end>
                    <x-inputs.switcher :checked="$form->open" wire:model="form.open"/>
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
                <x-blocks.main-block>
                    <x-layouts.title name="Остатки"/>
                </x-blocks.main-block>
                @if(!$market->warehouses()->count())
                    <x-blocks.center-block class="w-full bg-yellow-200 p-6">
                        <x-layouts.simple-text name="Ни один склад не добавлен. Остатки не будут выгружаться"/>
                    </x-blocks.center-block>
                @endif
                <x-blocks.flex-block>
                    <x-inputs.input-with-label name="max_count"
                                               type="number"
                                               field="form.max_count"
                    >Максимальный остаток
                    </x-inputs.input-with-label>
                </x-blocks.flex-block>
                <x-blocks.main-block>
                    <x-layouts.simple-text name="Ставить остаток 1 если"/>
                </x-blocks.main-block>
                <x-blocks.flex-block>
                    <x-inputs.input-with-label name="min"
                                               type="number"
                                               field="form.min"
                    >Остаток от
                    </x-inputs.input-with-label>
                    <x-inputs.input-with-label name="max"
                                               type="number"
                                               field="form.max"
                    >Остаток до
                    </x-inputs.input-with-label>
                </x-blocks.flex-block>
                <x-blocks.main-block>
                    <x-layouts.title name="Склады"/>
                </x-blocks.main-block>
                <x-blocks.flex-block-end>
                    <x-dropdown-select name="warehouse"
                                       field="selectedWarehouse"
                                       :options="$apiWarehouses">
                        Выберите склад
                    </x-dropdown-select>
                    <x-success-button wire:click="addWarehouse">Добавить</x-success-button>
                </x-blocks.flex-block-end>
                @if($market->warehouses()->count() > 0)
                    <x-table.table-layout>
                        <x-table.table-header>
                            <x-table.table-child>
                                <x-layouts.simple-text name="Наименование"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-layouts.simple-text name="Идентификатор"/>
                            </x-table.table-child>
                            <x-table.table-child>

                            </x-table.table-child>
                        </x-table.table-header>
                        @foreach($market->warehouses as $warehouse)
                            <x-table.table-item>
                                <x-table.table-child>
                                    <x-layouts.simple-text :name="$warehouse->name"/>
                                </x-table.table-child>
                                <x-table.table-child>
                                    <x-layouts.simple-text :name="$warehouse->id"/>
                                </x-table.table-child>
                                <x-table.table-child>
                                    <x-danger-button wire:click="deleteWarehouse({{$warehouse}})">Удалить
                                    </x-danger-button>
                                </x-table.table-child>
                            </x-table.table-item>
                        @endforeach
                    </x-table.table-layout>
                @else
                    <x-blocks.main-block>
                        <x-titles.sub-title name="Нет складов"/>
                    </x-blocks.main-block>
                @endif
                @break
            @case('export')
                <x-blocks.main-block>
                    <x-layouts.title name="Экспорт"/>
                </x-blocks.main-block>
                <x-blocks.center-block>
                    <x-secondary-button wire:click="export">Экспортировать</x-secondary-button>
                </x-blocks.center-block>
                <livewire:items-export-report.items-export-report-index :model="$market"/>
                @break
            @case('relationships_commissions')
                <x-blocks.main-block>
                    <x-layouts.title name="Создание/Обновление связей и комиссий"/>
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
                            <x-file-input wire:model="file" wire:loading.attr="disabled" wire:target="import"/>
                        </x-blocks.main-block>

                        <x-blocks.main-block x-show="uploading">
                            <x-file-progress x-bind:style="{ width: progress + '%' }"/>
                        </x-blocks.main-block>

                        <x-blocks.center-block>
                            @error('file')
                                {{ $message }}
                            @enderror
                        </x-blocks.center-block>

                        <x-blocks.main-block class="text-center" wire:loading.remove x-show="$wire.file">
                            <x-success-button>Загрузить</x-success-button>
                        </x-blocks.main-block>
                    </div>
                </form>
                <x-layouts.title name="Комиссии"/>
                <x-titles.sub-title name="Комиссии по умолчанию"/>
                <x-blocks.flex-block>
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
                </x-blocks.flex-block>
                <x-blocks.main-block>
                    <x-secondary-button wire:click="relationshipsAndCommissions">Загрузить связи и комиссии
                    </x-secondary-button>
                </x-blocks.main-block>
                <x-blocks.main-block>
                    <x-danger-button wire:click="clearRelationships">Очистить связи</x-danger-button>
                </x-blocks.main-block>
                <livewire:items-import-report.items-import-report-index :model="$market"/>
                @break
            @case('actions')
                <x-blocks.flex-block>
                    <x-secondary-button wire:click="testPrice">Пересчитать цены</x-secondary-button>
                    <x-secondary-button wire:click="nullStocks">Занулить кабинет</x-secondary-button>
                </x-blocks.flex-block>
                @break
        @endswitch
    </x-layouts.main-container>
    @if($selectedTab === 'relationships_commissions')
        <x-layouts.main-container>
            <x-blocks.main-block>
                <x-titles.sub-title name="Фильтры"/>
            </x-blocks.main-block>
            <x-blocks.flex-block>
                <x-inputs.input-with-label name="external_code"
                                           type="text"
                                           field="filters.external_code"
                >Внешний код
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="code"
                                           type="text"
                                           field="filters.code"
                >Ваш код
                </x-inputs.input-with-label>
                <x-dropdown-select name="status"
                                   field="filters.status"
                                   :options="$statusFilters"
                                   value="status">
                    Статус
                </x-dropdown-select>
            </x-blocks.flex-block>
            <x-blocks.main-block>
                <x-layouts.title name="Все связи"/>
            </x-blocks.main-block>
            @if($items->count() > 0)
                <x-table.table-layout>
                    <x-table.table-header>
                        <x-table.table-child>
                            <x-layouts.simple-text name="Внешний код"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text name="Ваш код"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text name="Статус"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text name="Последнее изменение"/>
                        </x-table.table-child>
                    </x-table.table-header>
                    @foreach($items as $item)
                        <x-table.table-item wire:key="{{$item->getKey()}}">
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$item->external_code"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$item->code"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$item->message"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$item->updated_at->diffForHumans()"/>
                            </x-table.table-child>
                        </x-table.table-item>
                    @endforeach
                </x-table.table-layout>
            @else
                <x-titles.sub-title name="Нет связей"/>
            @endif
            <x-blocks.main-block>
                {{ $items->links() }}
            </x-blocks.main-block>
        </x-layouts.main-container>
    @endif
    <div wire:loading
         wire:target="export, import, relationshipsAndCommissions, getWarehouses, clearRelationships, testPrice, nullStocks">
        <x-loader/>
    </div>
</div>

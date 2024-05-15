<div>
    <x-layouts.header :name="$form->name"/>
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
                <x-links.tab-link name="Поставщики" :active="$selectedTab === 'suppliers'"
                                  wire:click="$set('selectedTab', 'suppliers')"/>
                <x-links.tab-link name="Экспорт" :active="$selectedTab === 'export'"
                                  wire:click="$set('selectedTab', 'export')"/>
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
                <x-layouts.title name="Остатки"/>
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
                <x-layouts.title name="Склады"/>
                @break
            @case('export')
                <x-layouts.title name="Экспорт"/>
                <x-secondary-button wire:click="export">Экспортировать</x-secondary-button>
                <x-titles.sub-title name="История"/>
                @foreach($market->exportReports as $report)
                    <x-layouts.title :name="$report->uuid"/>
                @endforeach
                @break
        @endswitch
    </x-layouts.main-container>
    <x-layouts.main-container x-show="selectedTab === 'relationships_commissions'">
        <form wire:submit="saveFile">
            <div
                x-data="{ uploading: false, progress: 0 }"
                x-on:livewire-upload-start="uploading = true"
                x-on:livewire-upload-finish="uploading = false"
                x-on:livewire-upload-cancel="uploading = false"
                x-on:livewire-upload-error="uploading = false"
                x-on:livewire-upload-progress="progress = $event.detail.progress"
            >
                <x-blocks.main-block>
                    <x-file-input wire:model="table" wire:loading.attr="disabled" wire:target="saveFile"/>
                </x-blocks.main-block>

                <x-blocks.main-block x-show="uploading">
                    <x-file-progress x-bind:style="{ width: progress + '%' }"/>
                </x-blocks.main-block>

                @if($table)
                    <x-blocks.main-block class="text-center" wire:loading.remove>
                        <x-success-button>Загрузить</x-success-button>
                    </x-blocks.main-block>
                @endif
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
        <x-secondary-button wire:click="relationshipsAndCommissions">Загрузить связи и комиссии</x-secondary-button>
    </x-layouts.main-container>
    <div wire:loading wire:target="saveFile, save, destroy, export, relationshipsAndCommissions">
        <x-loader/>
    </div>

</div>

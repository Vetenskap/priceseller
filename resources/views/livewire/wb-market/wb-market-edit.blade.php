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
                <x-links.tab-link name="Связи и комиссии" :active="$selectedTab === 'links_commissions'"
                                  wire:click="$set('selectedTab', 'links_commissions')"/>
                <x-links.tab-link name="Поставщики" :active="$selectedTab === 'suppliers'"
                                  wire:click="$set('selectedTab', 'suppliers')"/>
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
            @case('links_commissions')
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
                @break
        @endswitch
    </x-layouts.main-container>
    <x-layouts.main-container x-show="selectedTab === 'links_commissions'">
        <x-layouts.title name="Загрузите связи или комиссии"/>
        <x-titles.sub-title name="Шаблоны для подгрузки связей"/>
        <x-blocks.flex-block class="justify-center">
            <x-primary-button>Артикул</x-primary-button>
            <x-primary-button>Код</x-primary-button>
        </x-blocks.flex-block>
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
                    <label for="dropzone-file"
                           class="mx-auto cursor-pointer flex w-full max-w-lg flex-col items-center rounded-xl border-2 border-dashed border-blue-400 bg-white p-6 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500" fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>

                        <h2 class="mt-4 text-xl font-medium text-gray-700 tracking-wide">Загрузите файл</h2>

                        <p class="mt-2 text-gray-500 tracking-wide">Загрузите или переместите свой файл XLS, XLSX, CSV и
                            TXT. </p>

                        <input id="dropzone-file" type="file" class="hidden" wire:model="table"
                               wire:loading.attr="disabled" wire:target="saveFile"/>
                    </label>
                </x-blocks.main-block>

                <x-blocks.main-block x-show="uploading">
                    <div class="mx-auto h-4 relative w-96 rounded-full overflow-hidden">
                        <div class=" w-full h-full bg-gray-200 absolute "></div>
                        <div class=" h-full bg-yellow-400 sm:bg-green-500 absolute"
                             x-bind:style="{ width: progress + '%' }"></div>
                    </div>
                </x-blocks.main-block>

                @if($table)
                    <x-blocks.main-block class="text-center" wire:loading.remove>
                        <x-success-button>Загрузить</x-success-button>
                    </x-blocks.main-block>
                @endif
            </div>

        </form>
    </x-layouts.main-container>
    <div wire:loading wire:target="saveFile, save, destroy">
        <x-loader/>
    </div>

</div>

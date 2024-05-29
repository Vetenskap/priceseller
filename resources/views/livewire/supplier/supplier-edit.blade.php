<div>
    <x-layouts.header :name="$supplier->name"/>
    <x-layouts.actions>
        <a href="{{route('suppliers')}}" wire:navigate.hover>
            <x-primary-button>Назад</x-primary-button>
        </a>
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <div class="bg-white dark:bg-gray-700">
            <nav class="flex flex-col sm:flex-row">
                <x-links.tab-link name="Основное" :active="$selectedTab === 'main'"
                                  wire:click="$set('selectedTab', 'main')"/>
                <x-links.tab-link name="Прайс" :active="$selectedTab === 'price'"
                                  wire:click="$set('selectedTab', 'price')"/>
            </nav>
        </div>
        @if($selectedTab === 'main')
            <x-blocks.flex-block-end>
                <x-inputs.switcher :checked="$supplier->open" wire:model="form.open"/>
                <x-inputs.input-with-label name="name"
                                           type="text"
                                           field="form.name"
                >Наименование
                </x-inputs.input-with-label>
                @if(auth()->user()->is_ms_sub())
                    <x-inputs.input-with-label name="ms_uuid"
                                               type="text"
                                               field="form.ms_uuid"
                    >МС UUID
                    </x-inputs.input-with-label>
                @endif
            </x-blocks.flex-block-end>
            <x-blocks.flex-block>
                <x-inputs.switcher :checked="$supplier->use_brand" wire:model="form.use_brand"/>
                <x-layouts.simple-text name="Использовать бренд"/>
            </x-blocks.flex-block>
            <x-blocks.main-block>
                <x-layouts.title name="Отчёты"/>
            </x-blocks.main-block>
            @if($supplier->reports->count() > 0)
                <x-table.table-layout>
                    <x-table.table-header>
                        <x-table.table-child>
                            <x-layouts.simple-text name="Статус"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text name="Начало"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text name="Конец"/>
                        </x-table.table-child>
                    </x-table.table-header>
                    @foreach($supplier->reports as $report)
                        <x-table.table-item :status="$report->status">
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$report->message"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$report->created_at"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$report->status !== 2 ? $report->updated_at : ''"/>
                            </x-table.table-child>
                        </x-table.table-item>
                    @endforeach
                </x-table.table-layout>
            @endif
        @elseif($selectedTab === 'price')
            <x-blocks.main-block>
                <x-layouts.title name="Прайс" />
            </x-blocks.main-block>
            <x-titles.sub-title name="Фильтры" />
            <x-blocks.flex-block>
                <x-inputs.input-with-label name="article"
                                           type="text"
                                           field="filters.article"
                >Артикул
                </x-inputs.input-with-label>
            </x-blocks.flex-block>
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Статус" />
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Артикул" />
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Бренд" />
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Цена" />
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Остаток" />
                    </x-table.table-child>
                </x-table.table-header>
                @foreach($priceItems as $priceItem)
                    <x-table.table-item wire:key="{{$priceItem->id}}" :status="$priceItem->status">
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$priceItem->message" />
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$priceItem->article" />
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$priceItem->brand" />
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$priceItem->price" />
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$priceItem->stock" />
                        </x-table.table-child>
                    </x-table.table-item>
                @endforeach
            </x-table.table-layout>
        <x-blocks.main-block>
            {{ $priceItems->links('livewire::tailwind') }}
        </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>

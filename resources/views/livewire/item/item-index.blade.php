<div>
    <x-layouts.header name="Товары"/>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Экспорт"/>
        </x-blocks.main-block>
        <x-blocks.center-block>
            <x-secondary-button wire:click="export">Экспортировать</x-secondary-button>
        </x-blocks.center-block>
        <livewire:items-export-report.items-export-report-index :model="auth()->user()" />
    </x-layouts.main-container>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Создайте новые товары или обновите старые"/>
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
                    <x-file-input wire:model="file"/>
                </x-blocks.main-block>

                <x-blocks.main-block x-show="uploading">
                    <x-file-progress x-bind:style="{ width: progress + '%' }"/>
                </x-blocks.main-block>

                @if($file)
                    <x-blocks.main-block class="text-center">
                        <x-success-button>Загрузить</x-success-button>
                    </x-blocks.main-block>
                @endif
            </div>
        </form>
        <livewire:items-import-report.items-import-report-index :model="auth()->user()" />
    </x-layouts.main-container>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Все товары"/>
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-titles.sub-title name="Фильтры"/>
        </x-blocks.main-block>
        <x-blocks.flex-block>
            <x-inputs.input-with-label name="code"
                                       type="text"
                                       field="filters.code"
            >Код
            </x-inputs.input-with-label>
            <x-inputs.input-with-label name="article"
                                       type="text"
                                       field="filters.article"
            >Артикул
            </x-inputs.input-with-label>
        </x-blocks.flex-block>
        @if($items->count() > 0)
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Код"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Наименование"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Поставщик"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Обновлен"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Создан"/>
                    </x-table.table-child>
                </x-table.table-header>
                @foreach($items->sortByDesc('updated_at') as $item)
                    <x-table.table-item wire:key="{{$item->getKey()}}">
                        <x-table.table-child>
                            <a href="{{route('item-edit', ['item' => $item->getKey()])}}">
                                <x-layouts.simple-text :name="$item->code"/>
                            </a>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$item->name"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$item->supplier?->name"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$item->updated_at->diffForHumans()"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$item->created_at"/>
                        </x-table.table-child>
                    </x-table.table-item>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-layouts.simple-text name="Сейчас у вас нет товаров"/>
            </x-blocks.main-block>
        @endif
        <x-blocks.main-block>
            {{ $items->links() }}
        </x-blocks.main-block>
    </x-layouts.main-container>
    <div wire:loading wire:target="export, import">
        <x-loader/>
    </div>
</div>

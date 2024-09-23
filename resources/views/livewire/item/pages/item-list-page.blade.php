<div>
    <x-layouts.header name="Товары"/>
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('items', ['page' => 'list'])}}" :active="$page === 'list'">Список
            </x-links.tab-link>
            <x-links.tab-link href="{{route('items', ['page' => 'manage'])}}" :active="$page === 'manage'">Управление
            </x-links.tab-link>
        </x-navigate-pages>
        <x-blocks.main-block>
            <x-layouts.title name="Список"/>
        </x-blocks.main-block>
        <x-blocks.main-block>
            <x-titles.sub-title name="Фильтры"/>
        </x-blocks.main-block>
        <x-blocks.flex-block-end>
            <x-inputs.input-with-label name="name"
                                       type="text"
                                       field="filters.name"
            >Наименование
            </x-inputs.input-with-label>
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
            <x-dropdown-select name="unloadWb"
                               field="filters.unload_wb"
                               :options="[['id' => 1, 'name' => 'Да'],['id' => 0, 'name' => 'Нет']]"
            >Выгружать на ВБ
            </x-dropdown-select>
            <x-dropdown-select name="unloadOzon"
                               field="filters.unload_ozon"
                               :options="[['id' => 1, 'name' => 'Да'],['id' => 0, 'name' => 'Нет']]"
            >Выгружать на ОЗОН
            </x-dropdown-select>
            <x-dropdowns.dropdown-select :items="$user->suppliers->all()"
                                         :current-id="isset($filters['supplier_id']) ? $filters['supplier_id'] : null"
                                         name="supplier_id"
                                         field="filters.supplier_id"
            >Поставщик
            </x-dropdowns.dropdown-select>
        </x-blocks.flex-block-end>
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
                        <x-layouts.simple-text name="Последнее обновление"/>
                    </x-table.table-child>
                </x-table.table-header>
                @foreach($items->sortByDesc('updated_at') as $item)
                    <a href="{{route('item-edit', ['item' => $item->getKey()])}}" wire:key="{{$item->getKey()}}">
                        <x-table.table-item :status="session('selected-item') === $item->getKey() ? 0 : -1">
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$item->code"/>
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
                        </x-table.table-item>
                    </a>
                @endforeach
            </x-table.table-layout>
            <x-blocks.main-block>
                {{ $items->withQueryString()->links() }}
            </x-blocks.main-block>
        @else
            <x-blocks.main-block>
                <x-information>Сейчас у вас нет товаров</x-information>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
    <div wire:loading>
        <x-loader/>
    </div>
</div>

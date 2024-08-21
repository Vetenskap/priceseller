<div>
    <x-layouts.header :name="$supplier->name"/>
    <x-layouts.actions>
        <x-primary-button wire:click="back">Закрыть</x-primary-button>
        <x-success-button wire:click="update">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy"
                         wire:confirm="Вы действительно хотите удалить поставщика? Так же будут удалены все связанные с ним товары.">
            Удалить
        </x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('supplier-edit', ['supplier' => $supplier->id, 'page' => 'main'])}}"
                              name="Основное" :active="$page === 'main'"/>
            <x-links.tab-link href="{{route('supplier-edit', ['supplier' => $supplier->id, 'page' => 'price'])}}"
                              name="Прайс" :active="$page === 'price'"/>
        </x-navigate-pages>

        <x-blocks.main-block>
            <x-layouts.title name="Прайс"/>
        </x-blocks.main-block>
        @if($priceItems->isNotEmpty())
            <x-blocks.main-block>
                <x-titles.sub-title name="Фильтры"/>
            </x-blocks.main-block>
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
                        <x-layouts.simple-text name="Статус"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Артикул"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Бренд"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Цена"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Остаток"/>
                    </x-table.table-child>
                </x-table.table-header>
                @foreach($priceItems as $priceItem)
                    <x-table.table-item wire:key="{{$priceItem->getKey()}}" :status="$priceItem->status">
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$priceItem->message"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$priceItem->article"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$priceItem->brand"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$priceItem->price"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$priceItem->stock"/>
                        </x-table.table-child>
                    </x-table.table-item>
                @endforeach
            </x-table.table-layout>
        @endif
    </x-layouts.main-container>
    <div wire:loading wire:target="destroy">
        <x-loader/>
    </div>
</div>

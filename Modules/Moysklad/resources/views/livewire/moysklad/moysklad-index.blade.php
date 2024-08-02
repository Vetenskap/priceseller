<div>
    <x-layouts.header name="Мой склад"/>
    <x-navigate-pages>
        <x-links.tab-link href="{{route('moysklad.index', ['page' => 'main'])}}" :active="$page === 'main'">Основное
        </x-links.tab-link>
        <x-links.tab-link href="{{route('moysklad.index', ['page' => 'warehouses'])}}" :active="$page === 'warehouses'">Склады
        </x-links.tab-link>
        <x-links.tab-link href="{{route('moysklad.index', ['page' => 'items'])}}" :active="$page === 'items'">Товары
        </x-links.tab-link>
        <x-links.tab-link href="{{route('moysklad.index', ['page' => 'suppliers'])}}" :active="$page === 'suppliers'">Поставщики
        </x-links.tab-link>
    </x-navigate-pages>
    @if($page === 'main')
        <x-layouts.module-container x-show="$wire.page == 'main'">
            <x-blocks.main-block>
                <x-success-button wire:click="save">Сохранить</x-success-button>
            </x-blocks.main-block>
            <x-blocks.main-block>
                <x-inputs.input-with-label name="api_key" field="form.api_key" type="text">АПИ ключ</x-inputs.input-with-label>
            </x-blocks.main-block>
        </x-layouts.module-container>
    @endif
    @if($page === 'warehouses')
        <livewire:moysklad::moysklad-warehouse.moysklad-warehouse-index :moysklad="$form->moysklad"/>
    @endif
    @if($page === 'items')
        <livewire:moysklad::moysklad-item.moysklad-item-index :moysklad="$form->moysklad"/>
    @endif
    @if($page === 'suppliers')
        <livewire:moysklad::moysklad-supplier.moysklad-supplier-index :moysklad="$form->moysklad"/>
    @endif
</div>

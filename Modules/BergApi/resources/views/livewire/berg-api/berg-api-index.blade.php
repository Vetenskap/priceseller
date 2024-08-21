<x-layouts.module-index-layout :modules="$modules">
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('bergapi.index', ['page' => 'main'])}}" :active="$page === 'main'">
                Основное
            </x-links.tab-link>
            @if($form->bergApi)
                <x-links.tab-link href="{{route('bergapi.index', ['page' => 'times'])}}" :active="$page === 'times'">
                    Время выгрузки
                </x-links.tab-link>
                <x-links.tab-link href="{{route('bergapi.index', ['page' => 'warehouses'])}}"
                                  :active="$page === 'warehouses'">Склады
                </x-links.tab-link>
                <x-links.tab-link href="{{route('bergapi.index', ['page' => 'attributes'])}}"
                                  :active="$page === 'attributes'">Атрибуты
                </x-links.tab-link>
            @endif
        </x-navigate-pages>
    </x-layouts.main-container>
    @if($page === 'main')
        <x-layouts.main-container>
            <x-blocks.main-block>
                <x-layouts.title name="Основное"/>
            </x-blocks.main-block>
            <x-blocks.main-block>
                <x-success-button wire:click="store">Сохранить</x-success-button>
            </x-blocks.main-block>
            <x-blocks.flex-block>
                <x-dropdowns.dropdown-select
                    :items="auth()->user()->suppliers->toArray()"
                    :current-id="$form->supplier_id"
                    name="supplier"
                    field="form.supplier_id"
                >Выберите Поставщика
                </x-dropdowns.dropdown-select>
                <x-inputs.input-with-label name="api_key"
                                           type="text"
                                           field="form.api_key"
                >Апи ключ
                </x-inputs.input-with-label>
            </x-blocks.flex-block>
        </x-layouts.main-container>
    @elseif($page === 'times')
        <livewire:bergapi::berg-api-time.berg-api-time-index :bergApi="$form->bergApi"/>
    @elseif($page === 'warehouses')
        <livewire:bergapi::berg-api-warehouse.berg-api-warehouse-index :bergApi="$form->bergApi"/>
    @elseif($page === 'attributes')
        <livewire:bergapi::berg-api-item-additional-attribute-link.berg-api-item-additional-attribute-link-index :bergApi="$form->bergApi"/>
    @endif
    <div wire:loading wire:target="store">
        <x-loader/>
    </div>
</x-layouts.module-index-layout>

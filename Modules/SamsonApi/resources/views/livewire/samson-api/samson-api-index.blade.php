<x-layouts.module-index-layout :modules="$modules">
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('samsonapi.index', ['page' => 'main'])}}" :active="$page === 'main'">
                Основное
            </x-links.tab-link>
            @if($form->samsonApi)
                <x-links.tab-link href="{{route('samsonapi.index', ['page' => 'times'])}}" :active="$page === 'times'">
                    Время выгрузки
                </x-links.tab-link>
                <x-links.tab-link href="{{route('samsonapi.index', ['page' => 'attributes'])}}"
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
        <livewire:samsonapi::samson-api-time.samson-api-time-index :samson-api="$form->samsonApi"/>
    @elseif($page === 'attributes')
        <livewire:samsonapi::samson-api-item-additional-attribute-link.samson-api-item-additional-attribute-link-index :samson-api="$form->samsonApi"/>
    @endif
    <div wire:loading wire:target="store">
        <x-loader/>
    </div>
</x-layouts.module-index-layout>

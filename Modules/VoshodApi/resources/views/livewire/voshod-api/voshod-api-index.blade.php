<x-layouts.module-index-layout :modules="$modules">
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('voshodapi.index', ['page' => 'main'])}}" :active="$page === 'main'">
                Основное
            </x-links.tab-link>
            @if($form->voshodApi)
                <x-links.tab-link href="{{route('voshodapi.index', ['page' => 'times'])}}" :active="$page === 'times'">
                    Время выгрузки
                </x-links.tab-link>
                <x-links.tab-link href="{{route('voshodapi.index', ['page' => 'warehouses'])}}"
                                  :active="$page === 'warehouses'">Склады
                </x-links.tab-link>
                <x-links.tab-link href="{{route('voshodapi.index', ['page' => 'attributes'])}}"
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

                <x-inputs.input-with-label name="proxy_ip"
                                           type="text"
                                           field="form.proxy_ip"
                >Прокси (айпи)
                </x-inputs.input-with-label>

                <x-inputs.input-with-label name="proxy_port"
                                           type="number"
                                           field="form.proxy_port"
                >Прокси (порт)
                </x-inputs.input-with-label>

                <x-inputs.input-with-label name="proxy_login"
                                           type="text"
                                           field="form.proxy_login"
                >Прокси (логин)
                </x-inputs.input-with-label>

                <x-inputs.input-with-label name="proxy_password"
                                           type="text"
                                           field="form.proxy_password"
                >Прокси (пароль)
                </x-inputs.input-with-label>

            </x-blocks.flex-block>
        </x-layouts.main-container>
    @elseif($page === 'times')
        <livewire:voshodapi::voshod-api-time.voshod-api-time-index :voshodApi="$form->voshodApi"/>
    @elseif($page === 'warehouses')
        <livewire:voshodapi::voshod-api-warehouse.voshod-api-warehouse-index :voshodApi="$form->voshodApi"/>
    @elseif($page === 'attributes')
        <livewire:voshodapi::voshod-api-item-additional-attribute-link.voshod-api-item-additional-attribute-link-index :voshodApi="$form->voshodApi"/>
    @endif
    <div wire:loading wire:target="store">
        <x-loader/>
    </div>
</x-layouts.module-index-layout>

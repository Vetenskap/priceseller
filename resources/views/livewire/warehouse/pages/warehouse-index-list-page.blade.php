<div>
    <x-layouts.header name="Склады"/>
    <div x-data="{ open: false }">
        <x-layouts.actions>
            <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
        </x-layouts.actions>
        <x-layouts.main-container x-show="open">
            <x-blocks.flex-block-end>
                <x-inputs.input-with-label name="name"
                                           type="text"
                                           field="form.name"
                >Наименование
                </x-inputs.input-with-label>
                <div class="self-center">
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </div>
            </x-blocks.flex-block-end>
        </x-layouts.main-container>
    </div>
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link :href="route('warehouses.index', ['page' => 'list'])" name="Список"
                              :active="$page === 'list'" wire:navigate.hover/>
            <x-links.tab-link :href="route('warehouses.index', ['page' => 'stocks'])" name="Управление остатками"
                              :active="$page === 'stocks'" wire:navigate.hover/>
        </x-navigate-pages>
        <x-blocks.main-block>
            <x-layouts.title name="Список" />
        </x-blocks.main-block>
        @if($warehouses->count() > 0)
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Наименование"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Последнее обновление"/>
                    </x-table.table-child>
                </x-table.table-header>
                @foreach($warehouses as $warehouse)
                    <a href="{{route('warehouses.edit', ['warehouse' => $warehouse->getKey()])}}" wire:key="{{$warehouse->getKey()}}">
                        <x-table.table-item>
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$warehouse->name"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-information>{{$warehouse->updated_at}}</x-information>
                            </x-table.table-child>
                        </x-table.table-item>
                    </a>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-information>Сейчас у вас нет складов</x-information>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>

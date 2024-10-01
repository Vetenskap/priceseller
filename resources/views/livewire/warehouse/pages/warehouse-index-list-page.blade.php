<div>
    <x-layouts.header name="Склады"/>

    <flux:modal name="create-warehouse" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Создание поставщика</flux:heading>
        </div>

        <flux:input wire:model="form.name" label="Наименование" required/>

        <div class="flex">
            <flux:spacer/>

            <flux:button variant="primary" wire:click="store">Создать</flux:button>
        </div>
    </flux:modal>

    <x-layouts.actions>
        <flux:modal.trigger name="create-warehouse">
            <flux:button>Добавить</flux:button>
        </flux:modal.trigger>
    </x-layouts.actions>

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

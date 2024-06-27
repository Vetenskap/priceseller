<div>
    <x-layouts.header name="Склады"/>
    <x-layouts.actions>
        <x-success-button wire:click="add">Добавить</x-success-button>
    </x-layouts.actions>
    @if($showCreateForm)
        <x-layouts.main-container>
            <x-blocks.flex-block-end>
                <x-inputs.input-with-label name="name"
                                           type="text"
                                           field="form.name"
                >Наименование
                </x-inputs.input-with-label>
                <x-success-button wire:click="create">Добавить</x-success-button>
            </x-blocks.flex-block-end>
        </x-layouts.main-container>
    @endif
    <x-layouts.main-container>
        @if($warehouses->count() > 0)
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Наименование"/>
                    </x-table.table-child>
                    <x-table.table-child>

                    </x-table.table-child>
                </x-table.table-header>
                @foreach($warehouses as $warehouse)
                    <x-table.table-item wire:key="{{$warehouse->getKey()}}">
                        <x-table.table-child>
                            <a href="{{route('warehouses.edit', ['warehouse' => $warehouse->getKey()])}}">
                                <x-layouts.simple-text :name="$warehouse->name"/>
                            </a>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-danger-button wire:click="destroy({{$warehouse}})">Удалить</x-danger-button>
                        </x-table.table-child>
                    </x-table.table-item>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-layouts.simple-text name="Сейчас у вас нет складов"/>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>

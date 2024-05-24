<div>
    <x-layouts.header name="ОЗОН"/>
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
                <x-inputs.input-with-label name="client_id"
                                           type="text"
                                           field="form.client_id"
                >Идентификатор клиента
                </x-inputs.input-with-label>
                <x-inputs.input-with-label name="api_key"
                                           type="text"
                                           field="form.api_key"
                >АПИ ключ
                </x-inputs.input-with-label>
                <x-success-button wire:click="create">Добавить</x-success-button>
            </x-blocks.flex-block-end>
        </x-layouts.main-container>
    @endif
    <x-layouts.main-container>
        @if($markets->count() > 0)
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Наименование"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Включен"/>
                    </x-table.table-child>
                    <x-table.table-child>

                    </x-table.table-child>
                </x-table.table-header>
                @foreach($markets as $market)
                    <x-table.table-item wire:key="{{$market->getKey()}}">
                        <x-table.table-child>
                            <a href="{{route('ozon-market-edit', ['market' => $market->getKey()])}}">
                                <x-layouts.simple-text :name="$market->name"/>
                            </a>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-inputs.switcher :checked="$market->open" wire:click="changeOpen({{$market}})"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-danger-button wire:click="destroy({{$market}})">Удалить</x-danger-button>
                        </x-table.table-child>
                    </x-table.table-item>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-layouts.simple-text name="Сейчас у вас нет кабинетов ОЗОН"/>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>

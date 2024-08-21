<div>
    <x-layouts.header name="ОЗОН"/>
    <div x-data="{ open: false }">
        <x-layouts.actions>
            <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
        </x-layouts.actions>
        <x-layouts.main-container x-show="open">
            <x-blocks.main-block>
                <x-layouts.title name="Добавление нового кабинета"/>
            </x-blocks.main-block>
            <x-blocks.flex-block>
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
                <div class="self-center">
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </div>
            </x-blocks.flex-block>
        </x-layouts.main-container>
    </div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Список"/>
        </x-blocks.main-block>
        @if($markets->count() > 0)
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Наименование"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Последнее обновление"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Включен"/>
                    </x-table.table-child>
                </x-table.table-header>
                @foreach($markets as $market)
                    <a href="{{route('ozon-market-edit', ['market' => $market->getKey()])}}"
                       wire:key="{{$market->getKey()}}">
                        <x-table.table-item>
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$market->name"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-information>{{$market->updated_at}}</x-information>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-inputs.switcher :disabled="$market->close" :checked="$market->open"
                                                   wire:click="changeOpen({{json_encode($market->getKey())}})"/>
                            </x-table.table-child>
                        </x-table.table-item>
                    </a>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-information>Сейчас у вас нет кабинетов ОЗОН</x-information>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>

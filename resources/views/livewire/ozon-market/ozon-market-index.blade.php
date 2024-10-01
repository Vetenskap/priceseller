<div>
    <x-layouts.header name="ОЗОН"/>

    <flux:modal name="create-ozon-market" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Создание кабинета</flux:heading>
        </div>

        <flux:input wire:model="form.name" label="Наименование" required/>
        <flux:input wire:model="form.client_id" label="Идентификатор клиента" type="number" required/>
        <flux:input wire:model="form.api_key" label="АПИ ключ" required/>

        <div class="flex">
            <flux:spacer/>

            <flux:button variant="primary" wire:click="store">Создать</flux:button>
        </div>
    </flux:modal>

    <x-layouts.actions>
        <flux:modal.trigger name="create-ozon-market">
            <flux:button>Добавить</flux:button>
        </flux:modal.trigger>
    </x-layouts.actions>

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

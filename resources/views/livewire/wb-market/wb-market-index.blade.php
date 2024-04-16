<div>
    <x-layouts.header name="ВБ"/>
    <x-layouts.actions>
        <x-success-button wire:click="add">Добавить</x-success-button>
    </x-layouts.actions>
    @if($showCreateForm)
        <x-layouts.main-container>
            <x-blocks.flex-block-end>
                <x-inputs.input-with-label name="name"
                                           type="text"
                                           field="form.name"
                >Наименование</x-inputs.input-with-label>
                <x-inputs.input-with-label name="api_key"
                                           type="text"
                                           field="form.api_key"
                >АПИ ключ</x-inputs.input-with-label>
                <x-success-button wire:click="create">Добавить</x-success-button>
            </x-blocks.flex-block-end>
        </x-layouts.main-container>
    @endif
    <x-layouts.main-container>
        @empty($markets->count())
            <x-blocks.main-block>
                <x-layouts.simple-text name="Сейчас у вас нет кабинетов ВБ"/>
            </x-blocks.main-block>
        @endempty
        @foreach($markets as $market)
            <x-table.table-item wire:key="{{$market->getKey()}}">
                <a href="{{route('wb-market-edit', ['market' => $market->getKey()])}}">
                    <x-layouts.simple-text :name="$market->name"/>
                </a>
                <x-danger-button wire:click="destroy({{$market}})">Удалить</x-danger-button>
            </x-table.table-item>
        @endforeach
    </x-layouts.main-container>
</div>

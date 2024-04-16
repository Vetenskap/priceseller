<div>
    <x-layouts.header name="Поставщики"/>
    <x-layouts.actions>
        <x-success-button wire:click="add">Добавить</x-success-button>
    </x-layouts.actions>
    @if($showCreateBlock)
        <x-layouts.main-container>
            <x-blocks.flex-block-end>
                <x-inputs.input-with-label name="name"
                                           type="text"
                                           field="form.name"
                >Наименование
                </x-inputs.input-with-label>
                @if(auth()->user()->is_ms_sub())

                    <x-inputs.input-with-label name="ms_uuid"
                                               type="text"
                                               field="form.ms_uuid"
                    >МС UUID
                    </x-inputs.input-with-label>

                @endif
                <x-success-button wire:click="store">Добавить</x-success-button>

            </x-blocks.flex-block-end>
        </x-layouts.main-container>
    @endif
    <x-layouts.main-container>
        @empty($suppliers->count())
            <x-blocks.main-block>
                <x-layouts.simple-text name="Сейчас у вас нет поставщиков"/>
            </x-blocks.main-block>
        @endempty
        @foreach($suppliers as $supplier)
            <x-table.table-item wire:key="{{$supplier->getKey()}}" wire:poll>
                <a href="{{route('supplier-edit', ['supplier' => $supplier->getKey()])}}" wire:navigate.hover>

                    <x-layouts.simple-text :name="$supplier->name"/>

                </a>
                @if(auth()->user()->is_ms_sub())
                    <a href="{{route('supplier-edit', ['supplier' => $supplier->getKey()])}}" wire:navigate.hover>

                        <x-layouts.simple-text :name="$supplier->ms_uuid"/>

                    </a>
                @endif
                <x-danger-button wire:click="destroy({{$supplier}})">Удалить</x-danger-button>
                <x-inputs.switcher :checked="$supplier->open"/>
            </x-table.table-item>
        @endforeach
    </x-layouts.main-container>
</div>

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
        @if($suppliers->count() > 0)
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
                @foreach($suppliers as $supplier)
                    <x-table.table-item wire:key="{{$supplier->getKey()}}" wire:poll>
                        <x-table.table-child>
                            <a href="{{route('supplier-edit', ['supplier' => $supplier->getKey()])}}" wire:navigate.hover>
                                <x-layouts.simple-text :name="$supplier->name"/>
                            </a>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-inputs.switcher :checked="$supplier->open" wire:click="changeOpen({{$supplier}})"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-danger-button wire:click="destroy({{$supplier}})">Удалить</x-danger-button>
                        </x-table.table-child>
                    </x-table.table-item>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-layouts.simple-text name="Сейчас у вас нет поставщиков"/>
            </x-blocks.main-block>
        @endif

    </x-layouts.main-container>
</div>

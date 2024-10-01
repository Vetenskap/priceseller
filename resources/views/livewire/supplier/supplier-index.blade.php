<div>
    <x-layouts.header name="Поставщики"/>

    <flux:modal name="create-supplier" class="md:w-96 space-y-6">
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
        <flux:modal.trigger name="create-supplier">
            <flux:button>Добавить</flux:button>
        </flux:modal.trigger>
    </x-layouts.actions>

    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Список" />
        </x-blocks.main-block>
        @if($suppliers->count() > 0)
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Наименование"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Последнее обновление"/>
                    </x-table.table-child>
                    <x-table.table-child>

                    </x-table.table-child>
                </x-table.table-header>
                @foreach($suppliers as $supplier)
                    <a href="{{route('supplier.edit', ['supplier' => $supplier->getKey()])}}" wire:key="{{$supplier->getKey()}}">
                        <x-table.table-item>
                            <x-table.table-child>
                                <x-layouts.simple-text :name="$supplier->name"/>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-information>{{$supplier->updated_at}}</x-information>
                            </x-table.table-child>
                            <x-table.table-child>
                                <x-inputs.switcher :checked="$supplier->open" wire:click="changeOpen({{json_encode($supplier->getKey())}})"/>
                            </x-table.table-child>
                        </x-table.table-item>
                    </a>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-information>Сейчас у вас нет поставщиков</x-information>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>

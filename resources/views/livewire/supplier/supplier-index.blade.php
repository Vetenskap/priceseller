<div>
    <x-layouts.header name="Поставщики"/>

    @if($this->user()->can('create-suppliers'))
        <flux:modal name="create-supplier" class="md:w-96 space-y-6">
            <div>
                <flux:heading size="lg">Создание поставщика</flux:heading>
            </div>

            <flux:input wire:model="form.name" label="Наименование" badge="обязательное" required/>

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
    @endif

    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:heading size="xl">Список</flux:heading>
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($this->suppliers->count() > 0)
                <flux:table :paginate="$this->suppliers">
                    <flux:columns>
                        <flux:column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                                     wire:click="sort('name')">Наименование
                        </flux:column>
                        <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                                     wire:click="sort('updated_at')">Последнее обновление
                        </flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach ($this->suppliers as $supplier)
                            <flux:row :key="$supplier->getKey()">
                                <flux:cell class="flex items-center gap-3">
                                    {{ $supplier->name }}
                                </flux:cell>

                                <flux:cell variant="strong">{{ $supplier->updated_at }}</flux:cell>

                                <flux:cell align="right">
                                    <flux:switch :checked="$supplier->open" disabled/>
                                </flux:cell>

                                <flux:cell align="right">
                                    <flux:button :href="route('supplier.edit', ['supplier' => $supplier->getKey()])"
                                                 icon="pencil-square" size="sm"/>
                                </flux:cell>

                                @if($this->user()->can('delete-suppliers'))
                                    <flux:cell align="right">
                                        <flux:button icon="trash" variant="danger" size="sm"
                                                     wire:click="destroy({{ json_encode($supplier->getKey()) }})"
                                                     wire:target="destroy({{ json_encode($supplier->getKey()) }})"
                                                     wire:confirm="Вы действительно хотите удалить этого поставщика? Это действие нельзя будет отменить. Так же удалятся все связанные товары, их связи т.д."/>
                                    </flux:cell>
                                @endif

                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @else
                <flux:subheading>Сейчас у вас нет поставщиков</flux:subheading>
            @endif
        </x-blocks.main-block>
    </x-layouts.main-container>
</div>

<div>
    <x-layouts.header name="Склады"/>
    <flux:modal name="create-warehouse" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Создание поставщика</flux:heading>
        </div>

        <flux:input wire:model="form.name" label="Наименование" required badge="обязательное"/>

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
        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="list" icon="user">Список</flux:tab>
                    <flux:tab name="manage" icon="cog-6-tooth">Управление остатками</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="list">
                <x-blocks.main-block>
                    @if($this->warehouses->count() > 0)
                        <flux:table :paginate="$this->warehouses">
                            <flux:columns>
                                <flux:column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                                             wire:click="sort('name')">Склад
                                </flux:column>
                                <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                                             wire:click="sort('updated_at')">Последнее обновление
                                </flux:column>
                            </flux:columns>

                            <flux:rows>
                                @foreach ($this->warehouses as $warehouse)
                                    <flux:row :key="$warehouse->id">
                                        <flux:cell class="flex items-center gap-3">
                                            {{ $warehouse->name }}
                                        </flux:cell>

                                        <flux:cell variant="strong">{{ $warehouse->updated_at }}</flux:cell>

                                        <flux:cell align="right">
                                            <flux:button icon="pencil-square" size="sm"
                                                         href="{{ route('warehouses.edit', ['warehouse' => $warehouse->getKey()]) }}" />
                                        </flux:cell>

                                        <flux:cell align="right">
                                            <flux:button icon="trash" variant="danger" size="sm"
                                                         wire:click="destroy({{ json_encode($warehouse->getKey()) }})"
                                                         wire:target="destroy({{ json_encode($warehouse->getKey()) }})"
                                                         wire:confirm="Вы действительно хотите удалить этот склад?"
                                            />
                                        </flux:cell>

                                    </flux:row>
                                @endforeach
                            </flux:rows>
                        </flux:table>
                    @else
                        <flux:subheading>Сейчас у вас нет складов</flux:subheading>
                    @endif
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="manage">
                <livewire:warehouses-items-export.warehouses-items-export-index :model="$this->currentUser()"/>
                <livewire:warehouses-items-import.warehouses-items-import-index :model="$this->currentUser()"/>
            </flux:tab.panel>
        </flux:tab-group>
    </x-layouts.main-container>
</div>

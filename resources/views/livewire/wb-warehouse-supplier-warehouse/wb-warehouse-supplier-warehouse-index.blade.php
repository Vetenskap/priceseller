<flux:card class="space-y-6">
    <flux:heading size="xl">Склады</flux:heading>
    <flux:subheading>Склады поставщика с которых нужно выгружать остатки. Если добавить несколько складов, то они
        будут складываться
    </flux:subheading>
    <flux:input.group>
        <flux:select variant="listbox" searchable placeholder="Выберите склад..." wire:model="supplier_warehouse_id">
            <x-slot name="search">
                <flux:select.search placeholder="Поиск..."/>
            </x-slot>

            @foreach($supplier->supplier->warehouses->all() as $warehouse)
                <flux:option :value="$warehouse->id">{{$warehouse->name}}</flux:option>
            @endforeach
        </flux:select>

        <flux:button icon="plus" wire:click="store">Добавить</flux:button>
    </flux:input.group>

    <flux:heading size="xl">Список</flux:heading>
    @if($this->warehouses->isNotEmpty())
        <flux:table :paginate="$this->warehouses">
            <flux:columns>
                <flux:column>Склад</flux:column>
            </flux:columns>

            <flux:rows>
                @foreach ($this->warehouses as $warehouse)
                    <flux:row :key="$warehouse->id">
                        <flux:cell class="flex items-center gap-3">
                            {{ $warehouse->supplierWarehouse->name }}
                        </flux:cell>

                        <flux:cell align="right">
                            <flux:icon.trash wire:click="destroy({{ json_encode($warehouse->getKey()) }})"
                                             wire:loading.remove
                                             wire:target="destroy({{ json_encode($warehouse->getKey()) }})"
                                             class="cursor-pointer hover:text-red-400"/>
                            <flux:icon.loading wire:loading
                                               wire:target="destroy({{ json_encode($warehouse->getKey()) }})"/>
                        </flux:cell>

                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    @else
        <flux:subheading>Вы пока ещё не добавляли склады</flux:subheading>
    @endif
</flux:card>

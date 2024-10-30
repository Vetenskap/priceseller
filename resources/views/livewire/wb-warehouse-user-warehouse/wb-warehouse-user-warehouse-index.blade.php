<div>
    <flux:card class="space-y-6">
        @if($this->user()->can('update-wb'))
            <flux:input.group>
                <flux:select variant="combobox" placeholder="Выберите склад..." wire:model="user_warehouse_id">

                    @foreach($this->currentUser()->warehouses as $warehouse)
                        <flux:option :value="$warehouse->getKey()">{{$warehouse->name}}</flux:option>
                    @endforeach
                </flux:select>

                <flux:button icon="plus" wire:click="store">Добавить</flux:button>
            </flux:input.group>
        @endif
        <flux:heading size="xl">Список</flux:heading>
        @if($this->userWarehouses()->count() > 0)
            <flux:table :paginate="$this->userWarehouses">
                <flux:columns>
                    <flux:column>Склад</flux:column>
                </flux:columns>

                <flux:rows>
                    @foreach ($this->userWarehouses as $userWarehouse)
                        <flux:row :key="$userWarehouse->getKey()">
                            <flux:cell class="flex items-center gap-3">
                                {{ $userWarehouse->warehouse->name }}
                            </flux:cell>

                            @if($this->user()->can('update-wb'))
                                <flux:cell align="right">
                                    <flux:icon.trash wire:click="destroy({{ json_encode($userWarehouse->getKey()) }})"
                                                     wire:loading.remove
                                                     wire:target="destroy({{ json_encode($userWarehouse->getKey()) }})"
                                                     wire:confirm="Вы действительно хотите удалить этот склад?"
                                                     class="cursor-pointer hover:text-red-400"/>
                                    <flux:icon.loading wire:loading
                                                       wire:target="destroy({{ json_encode($userWarehouse->getKey()) }})"/>
                                </flux:cell>
                            @endif

                        </flux:row>
                    @endforeach
                </flux:rows>
            </flux:table>
        @else
            <flux:subheading>Вы ещё не добавляли свои склады</flux:subheading>
        @endif
    </flux:card>
</div>

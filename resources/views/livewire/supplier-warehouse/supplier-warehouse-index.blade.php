<div>
    @if($this->user()->can('update-suppliers'))
        <x-blocks.main-block class="w-[365px]">
            <flux:input.group>
                <flux:input placeholder="Введите наименование" wire:model="form.name"/>

                <flux:button icon="plus" wire:click="store">Создать</flux:button>
            </flux:input.group>
        </x-blocks.main-block>
    @endif
    <x-blocks.main-block>
        <flux:table :paginate="$this->warehouses">

            <flux:columns>
                <flux:column>Наименование</flux:column>
            </flux:columns>

            <flux:rows>
                @foreach($this->warehouses as $warehouse)
                    <flux:row :key="$warehouse->getKey()">
                        <flux:cell>{{$warehouse->name}}</flux:cell>
                        @if($this->user()->can('update-suppliers'))
                            <flux:cell>
                                <flux:icon.trash wire:click="destroy({{ json_encode($warehouse->getKey()) }})"
                                                 wire:loading.remove
                                                 wire:target="destroy({{ json_encode($warehouse->getKey()) }})"
                                                 wire:confirm="Вы действительно хотите удалить этот склад?"
                                                 class="hover:text-red-400 cursor-pointer"/>
                                <flux:icon.loading wire:loading wire:target="destroy({{ json_encode($warehouse->getKey()) }})"/>
                            </flux:cell>
                        @endif
                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    </x-blocks.main-block>
</div>

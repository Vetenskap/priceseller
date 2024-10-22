<div>
    @if($this->user()->can('update-warehouses'))
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:heading size="xl">Добавить остаток</flux:heading>
                <div>
                    <flux:select variant="listbox" searchable placeholder="Выберите товар..." :filter="false"
                                 wire:model.live="item_id">
                        <x-slot name="search">
                            <flux:select.search placeholder="Введите код или наименование товара..."
                                                wire:model.live="searchItems"/>
                        </x-slot>

                        <flux:icon.loading wire:loading wire:target="searchItems"/>

                        @if($items)
                            @foreach($items as $item)
                                <flux:option :value="$item->getKey()">({{$item->code}}) {{$item->name}}</flux:option>
                            @endforeach
                        @endif
                    </flux:select>
                </div>
                <flux:input wire:model="stock" label="Остаток" required type="number"/>
                <flux:button wire:click="store">Добавить</flux:button>
            </flux:card>
        </x-blocks.main-block>
    @endif
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Все остатки</flux:heading>
            @if($this->stocks->isNotEmpty())
                <flux:table :paginate="$this->stocks">
                    <flux:columns>
                        <flux:column>Товар</flux:column>
                        <flux:column>Остаток</flux:column>
                        <flux:column>Дата создания</flux:column>
                        <flux:column>Дата обновления</flux:column>
                    </flux:columns>
                    <flux:rows>
                        @foreach($this->stocks as $stock)
                            <flux:row :key="$stock->getKey()">
                                <flux:cell>
                                    <flux:link :href="route('item-edit', ['item' => $stock->item_id])">{{$stock->item->code}}</flux:link>
                                </flux:cell>
                                <flux:cell>{{$stock->stock}}</flux:cell>
                                <flux:cell>{{$stock->created_at}}</flux:cell>
                                <flux:cell>{{$stock->updated_at}}</flux:cell>
                                @if($this->user()->can('update-warehouses'))
                                    <flux:cell>
                                        <flux:button
                                            icon="trash"
                                            variant="danger"
                                            wire:click="destroy({{$stock->getKey()}})"
                                            size="sm"
                                            wire:target="destroy({{$stock->getKey()}})"
                                            wire:confirm="Вы действительно хотите удалить этот остаток?"
                                        />
                                    </flux:cell>
                                @endif
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @endif
        </flux:card>
    </x-blocks.main-block>
</div>

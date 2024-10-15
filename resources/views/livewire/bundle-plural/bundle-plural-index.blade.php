<div>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
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
            <flux:input type="number" wire:model.live="multiplicity" required label="Кратность отгрузки"/>
            <flux:button wire:click="store">Связать</flux:button>
        </flux:card>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Список</flux:heading>
            @if($bundle->items->isNotEmpty())
                <flux:table>
                    <flux:columns>
                        <flux:column>Товар</flux:column>
                        <flux:column>Кратность отгрузки</flux:column>
                        <flux:column>Дата создания</flux:column>
                        <flux:column>Дата обновления</flux:column>
                    </flux:columns>
                    <flux:rows>
                        @foreach($bundle->items as $item)
                            <flux:row :key="$item->getKey()">
                                <flux:cell>
                                    <flux:link :href="route('item-edit', ['item' => $item->getKey()])">{{$item->code}}</flux:link>
                                </flux:cell>
                                <flux:cell>{{$item->pivot->multiplicity}}</flux:cell>
                                <flux:cell>{{$item->pivot->created_at}}</flux:cell>
                                <flux:cell>{{$item->pivot->updated_at}}</flux:cell>
                                <flux:cell>
                                    <flux:button icon="trash" variant="danger" size="sm" wire:click="destroy({{json_encode($item->getKey())}})" wire:target="destroy({{json_encode($item->getKey())}})"/>
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @endif
        </flux:card>
    </x-blocks.main-block>
</div>

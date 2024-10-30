<div>
    <x-layouts.header :name="$item->product_id"/>
    <x-layouts.actions>
        @if($this->user()->can('update-ozon'))
            <flux:button wire:click="update">Сохранить</flux:button>
            <flux:button
                variant="danger"
                wire:click="destroy"
                wire:confirm="Вы действительно хотите удалить этот товар?"
            >Удалить</flux:button>
        @endif
    </x-layouts.actions>
    <x-layouts.main-container>
        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="general">Главное</flux:tab>
                    <flux:tab name="commissions">Комиссии</flux:tab>
                    <flux:tab name="stocks">Остатки</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="general">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:input wire:model="form.offer_id" label="Артикул клиента" />
                        <flux:input wire:model="form.product_id" label="Идентификатор товара" />
                        <div>
                            <flux:select variant="listbox" placeholder="Выберите тип..." label="Тип" wire:model.live="form.ozonitemable_type">
                                <flux:option value="App\Models\Item">Товар</flux:option>
                                <flux:option value="App\Models\Bundle">Комплект</flux:option>
                            </flux:select>
                        </div>
                        <flux:input wire:model.live="searchItems" label="Товар/Комплект" placeholder="Поиск..."/>
                        <div>
                            <flux:select variant="listbox" searchable placeholder="Выберите товар..." :filter="false"
                                         wire:model="form.ozonitemable_id">
                                <x-slot name="search">
                                    <flux:select.search placeholder="Введите код или наименование товара..."/>
                                </x-slot>

                                <flux:icon.loading wire:loading wire:target="searchItems"/>

                                @if($items)
                                    @foreach($items as $userItem)
                                        <flux:option :value="$userItem->getKey()">({{$userItem->code}}) {{$userItem->name}}</flux:option>
                                    @endforeach
                                @endif
                            </flux:select>
                        </div>
                    </flux:card>
                </x-blocks.main-block>

            </flux:tab.panel>
            <flux:tab.panel name="commissions">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:input wire:model="form.deliv_to_customer" label="Последняя миля" />
                        <flux:input wire:model="form.direct_flow_trans" label="Магистраль" />
                        <flux:input wire:model="form.min_price" label="Мин. Цена" />
                        <flux:input wire:model="form.min_price_percent" label="Мин. Цена, процент" />
                        <flux:input wire:model="form.price_seller" label="Цена конкурента" />
                        <flux:input wire:model="form.sales_percent" label="Комиссия" />
                        <flux:input wire:model="form.shipping_processing" label="Обработка отправления" />
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="stocks">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:table>
                            <flux:columns>
                                <flux:column>Склад</flux:column>
                                <flux:column>Остаток</flux:column>
                                <flux:column>Дата создания</flux:column>
                                <flux:column>Дата обновления</flux:column>
                            </flux:columns>
                            <flux:rows>
                                @foreach($item->stocks as $stock)
                                    <flux:row :key="$stock->getKey()">
                                        <flux:cell>{{$stock->warehouse->name}}</flux:cell>
                                        <flux:cell>{{$stock->stock}}</flux:cell>
                                        <flux:cell>{{$stock->created_at}}</flux:cell>
                                        <flux:cell>{{$stock->updated_at}}</flux:cell>
                                    </flux:row>
                                @endforeach
                            </flux:rows>
                        </flux:table>
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
        </flux:tab.group>
    </x-layouts.main-container>
</div>

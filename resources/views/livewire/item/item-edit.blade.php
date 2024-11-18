<div>
    <x-layouts.header :name="$item->name . ' (' . $item->code . ')'"/>
    <x-layouts.actions>
        @if($this->user()->can('delete-items'))
            <flux:button
                    variant="danger"
                    wire:click="destroy"
                    wire:confirm="Вы действительно хотите удалить этот товар?"
            >Удалить
            </flux:button>
        @endif
    </x-layouts.actions>
    <x-layouts.main-container>
        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="general">Главное</flux:tab>
                    <flux:tab name="stocks">Остатки</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="general">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <div class="flex gap-12">
                            <div class="space-y-6">
                                <flux:heading size="xl">Основная информация</flux:heading>
                                <flux:input wire:model.live="form.name" label="Наименование"/>
                                <flux:input wire:model.live="form.code" label="Код" required/>
                                <flux:input wire:model.live="form.ms_uuid" label="МС UUID"/>
                                <flux:input wire:model.live="form.article" label="Артикул поставщик" required/>
                                <flux:input wire:model.live="form.brand" label="Бренд поставщик"/>
                                <flux:input wire:model.live="form.multiplicity" label="Кратность отгрузки" type="number"
                                            required/>
                                <flux:input wire:model.live="form.buy_price_reserve" label="Резервная закупочная цена"
                                            type="number"/>
                                <flux:select variant="combobox" placeholder="Выберите поставщика..."
                                             wire:model.live="form.supplier_id" label="Поставщик">
                                    @foreach($this->currentUser()->suppliers as $supplier)
                                        <flux:option value="{{ $supplier->id }}">{{$supplier->name}}</flux:option>
                                    @endforeach
                                </flux:select>
                                <flux:switch wire:model.live="form.unload_wb" label="Выгружать на ВБ"/>
                                <flux:switch wire:model.live="form.unload_ozon" label="Выгружать на ОЗОН"/>
                            </div>
                            <div class="space-y-6">
                                <flux:heading size="xl">Дополнительные поля</flux:heading>
                                @foreach($this->currentUser()->itemAttributes()->whereNotIn('id', $item->attributesValues->pluck('item_attribute_id'))->get() as $mainAttribute)
                                    @switch($mainAttribute->type)
                                        @case('boolean')
                                            <div class="flex justify-between items-center">
                                                <flux:switch wire:model.live="form.attributes.{{$mainAttribute->id}}"
                                                             :label="$mainAttribute->name"/>
                                                @if($this->user()->can('update-items'))
                                                    <flux:button wire:click="deleteAttribute({{$mainAttribute}})"
                                                                 variant="danger"
                                                                 icon="x-mark"
                                                                 wire:confirm="Вы действительно хотите удалить этот атрибут? Удаление произойдет со всех товаров"></flux:button>
                                                @endif

                                            </div>
                                            @break
                                        @case('textarea')
                                            <div class="flex justify-between items-end">
                                                <flux:textarea wire:model.live="form.attributes.{{$mainAttribute->id}}"
                                                               label="{{$mainAttribute->name}}"/>
                                                @if($this->user()->can('update-items'))
                                                    <flux:button wire:click="deleteAttribute({{$mainAttribute}})"
                                                                 variant="danger"
                                                                 icon="x-mark"
                                                                 wire:confirm="Вы действительно хотите удалить этот атрибут? Удаление произойдет со всех товаров"></flux:button>
                                                @endif

                                            </div>
                                            @break
                                        @default
                                            <flux:input.group class="items-end">
                                                <flux:input wire:model.live="form.attributes.{{$mainAttribute->id}}"
                                                            label="{{$mainAttribute->name}}"
                                                            type="{{$mainAttribute->type}}"/>

                                                @if($this->user()->can('update-items'))
                                                    <flux:button wire:click="deleteAttribute({{$mainAttribute}})"
                                                                 variant="danger"
                                                                 icon="x-mark"
                                                                 wire:confirm="Вы действительно хотите удалить этот атрибут? Удаление произойдет со всех товаров"></flux:button>
                                                @endif

                                            </flux:input.group>
                                            @break
                                    @endswitch
                                @endforeach
                                @foreach($item->attributesValues as $attribute)
                                    @switch($attribute->attribute->type)
                                        @case('boolean')
                                            <div class="flex justify-between items-center">
                                                <flux:switch
                                                        wire:model.live="form.attributes.{{$attribute->item_attribute_id}}"
                                                        :label="$attribute->attribute->name"/>
                                                @if($this->user()->can('update-items'))
                                                    <flux:button wire:click="deleteAttribute({{$attribute->attribute}})"
                                                                 variant="danger" icon="x-mark"
                                                                 wire:confirm="Вы действительно хотите удалить этот атрибут? Удаление произойдет со всех товаров"></flux:button>
                                                @endif

                                            </div>
                                            @break
                                        @case('textarea')
                                            <div class="flex justify-between items-end">
                                                <flux:textarea
                                                        wire:model.live="form.attributes.{{$attribute->item_attribute_id}}"
                                                        label="{{$attribute->attribute->name}}"/>
                                                @if($this->user()->can('update-items'))
                                                    <flux:button wire:click="deleteAttribute({{$attribute->attribute}})"
                                                                 variant="danger" icon="x-mark"
                                                                 wire:confirm="Вы действительно хотите удалить этот атрибут? Удаление произойдет со всех товаров"></flux:button>
                                                @endif

                                            </div>
                                            @break
                                        @default
                                            <flux:input.group class="items-end">
                                                <flux:input
                                                        wire:model.live="form.attributes.{{$attribute->item_attribute_id}}"
                                                        label="{{$attribute->attribute->name}}"
                                                        type="{{$attribute->attribute->type}}"/>
                                                @if($this->user()->can('update-items'))
                                                    <flux:button variant="danger" icon="x-mark"
                                                                 wire:confirm="Вы действительно хотите удалить этот атрибут? Удаление произойдет со всех товаров"
                                                                 wire:click="deleteAttribute({{$attribute->attribute}})"></flux:button>
                                                @endif

                                            </flux:input.group>
                                            @break
                                    @endswitch
                                @endforeach
                                @if($this->user()->can('update-items'))
                                    <livewire:item.item-attribute-dialog-form/>
                                @endif
                            </div>
                        </div>
                    </flux:card>
                </x-blocks.main-block>
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <div class="flex gap-12">
                            <div class="space-y-6">
                                <flux:heading size="xl">Прочая информация</flux:heading>
                                <flux:input.group>
                                    <flux:input :value="$item->price" readonly/>

                                    <flux:input.group.suffix>₽</flux:input.group.suffix>
                                </flux:input.group>
                                <div class="flex items-center gap-4">
                                    <flux:subheading>Был обновлён:</flux:subheading>
                                    <flux:badge
                                            :color="$item->updated ? 'lime' : 'red'">{{$item->updated ? 'Да' : 'Нет'}}</flux:badge>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <flux:heading size="xl">Остатки</flux:heading>
                                @foreach($item->supplierWarehouseStocks as $stock)
                                    <div class="flex gap-6">
                                        <flux:input :value="$stock->warehouse->name" readonly/>
                                        <flux:input.group>
                                            <flux:input :value="$stock->stock" readonly/>

                                            <flux:input.group.suffix>шт</flux:input.group.suffix>
                                        </flux:input.group>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </flux:card>
                </x-blocks.main-block>

                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <div class="flex">
                            <div class="space-y-6">
                                <flux:heading size="xl">Информация из прайса</flux:heading>
                                <div class="flex items-center gap-4">
                                    <flux:subheading>Статус:</flux:subheading>
                                    <flux:badge
                                            :color="$item->fromPrice ? 'lime' : 'red'">{{$item->fromPrice ? $item->fromPrice->message : "Товар не найден"}}</flux:badge>
                                </div>
                                @if($item->fromPrice)
                                    <flux:input label="Артикул" :value="$item->fromPrice->article" readonly/>
                                    <flux:input label="Бренд" :value="$item->fromPrice->brand" readonly/>
                                    <flux:input.group>
                                        <flux:input :value="$item->fromPrice->price" readonly/>

                                        <flux:input.group.suffix>₽</flux:input.group.suffix>
                                    </flux:input.group>
                                    <flux:input.group>
                                        <flux:input :value="$item->fromPrice->stock" readonly/>

                                        <flux:input.group.suffix>шт</flux:input.group.suffix>
                                    </flux:input.group>
                                @endif
                            </div>
                        </div>
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="stocks">
                <x-blocks.main-block>
                    <flux:table>
                        <flux:columns>
                            <flux:column>Склад</flux:column>
                            <flux:column>Остаток</flux:column>
                        </flux:columns>
                        <flux:rows>
                            @foreach($item->warehousesStocks as $warehouseStock)
                                <flux:row :key="$warehouseStock->getKey()">
                                    <flux:cell>{{$warehouseStock->warehouse->name}}</flux:cell>
                                    <flux:cell>{{$warehouseStock->stock}}</flux:cell>
                                </flux:row>
                            @endforeach
                        </flux:rows>
                    </flux:table>
                </x-blocks.main-block>
            </flux:tab.panel>
        </flux:tab.group>
    </x-layouts.main-container>
    @if($this->user()->can('update-items'))
        {!! $this->renderSaveButton() !!}
    @endif
</div>

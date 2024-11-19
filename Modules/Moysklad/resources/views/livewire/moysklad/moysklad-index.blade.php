<x-layouts.module-index-layout :modules="$modules">
    <x-blocks.main-block>
        <flux:navbar>
            <flux:navbar.item :href="route('moysklad.index', ['page' => 'main'])" :current="$page === 'main'">
                Основное
            </flux:navbar.item>
            @if($form->moysklad)
                <flux:navbar.item :href="route('moysklad.index', ['page' => 'warehouses'])"
                                  :current="$page === 'warehouses'">Склады
                </flux:navbar.item>
                <flux:navbar.item :href="route('moysklad.index', ['page' => 'suppliers'])"
                                  :current="$page === 'suppliers'">Поставщики
                </flux:navbar.item>
                <flux:navbar.item :href="route('moysklad.index', ['page' => 'organizations'])"
                                  :current="$page === 'organizations'">Организации
                </flux:navbar.item>
                <flux:navbar.item :href="route('moysklad.index', ['page' => 'items'])" :current="$page === 'items'">
                    Товары
                </flux:navbar.item>
                <flux:navbar.item :href="route('moysklad.index', ['page' => 'bundles'])" :current="$page === 'bundles'">
                    Комплекты
                </flux:navbar.item>
                <flux:navbar.item :href="route('moysklad.index', ['page' => 'webhooks'])"
                                  :current="$page === 'webhooks'">Вебхуки
                </flux:navbar.item>
                <flux:navbar.item :href="route('moysklad.index', ['page' => 'orders'])" :current="$page === 'orders'">
                    Заказы
                </flux:navbar.item>
                <flux:navbar.item :href="route('moysklad.index', ['page' => 'change_warehouse'])"
                                  :current="$page === 'change_warehouse'">Задача изменения склада
                </flux:navbar.item>
                <flux:navbar.item :href="route('moysklad.index', ['page' => 'quarantine'])"
                                  :current="$page === 'quarantine'" :badge="$this->quarantine->total()"
                                  badge-color="red">Карантин
                </flux:navbar.item>
            @endif
        </flux:navbar>
    </x-blocks.main-block>
    @if($page === 'main')
        <x-blocks.main-block>
            <flux:card>
                <flux:input label="АПИ ключ" wire:model.live="form.api_key" required/>
            </flux:card>
        </x-blocks.main-block>
        @if($form->moysklad)
            <livewire:moysklad::moysklad-recount-retail-markup.moysklad-recount-retail-markup-index
                :moysklad="$form->moysklad"/>
        @endif
        {!! $this->renderSaveButton() !!}
    @endif
    @if($form->moysklad)
        @if($page === 'warehouses')
            <livewire:moysklad::moysklad-warehouse.moysklad-warehouse-index :moysklad="$form->moysklad"/>
        @endif
        @if($page === 'items')
            <livewire:moysklad::moysklad-item.moysklad-item-index :moysklad="$form->moysklad"/>
        @endif
        @if($page === 'bundles')
            <livewire:moysklad::moysklad-bundle.moysklad-bundle-index :moysklad="$form->moysklad"/>
        @endif
        @if($page === 'suppliers')
            <livewire:moysklad::moysklad-supplier.moysklad-supplier-index :moysklad="$form->moysklad"/>
        @endif
        @if($page === 'organizations')
            <livewire:moysklad::moysklad-organization.moysklad-organization-index :moysklad="$form->moysklad"/>
        @endif
        @if($page === 'orders')
            <livewire:moysklad::moysklad-item-order.moysklad-item-order-index :moysklad="$form->moysklad"/>
        @endif
        @if($page === 'webhooks')
            <livewire:moysklad::moysklad-webhook.moysklad-webhook-index :moysklad="$form->moysklad"/>
        @endif
        @if($page === 'change_warehouse')
            <livewire:moysklad::moysklad-change-warehouse.moysklad-change-warehouse-index :moysklad="$form->moysklad"/>
        @endif
        @if($page === 'quarantine')
            <x-blocks.main-block>
                <flux:card class="space-y-6">
                    <div class="flex">
                        <div class="space-y-6">
                            <flux:switch label="Включить карантин" wire:model.live="form.enabled_diff_price"/>
                            <flux:input label="Разница между ценами, %" wire:model.live="form.diff_price"/>
                        </div>
                    </div>
                </flux:card>
            </x-blocks.main-block>
            <x-blocks.main-block>
                <flux:card class="space-y-6">
                    <div class="flex justify-between">
                        <flux:heading size="xl">Карантин</flux:heading>
                        <flux:button wire:click="unloadQuarantine">Выгрузить всё в МС</flux:button>
                    </div>
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Фильтры</flux:heading>
                        <div class="flex flex-wrap gap-6">
                            <flux:input wire:model.live.debounce.2s="filters.price_difference_from"
                                        label="Разница в % от"/>
                            <flux:input wire:model.live.debounce.2s="filters.price_difference_to"
                                        label="Разница в % до"/>
                        </div>
                    </flux:card>
                    <flux:table :paginate="$this->quarantine">
                        <flux:columns>
                            <flux:column>Товар</flux:column>
                            <flux:column sortable :sorted="$sortBy === 'supplier_buy_price'" :direction="$sortDirection"
                                         wire:click="sort('supplier_buy_price')">Цена поставщика
                            </flux:column>
                            <flux:column sortable :sorted="$sortBy === 'items.buy_price_reserve'"
                                         :direction="$sortDirection"
                                         wire:click="sort('items.buy_price_reserve')">Ваша цена
                            </flux:column>
                            <flux:column sortable :sorted="$sortBy === 'price_difference'" :direction="$sortDirection"
                                         wire:click="sort('price_difference')">Разница в %
                            </flux:column>
                            <flux:column>Дата создания</flux:column>
                            <flux:column>Дата обновления</flux:column>
                        </flux:columns>
                        <flux:rows>
                            @foreach($this->quarantine as $item)
                                <flux:row :key="$item->getKey()">
                                    <flux:cell>
                                        <flux:link href="{{ route('item-edit', ['item' => $item->item->getKey()]) }}">
                                            {{$item->item->code}}
                                        </flux:link>
                                    </flux:cell>
                                    <flux:cell>{{$item->supplier_buy_price}}</flux:cell>
                                    <flux:cell>{{$item->item->buy_price_reserve}}</flux:cell>
                                    <flux:cell>{{intval($item->price_difference)}}</flux:cell>
                                    <flux:cell>{{$item->created_at}}</flux:cell>
                                    <flux:cell>{{$item->updated_at}}</flux:cell>
                                    <flux:cell>
                                        <flux:tooltip content="Выгрузить в МС">
                                            <flux:button icon="arrow-up-tray"
                                                         wire:click="setBuyPriceFromQuarantine({{$item->getKey()}})"
                                                         wire:target="setBuyPriceFromQuarantine({{$item->getKey()}})"></flux:button>
                                        </flux:tooltip>
                                    </flux:cell>
                                </flux:row>
                            @endforeach
                        </flux:rows>
                    </flux:table>
                </flux:card>
            </x-blocks.main-block>
            {!! $this->renderSaveButton() !!}
        @endif
    @endif
</x-layouts.module-index-layout>

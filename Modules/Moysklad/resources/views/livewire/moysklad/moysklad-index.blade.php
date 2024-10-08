<x-layouts.module-index-layout :modules="$modules">
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('moysklad.index', ['page' => 'main'])}}" :active="$page === 'main'">Основное
            </x-links.tab-link>
            @if($form->moysklad)
                <x-links.tab-link href="{{route('moysklad.index', ['page' => 'warehouses'])}}" :active="$page === 'warehouses'">Склады
                </x-links.tab-link>
                <x-links.tab-link href="{{route('moysklad.index', ['page' => 'items'])}}" :active="$page === 'items'">Товары
                </x-links.tab-link>
                <x-links.tab-link href="{{route('moysklad.index', ['page' => 'bundles'])}}" :active="$page === 'bundles'">Комплекты
                </x-links.tab-link>
                <x-links.tab-link href="{{route('moysklad.index', ['page' => 'suppliers'])}}" :active="$page === 'suppliers'">Поставщики
                </x-links.tab-link>
                <x-links.tab-link href="{{route('moysklad.index', ['page' => 'organizations'])}}" :active="$page === 'organizations'">Организации
                </x-links.tab-link>
                <x-links.tab-link href="{{route('moysklad.index', ['page' => 'webhooks'])}}" :active="$page === 'webhooks'">Вебхуки
                </x-links.tab-link>
                <x-links.tab-link href="{{route('moysklad.index', ['page' => 'orders'])}}" :active="$page === 'orders'">Заказы
                </x-links.tab-link>
                <x-links.tab-link href="{{route('moysklad.index', ['page' => 'change_warehouse'])}}" :active="$page === 'change_warehouse'">Задача изменения склада
                </x-links.tab-link>
                <x-links.tab-link href="{{route('moysklad.index', ['page' => 'quarantine'])}}" :active="$page === 'quarantine'">Карантин
                </x-links.tab-link>
            @endif
        </x-navigate-pages>
    </x-layouts.main-container>
    @if($page === 'main')
        <x-layouts.main-container>
            <x-blocks.main-block>
                <x-layouts.title name="Основное" />
            </x-blocks.main-block>
            <x-blocks.main-block>
                <x-success-button wire:click="store">Сохранить</x-success-button>
            </x-blocks.main-block>
            <x-blocks.main-block>
                <x-inputs.input-with-label name="api_key" field="form.api_key" type="text">АПИ ключ</x-inputs.input-with-label>
            </x-blocks.main-block>
        </x-layouts.main-container>
    @endif
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
        <x-layouts.main-container>
            <x-blocks.main-block>
                <flux:card>
                    <flux:button wire:click="store">Сохранить</flux:button>
                </flux:card>
            </x-blocks.main-block>
            <x-blocks.main-block>
                <flux:card class="space-y-6">
                    <div class="flex">
                        <div class="space-y-6">
                            <flux:switch label="Включить карантин" wire:model="form.enabled_diff_price" />
                            <flux:input label="Разница между ценами, %" wire:model="form.diff_price"/>
                        </div>
                    </div>
                </flux:card>
            </x-blocks.main-block>
            <x-blocks.main-block>
                <flux:card class="space-y-6">
                    <div class="flex justify-between">
                        <flux:heading size="xl">Карантин</flux:heading>
                        <flux:button wire:click="unloadQuarantine">Выгрузить всё</flux:button>
                    </div>
                    <flux:table :paginate="$this->quarantine">
                        <flux:columns>
                            <flux:column>Товар</flux:column>
                            <flux:column>Цена поставщика</flux:column>
                            <flux:column>Ваша цена</flux:column>
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
                                    <flux:cell>{{$item->created_at}}</flux:cell>
                                    <flux:cell>{{$item->updated_at}}</flux:cell>
                                    <flux:cell>
                                        <flux:icon.arrow-up-tray class="cursor-pointer hover:text-gray-800"
                                                                 wire:click="setBuyPriceFromQuarantine({{$item->getKey()}})"
                                                                 wire:loading.remove
                                                                 wire:target="setBuyPriceFromQuarantine({{$item->getKey()}}),unloadQuarantine"
                                        />
                                        <flux:icon.loading wire:loading wire:target="setBuyPriceFromQuarantine({{$item->getKey()}}),unloadQuarantine"/>
                                    </flux:cell>
                                </flux:row>
                            @endforeach
                        </flux:rows>
                    </flux:table>
                </flux:card>
            </x-blocks.main-block>
        </x-layouts.main-container>
    @endif
</x-layouts.module-index-layout>

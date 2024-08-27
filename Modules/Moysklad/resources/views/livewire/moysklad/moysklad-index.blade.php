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
</x-layouts.module-index-layout>

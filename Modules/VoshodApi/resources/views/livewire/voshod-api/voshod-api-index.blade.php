<x-layouts.module-index-layout :modules="$modules">
    <x-blocks.main-block>
        <flux:navbar>
            <flux:navbar.item :href="route('voshodapi.index', ['page' => 'main'])" :current="$page === 'main'">Основное</flux:navbar.item>
            <flux:navbar.item :href="route('voshodapi.index', ['page' => 'times'])" :current="$page === 'times'">Время выгрузки</flux:navbar.item>
            <flux:navbar.item :href="route('voshodapi.index', ['page' => 'warehouses'])" :current="$page === 'warehouses'">Склады</flux:navbar.item>
            <flux:navbar.item :href="route('voshodapi.index', ['page' => 'attributes'])" :current="$page === 'attributes'">Атрибуты</flux:navbar.item>
        </flux:navbar>
    </x-blocks.main-block>
    @if($page === 'main')
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:button wire:click="store">Сохранить</flux:button>
                <flux:separator />
                <flux:heading size="xl">Основные настройки</flux:heading>
                <flux:select variant="combobox" placeholder="Выберите поставщика..." label="Связать с поставщиком" wire:model="form.supplier_id">
                    @foreach(auth()->user()->suppliers as $supplier)
                        <flux:option :value="$supplier->getKey()">{{$supplier->name}}</flux:option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="form.api_key" label="Апи ключ"/>
                <flux:separator />
                <flux:heading size="xl">Настройка прокси</flux:heading>
                <flux:input wire:model="form.proxy_ip" label="IP"/>
                <flux:input wire:model="form.proxy_port" label="Port" type="number"/>
                <flux:input wire:model="form.proxy_login" label="Логин"/>
                <flux:input wire:model="form.proxy_password" label="Пароль"/>
            </flux:card>
        </x-blocks.main-block>
    @elseif($page === 'times')
        <livewire:voshodapi::voshod-api-time.voshod-api-time-index :voshodApi="$form->voshodApi"/>
    @elseif($page === 'warehouses')
        <livewire:voshodapi::voshod-api-warehouse.voshod-api-warehouse-index :voshodApi="$form->voshodApi"/>
    @elseif($page === 'attributes')
        <livewire:voshodapi::voshod-api-item-additional-attribute-link.voshod-api-item-additional-attribute-link-index :voshodApi="$form->voshodApi"/>
    @endif
    <div wire:loading wire:target="store">
        <x-loader/>
    </div>
</x-layouts.module-index-layout>

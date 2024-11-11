<x-layouts.module-index-layout :modules="$modules">
    <x-blocks.main-block>
        <flux:navbar>
            <flux:navbar.item :href="route('bergapi.index', ['page' => 'main'])" :current="$page === 'main'">Основное</flux:navbar.item>
            <flux:navbar.item :href="route('bergapi.index', ['page' => 'times'])" :current="$page === 'times'">Время выгрузки</flux:navbar.item>
            <flux:navbar.item :href="route('bergapi.index', ['page' => 'warehouses'])" :current="$page === 'warehouses'">Склады</flux:navbar.item>
            <flux:navbar.item :href="route('bergapi.index', ['page' => 'attributes'])" :current="$page === 'attributes'">Атрибуты</flux:navbar.item>
        </flux:navbar>
    </x-blocks.main-block>
    @if($page === 'main')
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:button wire:click="store">Сохранить</flux:button>
                <flux:select variant="combobox" placeholder="Выберите поставщика..." label="Связанный поставщик" wire:model="form.supplier_id">
                    @foreach(auth()->user()->suppliers as $supplier)
                        <flux:option :value="$supplier->getKey()">{{$supplier->name}}</flux:option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="form.api_key" label="Апи ключ"/>
            </flux:card>
        </x-blocks.main-block>
    @elseif($page === 'times')
        <livewire:bergapi::berg-api-time.berg-api-time-index :bergApi="$form->bergApi"/>
    @elseif($page === 'warehouses')
        <livewire:bergapi::berg-api-warehouse.berg-api-warehouse-index :bergApi="$form->bergApi"/>
    @elseif($page === 'attributes')
        <livewire:bergapi::berg-api-item-additional-attribute-link.berg-api-item-additional-attribute-link-index :bergApi="$form->bergApi"/>
    @endif
    <div wire:loading wire:target="store">
        <x-loader/>
    </div>
</x-layouts.module-index-layout>

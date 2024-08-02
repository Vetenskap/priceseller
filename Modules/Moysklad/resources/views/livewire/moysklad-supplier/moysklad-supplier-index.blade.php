<x-layouts.module-container>
    <x-blocks.main-block>
        <x-information>
            Вы можете привязать своих поставщиков с Моего Склада к своим существующим.
        </x-information>
    </x-blocks.main-block>
    <x-blocks.flex-block-end>
        <x-dropdown-select name="suppliers" field="supplierId" :options="auth()->user()->suppliers">Ваши поставщики</x-dropdown-select>
        <x-success-button wire:click="add">Добавить</x-success-button>
    </x-blocks.flex-block-end>
    @if($moysklad->suppliers->count())
        <x-blocks.main-block>
            <x-success-button wire:click="save">Сохранить</x-success-button>
        </x-blocks.main-block>
    @endif
    @foreach($moysklad->suppliers as $supplier)
        <livewire:moysklad::moysklad-supplier.moysklad-supplier-edit :supplier="$supplier" wire:key="{{$supplier->id}}" :moysklad-suppliers="$moyskladSuppliers"/>
    @endforeach
</x-layouts.module-container>

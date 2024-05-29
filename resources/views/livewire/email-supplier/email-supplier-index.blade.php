<div>
    <x-blocks.main-block>
        <x-layouts.title name="Поставщики"/>
    </x-blocks.main-block>
    <x-blocks.flex-block-end>
        <x-dropdown-select name="supplier"
                           field="selectedSupplier"
                           :options="auth()->user()->suppliers"

            >
            Выберите поставщика
        </x-dropdown-select>
        <x-success-button wire:click="store">Добавить</x-success-button>
    </x-blocks.flex-block-end>
    @foreach($email->suppliers as $supplier)
        <livewire:email-supplier.email-supplier-edit wire:key="{{$supplier->pivot->id}}" :email-supplier-id="$supplier->pivot->id"/>
    @endforeach
</div>

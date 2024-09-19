<div>
    <x-blocks.main-block>
        <x-layouts.title name="Склады"/>
        <x-information>Вы можете добавить склады поставщика</x-information>
    </x-blocks.main-block>
    <div x-data="{ open: false }">
        <x-blocks.main-block>
            <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
        </x-blocks.main-block>
        <div x-show="open">
            <x-blocks.flex-block>
                <x-inputs.input-with-label name="name"
                                           field="form.name">
                    Наименование
                </x-inputs.input-with-label>
                <div class="self-center">
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </div>
            </x-blocks.flex-block>
        </div>
    </div>
    <x-blocks.main-block>
        <x-layouts.title name="Все склады"/>
    </x-blocks.main-block>
    @if($supplier->warehouses->isNotEmpty())
        <x-blocks.main-block>
            <x-success-button wire:click="update">Сохранить</x-success-button>
        </x-blocks.main-block>
        @foreach($supplier->warehouses as $warehouse)
            <livewire:supplier-warehouse.supplier-warehouse-edit :supplier="$supplier" :warehouse="$warehouse" wire:key="{{$warehouse->getKey()}}"/>
        @endforeach
    @else
        <x-blocks.main-block>
            <x-information>Вы пока ещё не добавляли склады</x-information>
        </x-blocks.main-block>
    @endif
</div>

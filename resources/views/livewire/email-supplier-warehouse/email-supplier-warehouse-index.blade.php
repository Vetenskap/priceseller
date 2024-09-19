<div>
    <x-blocks.main-block>
        <x-layouts.title name="Склады"/>
        <x-titles.sub-title name="Привязка складов"/>
        <x-information>Вы можете привязать склады с прайса к складам поставщика</x-information>
    </x-blocks.main-block>
    <div x-data="{ open: false }">
        <x-blocks.main-block>
            <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
        </x-blocks.main-block>
        <div x-show="open">
            <x-blocks.flex-block>
                <x-inputs.input-with-label name="value"
                                           field="form.value"
                                           type="text"
                >Название в прайсе
                </x-inputs.input-with-label>
                <x-dropdowns.dropdown-select name="supplier_warehouse_id"
                                             :items="$emailSupplier->supplier->warehouses->all()"
                                             field="form.supplier_warehouse_id"
                                             :current-id="$form->supplier_warehouse_id"
                >Склад
                </x-dropdowns.dropdown-select>
                <div class="self-center">
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </div>
            </x-blocks.flex-block>
        </div>
    </div>
    <x-blocks.main-block>
        <x-layouts.title name="Все склады"/>
    </x-blocks.main-block>
    @if($emailSupplier->warehouses->isNotEmpty())
        <x-blocks.main-block>
            <x-success-button wire:click="update">Сохранить</x-success-button>
        </x-blocks.main-block>
        @foreach($emailSupplier->warehouses as $warehouse)
            <livewire:email-supplier-warehouse.email-supplier-warehouse-edit :email-supplier-warehouse="$warehouse" :email-supplier="$emailSupplier" wire:key="{{$warehouse->getKey()}}"/>
        @endforeach
    @else
        <x-blocks.main-block>
            <x-information>Вы пока ещё не добавляли склады</x-information>
        </x-blocks.main-block>
    @endif
</div>

<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Добавление нового склада"/>
        </x-blocks.main-block>
        <x-blocks.flex-block>
            <x-inputs.input-with-label name="name" field="name" type="text" required>Наименование</x-inputs.input-with-label>
            <x-inputs.input-with-label name="warehouse_id" field="warehouse_id" type="number" required>Идентификатор</x-inputs.input-with-label>
            <div class="self-center">
                <x-success-button wire:click="store">Добавить</x-success-button>
            </div>
        </x-blocks.flex-block>
    </x-layouts.main-container>
    @if($bergApi->warehouses->isNotEmpty())
        <x-layouts.main-container>
            <x-blocks.main-block>
                <x-layouts.title name="Список" />
            </x-blocks.main-block>
            @foreach($bergApi->warehouses as $warehouse)
                <livewire:bergapi::berg-api-warehouse.berg-api-warehouse-edit :warehouse="$warehouse"
                                                                                    wire:key="{{$warehouse->getKey()}}"/>
            @endforeach
        </x-layouts.main-container>
    @endif
</div>

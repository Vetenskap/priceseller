<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Добавление нового склада"/>
        </x-blocks.main-block>
        <x-blocks.flex-block>
            <x-dropdowns.dropdown-select name="warehouse"
                                         field="name"
                                         :current-id="$name"
                                         :items="$configWarehouses"
                                         option-value="name"
                                         option-name="label"
                                         :current-items="$voshodApi->warehouses"
                                         current-items-option-value="name"
            >Выберите склад</x-dropdowns.dropdown-select>
            <div class="self-center">
                <x-success-button wire:click="store">Добавить</x-success-button>
            </div>
        </x-blocks.flex-block>
    </x-layouts.main-container>
    @if($voshodApi->warehouses->isNotEmpty())
        <x-layouts.main-container>
            <x-blocks.main-block>
                <x-layouts.title name="Список" />
            </x-blocks.main-block>
            @foreach($voshodApi->warehouses as $warehouse)
                <livewire:voshodapi::voshod-api-warehouse.voshod-api-warehouse-edit :warehouse="$warehouse"
                                                                                    wire:key="{{$warehouse->getKey()}}"/>
            @endforeach
        </x-layouts.main-container>
    @endif
</div>

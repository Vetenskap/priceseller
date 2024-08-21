<div>
    <x-blocks.flex-block>
        <x-dropdowns.dropdown-select name="warehouse_id"
                                     :items="auth()->user()->warehouses"
                                     :current-id="$form->warehouse_id"
                                     field="form.warehouse_id">
            Ваши склады (priceseller)
        </x-dropdowns.dropdown-select>
        <x-dropdowns.dropdown-select name="moysklad_warehouse_id"
                                     :items="$moyskladWarehouses"
                                     :current-id="$form->moysklad_warehouse_uuid"
                                     field="form.moysklad_warehouse_uuid">
            Ваши склады (Мой склад)
        </x-dropdowns.dropdown-select>
        <div class="self-center">
            <x-danger-button wire:click="destroy">Удалить</x-danger-button>
        </div>
        <div class="self-center">
            <x-success-button wire:click="updateStocks">Обновить остатки</x-success-button>
        </div>
    </x-blocks.flex-block>
    <div wire:loading wire:target="updateStocks">
        <x-loader />
    </div>
</div>

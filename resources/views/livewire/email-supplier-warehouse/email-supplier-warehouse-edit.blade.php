<div>
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
            <x-danger-button wire:click="destroy">Удалить</x-danger-button>
        </div>
    </x-blocks.flex-block>
</div>

<div>
    <x-blocks.flex-block>
        <x-dropdowns.dropdown-select name="supplier_id"
                                     :items="auth()->user()->suppliers"
                                     field="form.supplier_id"
                                     :current-id="$form->supplier_id"
                                     :current-items="$moysklad->suppliers"
                                     current-items-option-value="supplier_id"
        >Ваш поставщик (priceseller)
        </x-dropdowns.dropdown-select>
        <x-dropdowns.dropdown-select name="moysklad_supplier_uuid"
                                     :items="$moyskladSuppliers"
                                     field="form.moysklad_supplier_uuid"
                                     :current-id="$form->moysklad_supplier_uuid"
                                     :current-items="$moysklad->suppliers"
                                     current-items-option-value="moysklad_supplier_uuid"
        >Ваш поставщик (Мой склад)
        </x-dropdowns.dropdown-select>
        <div class="self-center">
            <x-danger-button wire:click="destroy">Удалить</x-danger-button>
        </div>
    </x-blocks.flex-block>
</div>

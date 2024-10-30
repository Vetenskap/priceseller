<div>
    <x-blocks.flex-block>
        <x-dropdowns.dropdown-select name="organization_id"
                                     :items="auth()->user()->organizations"
                                     field="form.organization_id"
                                     :current-id="$form->organization_id"
                                     :current-items="$moysklad->organizations"
                                     current-items-option-value="organization_id"
        >Ваша организация (priceseller)
        </x-dropdowns.dropdown-select>
        <x-dropdowns.dropdown-select name="moysklad_organization_uuid"
                                     :items="$moyskladOrganizations"
                                     field="form.moysklad_organization_uuid"
                                     :current-id="$form->moysklad_organization_uuid"
                                     :current-items="$moysklad->organizations"
                                     current-items-option-value="moysklad_organization_uuid"
        >Ваша организация (Мой склад)
        </x-dropdowns.dropdown-select>
        <div class="self-center">
            <x-danger-button wire:click="destroy">Удалить</x-danger-button>
        </div>
    </x-blocks.flex-block>
</div>

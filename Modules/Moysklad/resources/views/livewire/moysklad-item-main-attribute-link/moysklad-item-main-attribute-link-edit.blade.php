<div>
    <x-blocks.flex-block>
        <x-dropdowns.dropdown-select name="attribute_name"
                                     :items="App\Models\Item::MAINATTRIBUTES"
                                     option-name="label"
                                     option-value="name"
                                     field="form.attribute_name"
                                     :current-id="$form->attribute_name"
                                     :current-items="$moysklad->itemMainAttributeLinks"
                                     current-items-option-value="attribute_name"
        >Атрибут priceseller</x-dropdowns.dropdown-select>
        <x-dropdowns.dropdown-select name="link"
                                     :items="$assortmentAttributes"
                                     option-name="label"
                                     option-value="name"
                                     field="form.link"
                                     :current-id="$form->link"
        >Атрибут Мой склад</x-dropdowns.dropdown-select>
        <x-dropdown-select name="user_type" field="form.user_type"
                           :options="config('app.attributes_types')" option-name="label" value="name">Тип
        </x-dropdown-select>
        <div class="self-center">
            <x-danger-button wire:click="destroy">Удалить</x-danger-button>
        </div>
    </x-blocks.flex-block>
</div>

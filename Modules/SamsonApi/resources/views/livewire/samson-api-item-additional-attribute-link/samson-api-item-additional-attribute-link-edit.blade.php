<x-blocks.flex-block>
    <x-dropdowns.dropdown-select name="item_attribute_id"
                                 :items="auth()->user()->itemAttributes"
                                 :current-id="$form->item_attribute_id"
                                 field="form.item_attribute_id"
    >Атрибут</x-dropdowns.dropdown-select>
    <x-dropdowns.dropdown-select name="link"
                                 :items="\Modules\SamsonApi\HttpClient\Resources\Sku::ATTRIBUTES"
                                 option-value="name"
                                 option-name="label"
                                 :current-id="$form->link"
                                 field="form.link"
    >Поле из API</x-dropdowns.dropdown-select>
    <div class="self-center">
        <x-success-button wire:click="store">Добавить</x-success-button>
    </div>
</x-blocks.flex-block>

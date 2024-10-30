<div>
    <x-blocks.flex-block>
        <x-dropdowns.dropdown-select name="attribute_name"
                                     :items="App\Models\Bundle::MAINATTRIBUTES"
                                     option-name="label"
                                     option-value="name"
                                     field="form.attribute_name"
                                     :current-id="$form->attribute_name"
                                     :current-items="$moysklad->bundleMainAttributeLinks"
                                     current-items-option-value="attribute_name"
        >Атрибут priceseller</x-dropdowns.dropdown-select>
        <x-dropdowns.dropdown-select name="link"
                                     :items="$bundleAttributes"
                                     option-name="label"
                                     option-value="name"
                                     field="form.link"
                                     :current-id="$form->link"
        >Атрибут Мой склад</x-dropdowns.dropdown-select>
        <x-dropdown-select name="user_type" field="form.user_type"
                           :options="config('app.attributes_types')" option-name="label" value="name">Тип
        </x-dropdown-select>
        @if($form->user_type === 'boolean')
            <x-blocks.flex-block>
                <x-inputs.switcher :checked="$form->invert" wire:model="form.invert"/>
                <x-layouts.simple-text name="Инвертировать" />
            </x-blocks.flex-block>
        @endif
        <div class="self-center">
            <x-danger-button wire:click="destroy">Удалить</x-danger-button>
        </div>
    </x-blocks.flex-block>
</div>

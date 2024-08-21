<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Добавление нового атрибута"/>
            <x-information>Вы можете связать свои атрибуты с полями получаемыми из API</x-information>
        </x-blocks.main-block>
        <x-blocks.flex-block>
            <x-dropdowns.dropdown-select name="item_attribute_id"
                                         :items="auth()->user()->itemAttributes"
                                         :current-id="$form->item_attribute_id"
                                         field="form.item_attribute_id"
            >Выберите атрибут</x-dropdowns.dropdown-select>
            <x-dropdowns.dropdown-select name="link"
                                         :items="\Modules\BergApi\HttpClient\Resources\Resource::ATTRIBUTES"
                                         option-value="name"
                                         option-name="label"
                                         :current-id="$form->link"
                                         field="form.link"
            >Выберите поле из API</x-dropdowns.dropdown-select>
            <div class="self-center">
                <x-success-button wire:click="store">Добавить</x-success-button>
            </div>
        </x-blocks.flex-block>
    </x-layouts.main-container>
    @if($bergApi->itemAdditionalAttributeLinks->isNotEmpty())
        <x-layouts.main-container>
            <x-blocks.main-block>
                <x-layouts.title name="Список" />
            </x-blocks.main-block>
            <x-blocks.main-block>
                <x-success-button wire:click="update">Сохранить</x-success-button>
            </x-blocks.main-block>
            @foreach($bergApi->itemAdditionalAttributeLinks as $link)
                <livewire:bergapi::berg-api-item-additional-attribute-link.berg-api-item-additional-attribute-link-edit :bergItemLink="$link"
            @endforeach
        </x-layouts.main-container>
    @endif
</div>

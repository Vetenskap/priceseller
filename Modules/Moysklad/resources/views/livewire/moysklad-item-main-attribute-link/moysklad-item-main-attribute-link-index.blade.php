<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Атрибуты"/>
            <x-titles.sub-title name="Привязка основных атрибутов"/>
            <x-information>Постарайтесь привязать все основные атрибуты priceseller, без них товары не будут
                создаваться/обновляться
            </x-information>
        </x-blocks.main-block>
        <div x-data="{ open: false }">
            <x-blocks.main-block>
                <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
            </x-blocks.main-block>
            <div x-show="open">
                <x-blocks.flex-block>
                    <x-dropdowns.dropdown-select name="attribute_name"
                                                 :items="App\Models\Item::MAINATTRIBUTES"
                                                 option-name="label"
                                                 option-value="name"
                                                 field="form.attribute_name"
                                                 :current-id="$form->attribute_name"
                                                 :current-items="$moysklad->itemMainAttributeLinks"
                                                 current-items-option-value="attribute_name"
                    >Атрибут priceseller
                    </x-dropdowns.dropdown-select>
                    <x-dropdowns.dropdown-select name="link"
                                                 :items="$assortmentAttributes"
                                                 option-name="label"
                                                 option-value="name"
                                                 field="form.link"
                                                 :current-id="$form->link"
                    >Атрибут Мой склад
                    </x-dropdowns.dropdown-select>
                    <x-dropdown-select name="user_type" field="form.user_type"
                                       :options="config('app.attributes_types')" option-name="label" value="name">Тип
                    </x-dropdown-select>
                </x-blocks.flex-block>
                <x-blocks.main-block>
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </x-blocks.main-block>
            </div>
        </div>
    </x-layouts.main-container>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Все атрибуты"/>
            <x-titles.sub-title name="Основные"/>
        </x-blocks.main-block>
        @if($moysklad->itemMainAttributeLinks->isNotEmpty())
            <x-blocks.main-block>
                <x-success-button wire:click="update">Сохранить</x-success-button>
            </x-blocks.main-block>
            @foreach($moysklad->itemMainAttributeLinks as $attribute)
                <livewire:moysklad::moysklad-item-main-attribute-link.moysklad-item-main-attribute-link-edit
                    :moysklad="$moysklad" wire:key="{{$attribute->id}}" :moysklad-item-link="$attribute"/>
            @endforeach
        @else
            <x-blocks.main-block>
                <x-information>Вы пока ещё не добавляли атрибуты</x-information>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>
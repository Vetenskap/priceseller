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
                                                 :items="App\Models\Bundle::MAINATTRIBUTES"
                                                 option-name="label"
                                                 option-value="name"
                                                 field="form.attribute_name"
                                                 :current-id="$form->attribute_name"
                                                 :current-items="$moysklad->bundleMainAttributeLinks"
                                                 current-items-option-value="attribute_name"
                    >Атрибут priceseller
                    </x-dropdowns.dropdown-select>
                    <x-dropdowns.dropdown-select name="link"
                                                 :items="$bundleAttributes"
                                                 option-name="label"
                                                 option-value="name"
                                                 field="form.link"
                                                 :current-id="$form->link"
                    >Атрибут Мой склад
                    </x-dropdowns.dropdown-select>
                    <x-dropdown-select name="user_type" field="form.user_type"
                                       :options="config('app.attributes_types')" option-name="label" value="name">Тип
                    </x-dropdown-select>
                    @if($form->user_type === 'boolean')
                        <x-blocks.flex-block>
                            <x-inputs.switcher :checked="$form->invert" wire:model="form.invert"/>
                            <x-layouts.simple-text name="Инвертировать" />
                        </x-blocks.flex-block>
                    @endif
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
        @if($moysklad->bundleMainAttributeLinks->isNotEmpty())
            <x-blocks.main-block>
                <x-success-button wire:click="update">Сохранить</x-success-button>
            </x-blocks.main-block>
            @foreach($moysklad->bundleMainAttributeLinks as $attribute)
                <livewire:moysklad::moysklad-bundle-main-attribute-link.moysklad-bundle-main-attribute-link-edit
                    :moysklad="$moysklad" wire:key="{{$attribute->id}}" :moysklad-bundle-link="$attribute"/>
            @endforeach
        @else
            <x-blocks.main-block>
                <x-information>Вы пока ещё не добавляли атрибуты</x-information>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>
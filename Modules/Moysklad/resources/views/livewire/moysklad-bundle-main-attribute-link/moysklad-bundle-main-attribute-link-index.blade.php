<div>
    <flux:modal name="create-moysklad-bundle-main-attribute-link" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Привязка основного атрибута</flux:heading>
        </div>

        <flux:select variant="combobox" placeholder="Выберите атрибут..." label="Атрибут priceseller" wire:model="form.attribute_name">

            @foreach(\App\Models\Bundle::MAINATTRIBUTES as $mainAttribute)
                <flux:option :value="$mainAttribute['name']">{{$mainAttribute['label']}}</flux:option>
            @endforeach
        </flux:select>

        <flux:select variant="combobox" placeholder="Выберите атрибут..." label="Атрибут мой склад" wire:model="form.link">

            @foreach($bundleAttributes as $bundleAttribute)
                <flux:option :value="$bundleAttribute['name']">{{$bundleAttribute['label']}}</flux:option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="form.user_type" placeholder="Выберите тип..." label="Тип">
            @foreach(config('app.attributes_types') as $type)
                <flux:option :value="$type['name']">{{$type['label']}}</flux:option>
            @endforeach
        </flux:select>

        @if($form->user_type === 'boolean')
            <flux:switch wire:model="form.invert" label="Инвертировать"/>
        @endif

        <div class="flex">
            <flux:spacer/>

            <flux:button variant="primary" wire:click="store">Создать</flux:button>
        </div>
    </flux:modal>

    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Основные атрибуты</flux:heading>
            <flux:subheading>Привязка основных атрибутов</flux:subheading>
            <div>
                <flux:modal.trigger name="create-moysklad-bundle-main-attribute-link">
                    <flux:button>Добавить</flux:button>
                </flux:modal.trigger>
            </div>
            <flux:card class="space-y-6">
                <flux:heading size="xl">Список</flux:heading>
                @if($moysklad->bundleMainAttributeLinks->isNotEmpty())
                    <flux:table>
                        <flux:columns>
                            <flux:column>Атрибут priceseller</flux:column>
                            <flux:column>Атрибут мой склад</flux:column>
                            <flux:column>Тип</flux:column>
                            <flux:column>Инвертировать</flux:column>
                        </flux:columns>
                        <flux:rows>
                            @foreach($moysklad->bundleMainAttributeLinks as $bundleMainAttributeLink)
                                <flux:row :key="$bundleMainAttributeLink->getKey()">
                                    <flux:cell>{{collect(\App\Models\Bundle::MAINATTRIBUTES)->firstWhere('name', $bundleMainAttributeLink->attribute_name)['label']}}</flux:cell>
                                    <flux:cell>{{collect($bundleAttributes)->firstWhere('name', $bundleMainAttributeLink->link)['label']}}</flux:cell>
                                    <flux:cell>{{collect(config('app.attributes_types'))->firstWhere('name', $bundleMainAttributeLink->user_type)['label']}}</flux:cell>
                                    <flux:cell>
                                        <flux:switch :checked="boolval($bundleMainAttributeLink->invert)" disabled/>
                                    </flux:cell>
                                    <flux:cell align="right">
                                        <flux:icon.trash wire:click="destroy({{ json_encode($bundleMainAttributeLink->getKey()) }})"
                                                         wire:loading.remove
                                                         wire:target="destroy({{ json_encode($bundleMainAttributeLink->getKey()) }})"
                                                         wire:confirm="Вы действительно хотите удалить этот атрибут?"
                                                         class="cursor-pointer hover:text-red-400"/>
                                        <flux:icon.loading wire:loading
                                                           wire:target="destroy({{ json_encode($bundleMainAttributeLink->getKey()) }})"/>
                                    </flux:cell>
                                </flux:row>
                            @endforeach
                        </flux:rows>
                    </flux:table>
                @endif
            </flux:card>
        </flux:card>
    </x-blocks.main-block>
</div>

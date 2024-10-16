<div>
    <flux:modal name="create-moysklad-item-additional-attribute-link" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Привязка дополнительного атрибута</flux:heading>
        </div>

        <flux:select variant="combobox" placeholder="Выберите атрибут..." label="Дополнительный атрибут" wire:model="form.item_attribute_id">

            @foreach(auth()->user()->itemAttributes as $attribute)
                <flux:option :value="$attribute->id">{{$attribute->name}}</flux:option>
            @endforeach
        </flux:select>

        <flux:select variant="combobox" placeholder="Выберите атрибут..." label="Атрибут мой склад" wire:model="form.link">

            @foreach($assortmentAttributes as $assortmentAttribute)
                <flux:option :value="$assortmentAttribute['name']">{{$assortmentAttribute['label']}}</flux:option>
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
            <flux:heading size="xl">Дополнительные атрибуты</flux:heading>
            <flux:subheading>Привязка дополнительных атрибутов</flux:subheading>
            <div>
                <flux:modal.trigger name="create-moysklad-item-additional-attribute-link">
                    <flux:button>Добавить</flux:button>
                </flux:modal.trigger>
            </div>
            <flux:card class="space-y-6">
                <flux:heading size="xl">Список</flux:heading>
                @if($moysklad->itemAdditionalAttributeLinks->isNotEmpty())
                    <flux:table>
                        <flux:columns>
                            <flux:column>Дополнительный атрибут</flux:column>
                            <flux:column>Атрибут мой склад</flux:column>
                            <flux:column>Тип</flux:column>
                            <flux:column>Инвертировать</flux:column>
                        </flux:columns>
                        <flux:rows>
                            @foreach($moysklad->itemAdditionalAttributeLinks as $itemAdditionalAttributeLink)
                                <flux:row :key="$itemAdditionalAttributeLink->getKey()">
                                    <flux:cell>{{$itemAdditionalAttributeLink->itemAttribute->name}}</flux:cell>
                                    <flux:cell>{{collect($assortmentAttributes)->firstWhere('name', $itemAdditionalAttributeLink->link)['label']}}</flux:cell>
                                    <flux:cell>{{collect(config('app.attributes_types'))->firstWhere('name', $itemAdditionalAttributeLink->user_type)['label']}}</flux:cell>
                                    <flux:cell>
                                        <flux:switch :checked="boolval($itemAdditionalAttributeLink->invert)" disabled/>
                                    </flux:cell>
                                    <flux:cell align="right">
                                        <flux:icon.trash wire:click="destroy({{ json_encode($itemAdditionalAttributeLink->getKey()) }})"
                                                         wire:loading.remove
                                                         wire:target="destroy({{ json_encode($itemAdditionalAttributeLink->getKey()) }})"
                                                         wire:confirm="Вы действительно хотите удалить этот атрибут?"
                                                         class="cursor-pointer hover:text-red-400"/>
                                        <flux:icon.loading wire:loading
                                                           wire:target="destroy({{ json_encode($itemAdditionalAttributeLink->getKey()) }})"/>
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

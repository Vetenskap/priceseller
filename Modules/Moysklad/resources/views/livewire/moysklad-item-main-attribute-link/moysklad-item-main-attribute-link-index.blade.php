<div>
    <flux:modal name="create-moysklad-item-main-attribute-link" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Привязка основного атрибута</flux:heading>
        </div>

        <flux:select variant="combobox" placeholder="Выберите атрибут..." label="Атрибут priceseller" wire:model="form.attribute_name">

            @foreach(\App\Models\Item::MAINATTRIBUTES as $mainAttribute)
                <flux:option :value="$mainAttribute['name']">{{$mainAttribute['label']}}</flux:option>
            @endforeach
        </flux:select>

        <flux:select variant="combobox" placeholder="Выберите атрибут..." label="Атрибут мой склад" wire:model="form.link">

            @foreach($assortmentAttributes as $assortmentAttribute)
                <flux:option :value="$assortmentAttribute['name']">{{$assortmentAttribute['label']}}</flux:option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="form.user_type" placeholder="Выберите тип..." label="Тип">
            <flux:option value="''">Нет</flux:option>
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
                <flux:modal.trigger name="create-moysklad-item-main-attribute-link">
                    <flux:button>Добавить</flux:button>
                </flux:modal.trigger>
            </div>
            <flux:card class="space-y-6">
                <flux:heading size="xl">Список</flux:heading>
                @if($moysklad->itemMainAttributeLinks->isNotEmpty())
                    <flux:table>
                        <flux:columns>
                            <flux:column>Атрибут priceseller</flux:column>
                            <flux:column>Атрибут мой склад</flux:column>
                            <flux:column>Тип</flux:column>
                            <flux:column>Инвертировать</flux:column>
                        </flux:columns>
                        <flux:rows>
                            @foreach($moysklad->itemMainAttributeLinks as $itemMainAttributeLink)
                                <flux:row :key="$itemMainAttributeLink->getKey()">
                                    <flux:cell>{{collect(\App\Models\Item::MAINATTRIBUTES)->firstWhere('name', $itemMainAttributeLink->attribute_name)['label']}}</flux:cell>
                                    <flux:cell>{{collect($assortmentAttributes)->firstWhere('name', $itemMainAttributeLink->link)['label']}}</flux:cell>
                                    <flux:cell>{{collect(config('app.attributes_types'))->firstWhere('name', $itemMainAttributeLink->user_type)['label']}}</flux:cell>
                                    <flux:cell>
                                        <flux:switch :checked="boolval($itemMainAttributeLink->invert)" disabled/>
                                    </flux:cell>
                                    <flux:cell align="right">
                                        <flux:button size="sm" variant="danger" icon="trash"
                                                     wire:click="destroy({{ json_encode($itemMainAttributeLink->getKey()) }})"
                                                     wire:target="destroy({{ json_encode($itemMainAttributeLink->getKey()) }})"
                                                     wire:confirm="Вы действительно хотите удалить этот атрибут?"
                                        />
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

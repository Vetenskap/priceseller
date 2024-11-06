<div>
    <flux:modal name="create" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Добавить перерасчёт цен</flux:heading>
        </div>

        <flux:switch wire:model="form.enabled" label="Включить"/>
        <flux:select variant="combobox" wire:model="form.link" placeholder="Выберите атрибут МС с процентом..." label="Атрибут">
            @foreach($assortmentAttributes as $attribute)
                <flux:option :value="$attribute['name']">{{$attribute['label']}}</flux:option>
            @endforeach
        </flux:select>
        <flux:select variant="combobox" wire:model="form.price_type_uuid" placeholder="Выберите тип цены..." label="Тип цены">
            @foreach($priceTypes as $priceType)
                <flux:option :value="$priceType['name']">{{$priceType['label']}}</flux:option>
            @endforeach
        </flux:select>

        <div class="flex">
            <flux:spacer/>

            <flux:button variant="primary" wire:click="store">Создать</flux:button>
        </div>
    </flux:modal>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Перерасчёт цен</flux:heading>
            <div>
                <flux:modal.trigger name="create">
                    <flux:button>Добавить</flux:button>
                </flux:modal.trigger>
            </div>
            <flux:table>
                <flux:columns>
                    <flux:column>Атрибут</flux:column>
                    <flux:column>Тип</flux:column>
                </flux:columns>
                <flux:rows>
                    @foreach($moysklad->recountRetailMarkups as $recountRetailMarkup)
                        <flux:row :key="$recountRetailMarkup->getKey()">
                            <flux:cell>{{$recountRetailMarkup->link_label}}</flux:cell>
                            <flux:cell>{{$priceTypes->firstWhere('name', $recountRetailMarkup->price_type_uuid)['label']}}</flux:cell>
                            <flux:cell>
                                <flux:switch wire:model.live="dirtyRecountRetailMarkup.{{ $recountRetailMarkup->id }}.enabled" />
                            </flux:cell>
                            <flux:cell align="right">
                                <flux:button icon="trash" variant="danger" size="sm"
                                             wire:click="destroy({{ json_encode($recountRetailMarkup->getKey()) }})"
                                             wire:target="destroy({{ json_encode($recountRetailMarkup->getKey()) }})"
                                             wire:confirm="Вы действительно хотите удалить? Это действие нельзя будет отменить."/>
                            </flux:cell>
                        </flux:row>
                    @endforeach
                </flux:rows>
            </flux:table>
        </flux:card>
    </x-blocks.main-block>
</div>

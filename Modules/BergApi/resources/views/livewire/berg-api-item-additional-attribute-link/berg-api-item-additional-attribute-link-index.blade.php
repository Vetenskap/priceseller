<div>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Связь нового атрибута</flux:heading>
            <flux:subheading>Вы можете связать свои атрибуты с полями получаемыми из API</flux:subheading>
            <flux:select variant="combobox" placeholder="Выберите атрибут..." label="Дополнительный атрибут" wire:model="form.item_attribute_id">
                @foreach(auth()->user()->itemAttributes as $attribute)
                    <flux:option :value="$attribute->getKey()">{{$attribute->name}}</flux:option>
                @endforeach
            </flux:select>
            <flux:select variant="combobox" placeholder="Выберите поле из API" label="Поле из API" wire:model="form.link">
                @foreach(\Modules\BergApi\HttpClient\Resources\Resource::ATTRIBUTES as $attribute)
                    <flux:option :value="$attribute['name']">{{$attribute['label']}}</flux:option>
                @endforeach
            </flux:select>
            <flux:button wire:click="store">Добавить</flux:button>
        </flux:card>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <flux:card>
            <flux:table>
                <flux:columns>
                    <flux:column>Дополнительный атрибут</flux:column>
                    <flux:column>Поле из API</flux:column>
                </flux:columns>
                <flux:rows>
                    @foreach($bergApi->itemAdditionalAttributeLinks as $link)
                        <flux:row :key="$link->getKey()">
                            <flux:cell>{{$link->itemAttribute->name}}</flux:cell>
                            <flux:cell>{{collect(\Modules\BergApi\HttpClient\Resources\Resource::ATTRIBUTES)->firstWhere('name', $link->link)['label']}}</flux:cell>
                            <flux:cell align="right">
                                <flux:button size="sm" icon="trash" variant="danger" wire:click="destroy({{$link->getKey()}})" wire:target="destroy({{$link->getKey()}})" />
                            </flux:cell>
                        </flux:row>
                    @endforeach
                </flux:rows>
            </flux:table>
        </flux:card>
    </x-blocks.main-block>
</div>

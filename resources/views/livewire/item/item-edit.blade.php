<div>
    <x-layouts.header :name="$item->name . ' (' . $item->code . ')'"/>
    <x-layouts.actions>
        {{--        <a href="{{url()->previous()}}" wire:navigate.hover>--}}
        <x-primary-button wire:click="redirectBack">Закрыть</x-primary-button>
        {{--        </a>--}}
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Основная информация"/>
        </x-blocks.main-block>
        <x-blocks.flex-block>
            <x-inputs.input-with-label name="name"
                                       type="text"
                                       field="form.name"
            >Наименование
            </x-inputs.input-with-label>
            <x-inputs.input-with-label name="code"
                                       type="text"
                                       field="form.code"
            >Код
            </x-inputs.input-with-label>
            @if(auth()->user()->isMsSub())
                <x-inputs.input-with-label name="ms_uuid"
                                           type="text"
                                           field="form.ms_uuid"
                >МС UUID
                </x-inputs.input-with-label>
            @endif
            <x-inputs.input-with-label name="article"
                                       type="text"
                                       field="form.article"
            >Артикул поставщик
            </x-inputs.input-with-label>
            <x-inputs.input-with-label name="brand"
                                       type="text"
                                       field="form.brand"
            >Бренд поставщик
            </x-inputs.input-with-label>
            <x-inputs.input-with-label name="multiplicity"
                                       type="number"
                                       field="form.multiplicity"
            >Кратность отгрузки
            </x-inputs.input-with-label>
            <x-dropdown-select name="supplier"
                               field="form.supplier_id"
                               :options="auth()->user()->suppliers">
                Поставщики
            </x-dropdown-select>
        </x-blocks.flex-block>
        <x-blocks.flex-block>
            <x-inputs.switcher :checked="$form->unload_wb" wire:model="form.unload_wb"/>
            <x-layouts.simple-text name="Выгружать на ВБ"/>
        </x-blocks.flex-block>
        <x-blocks.flex-block>
            <x-inputs.switcher :checked="$form->unload_ozon" wire:model="form.unload_ozon"/>
            <x-layouts.simple-text name="Выгружать на ОЗОН"/>
        </x-blocks.flex-block>
        <x-blocks.main-block>
            <x-layouts.title name="Дополнительные поля"/>
        </x-blocks.main-block>

        @foreach(auth()->user()->itemAttributes()->whereNotIn('id', $item->attributesValues->pluck('item_attribute_id'))->get() as $mainAttribute)
            @switch($mainAttribute->type)
                @case('boolean')
                    <div class="flex items-center p-0 gap-2">
                        <x-blocks.flex-block>
                            <x-inputs.switcher :checked="false"
                                               wire:model="form.attributes.{{$mainAttribute->id}}"/>
                            <x-layouts.simple-text :name="$mainAttribute->name"/>
                        </x-blocks.flex-block>
                        <div class="cursor-pointer" wire:click="deleteAttribute({{$mainAttribute}})">&#10006;</div>
                    </div>
                    @break
                @case('textarea')
                    <div class="flex items-center p-0 gap-1">
                        <x-blocks.flex-block>
                            <x-textarea name="{{$mainAttribute->name}}" wire:model="form.attributes.{{$mainAttribute->id}}"/>
                            <div class="cursor-pointer" wire:click="deleteAttribute({{$mainAttribute}})">&#10006;</div>
                        </x-blocks.flex-block>
                    </div>
                    @break
                @default
                    <x-blocks.flex-block class="p-0 gap-1">
                        <x-inputs.input-with-label name="{{$mainAttribute->name}}"
                                                   type="{{$mainAttribute->type}}"
                                                   field="form.attributes.{{$mainAttribute->id}}"
                                                   wire:key="{{$mainAttribute->id}}"
                        >{{$mainAttribute->name}}
                        </x-inputs.input-with-label>
                        <div class="cursor-pointer" wire:click="deleteAttribute({{$mainAttribute}})">&#10006;</div>
                    </x-blocks.flex-block>
                    @break
            @endswitch
        @endforeach
        @foreach($item->attributesValues as $attribute)
            @switch($attribute->attribute->type)
                @case('boolean')
                    <x-blocks.flex-block class="p-0 gap-2">
                        <x-blocks.flex-block class="p-0">
                            <x-inputs.switcher :checked="$attribute->value"
                                               wire:model="form.attributes.{{$attribute->item_attribute_id}}"/>
                            <x-layouts.simple-text :name="$attribute->attribute->name"/>
                        </x-blocks.flex-block>
                        <div class="cursor-pointer" wire:click="deleteAttribute({{$attribute->attribute}})">
                            &#10006;
                        </div>
                    </x-blocks.flex-block>
                    @break
                @case('textarea')
                    <div class="flex items-center p-0 gap-1">
                        <x-blocks.flex-block>
                            <x-textarea name="{{$attribute->attribute->name}}" wire:model="form.attributes.{{$attribute->item_attribute_id}}"/>
                            <div class="cursor-pointer" wire:click="deleteAttribute({{$attribute->attribute}})">&#10006;</div>
                        </x-blocks.flex-block>
                    </div>
                    @break
                @default
                    <x-blocks.flex-block-end class="p-0 gap-1">
                        <x-inputs.input-with-label name="{{$attribute->attribute->name}}"
                                                   type="{{$attribute->attribute->type}}"
                                                   field="form.attributes.{{$attribute->item_attribute_id}}"
                                                   wire:key="{{$attribute->id}}"
                        >{{$attribute->attribute->name}}
                        </x-inputs.input-with-label>
                        <div class="cursor-pointer" wire:click="deleteAttribute({{$attribute->attribute}})">
                            &#10006;
                        </div>
                    </x-blocks.flex-block-end>
                    @break
            @endswitch
        @endforeach
        <livewire:item.item-attribute-dialog-form></livewire:item.item-attribute-dialog-form>
        <x-blocks.main-block>
            <x-layouts.title name="Прочая информация"/>
        </x-blocks.main-block>
        <x-blocks.flex-block>
            <x-layouts.simple-text :name="'Цена: ' . $item->price"/>
            <x-layouts.simple-text :name="'Остаток: ' . $item->count"/>
            <x-layouts.simple-text :name="'Был обновлен: ' . ($item->updated ? 'Да' : 'Нет')"/>
        </x-blocks.flex-block>
        <x-blocks.main-block>
            <x-layouts.title name="Из прайса"/>
        </x-blocks.main-block>
        @if($item->fromPrice)
            <x-blocks.flex-block>
                <x-layouts.simple-text :name="'Статус: ' . $item->fromPrice->message"/>
                <x-layouts.simple-text :name="'Артикул: ' . $item->fromPrice->article"/>
                <x-layouts.simple-text :name="'Бренд: ' . $item->fromPrice->brand"/>
                <x-layouts.simple-text :name="'Цена: ' . $item->fromPrice->price"/>
                <x-layouts.simple-text :name="'Остаток: ' . $item->fromPrice->stock"/>
            </x-blocks.flex-block>
        @endif
    </x-layouts.main-container>
    <div wire:loading wire:target="deleteAttribute">
        <x-loader/>
    </div>
</div>

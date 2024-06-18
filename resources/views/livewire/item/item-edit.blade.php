<div>
    <x-layouts.header :name="$item->name . ' (' . $item->code . ')'"/>
    <x-layouts.actions>
        <a href="{{route('items')}}" wire:navigate.hover>
            <x-primary-button>Назад</x-primary-button>
        </a>
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
</div>

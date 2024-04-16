<div>
    <x-layouts.header :name="$item->code"/>
    <x-layouts.actions>
        <a href="{{route('items')}}" wire:navigate.hover>
            <x-primary-button>Назад</x-primary-button>
        </a>
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-layouts.title name="Основная информация"/>
        <x-blocks.flex-block>
            <x-inputs.input-with-label name="code"
                                       type="text"
                                       field="form.code"
            >Код</x-inputs.input-with-label>
            @if(auth()->user()->is_ms_sub())
                <x-inputs.input-with-label name="ms_uuid"
                                           type="text"
                                           field="form.ms_uuid"
                >МС UUID</x-inputs.input-with-label>
            @endif
            <x-inputs.input-with-label name="article_supplier"
                                       type="text"
                                       field="form.article_supplier"
            >Артикул поставщик</x-inputs.input-with-label>
            <x-inputs.input-with-label name="brand"
                                       type="text"
                                       field="form.brand"
            >Бренд поставщик</x-inputs.input-with-label>
            <x-inputs.input-with-label name="article_manufactor"
                                       type="text"
                                       field="form.article_manufactor"
            >Артикул производитель</x-inputs.input-with-label>
        </x-blocks.flex-block>
        <x-blocks.flex-block>
            <x-inputs.input-with-label name="article_manufactor_brand"
                                       type="text"
                                       field="form.article_manufactor_brand"
            >Бренд производитель</x-inputs.input-with-label>
            <x-inputs.input-with-label name="multiplicity"
                                       type="number"
                                       field="form.multiplicity"
            >Кратность отгрузки</x-inputs.input-with-label>
        </x-blocks.flex-block>
    </x-layouts.main-container>
</div>

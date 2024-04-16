<div>
    <x-layouts.header :name="$supplier->name"/>
    <x-layouts.actions>
        <a href="{{route('suppliers')}}" wire:navigate.hover>
            <x-primary-button>Назад</x-primary-button>
        </a>
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-blocks.flex-block-end>
            <x-inputs.switcher :checked="$supplier->open" wire:model="form.open"/>
            <x-inputs.input-with-label name="name"
                                       type="text"
                                       field="form.name"
            >Наименование
            </x-inputs.input-with-label>
            @if(auth()->user()->is_ms_sub())
                <x-inputs.input-with-label name="ms_uuid"
                                           type="text"
                                           field="form.ms_uuid"
                >МС UUID
                </x-inputs.input-with-label>
            @endif
        </x-blocks.flex-block-end>
        <x-blocks.flex-block>
            <x-inputs.switcher :checked="$supplier->use_brand" wire:model="form.use_brand"/>
            <x-layouts.simple-text name="Использовать бренд"/>
        </x-blocks.flex-block>
    </x-layouts.main-container>
</div>

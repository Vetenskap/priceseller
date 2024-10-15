<div>
    <x-layouts.header :name="$form->name"/>
    <x-layouts.actions>
        <x-success-button wire:click="update">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-blocks.flex-block-end>
            <x-inputs.input-with-label name="name"
                                       type="text"
                                       field="form.name"
            >Наименование
            </x-inputs.input-with-label>
        </x-blocks.flex-block-end>
    </x-layouts.main-container>
</div>

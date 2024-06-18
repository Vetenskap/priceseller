<div>
    <x-blocks.flex-block-end>
        <x-inputs.switcher :checked="$open" />
        <x-inputs.input-with-label name="name"
                                   field="name"
        >
            Наименование
        </x-inputs.input-with-label>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-blocks.flex-block-end>
</div>

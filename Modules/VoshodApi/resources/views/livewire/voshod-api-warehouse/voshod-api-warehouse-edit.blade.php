<div>
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="label"
                                   type="text"
                                   field="label"
                                   disabled
        >Наименование склада</x-inputs.input-with-label>
        <div class="self-center">
            <x-danger-button wire:click="destroy">Удалить</x-danger-button>
        </div>
    </x-blocks.flex-block>
</div>

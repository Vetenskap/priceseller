<div>
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="name"
                                   field="form.name"
                                   type="text"
        >Значение в прайсе
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="value"
                                   field="form.value"
                                   type="number"
        >Какой остаток ставить
        </x-inputs.input-with-label>
        <div class="self-center">
            <x-danger-button wire:click="destroy">Удалить</x-danger-button>
        </div>
    </x-blocks.flex-block>
</div>

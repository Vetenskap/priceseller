<div>
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="name" field="name" type="text" disabled>Наименование</x-inputs.input-with-label>
        <x-inputs.input-with-label name="warehouse_id" field="warehouse_id" type="number" disabled>Идентификатор</x-inputs.input-with-label>
        <div class="self-center">
            <x-danger-button wire:click="destroy">Удалить</x-danger-button>
        </div>
    </x-blocks.flex-block>
</div>

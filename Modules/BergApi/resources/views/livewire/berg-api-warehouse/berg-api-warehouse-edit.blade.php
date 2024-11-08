<div>
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="name" field="name" type="text" disabled>Наименование</x-inputs.input-with-label>
        <x-inputs.input-with-label name="warehouse_name" field="warehouse_name" disabled>Наименование склада</x-inputs.input-with-label>
        <div class="self-center">
            <x-danger-button wire:click="destroy">Удалить</x-danger-button>
        </div>
    </x-blocks.flex-block>
</div>

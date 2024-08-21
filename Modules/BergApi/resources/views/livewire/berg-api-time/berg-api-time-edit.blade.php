<div>
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="time" type="time" field="time" disabled>Время</x-inputs.input-with-label>
        <div class="self-center">
            <x-danger-button wire:click="destroy">Удалить</x-danger-button>
        </div>
    </x-blocks.flex-block>
</div>

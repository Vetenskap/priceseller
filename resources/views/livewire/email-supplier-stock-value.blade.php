<x-blocks.flex-block-end>
    <x-inputs.input-with-label name="name"
                               type="text"
                               field="form.name"
    >Из
    </x-inputs.input-with-label>
    <x-inputs.input-with-label name="value"
                               type="text"
                               field="form.value"
    >В
    </x-inputs.input-with-label>
    <x-success-button wire:click="save">Сохранить</x-success-button>
    <x-danger-button wire:click="$parent.deleteEmailSupplierStockValue({{$stockValue}})">Удалить</x-danger-button>
</x-blocks.flex-block-end>

<x-blocks.flex-block-end>
    <x-inputs.input-with-label name="name" field="" value="{{$supplier->supplier->name}}" disabled></x-inputs.input-with-label>
    <x-dropdown-select name="supplierId" field="moyskladSupplierId" :options="$moyskladSuppliers"></x-dropdown-select>
</x-blocks.flex-block-end>

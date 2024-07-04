@props(['market' => null, 'apiWarehouses' => null])

<div>
    <x-blocks.main-block>
        <x-layouts.title name="Остатки"/>
    </x-blocks.main-block>
    @if(!$market?->warehouses()->count())
        <x-blocks.center-block class="w-full bg-yellow-200 p-6 dark:text-yellow-400">
            <x-layouts.simple-text class="dark:text-gray-900"
                                   name="Ни один склад не добавлен. Остатки не будут выгружаться"/>
        </x-blocks.center-block>
    @endif
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="max_count"
                                   type="number"
                                   field="form.max_count"
        >Максимальный остаток
        </x-inputs.input-with-label>
    </x-blocks.flex-block>
    <x-blocks.main-block>
        <x-layouts.simple-text name="Ставить остаток 1 если"/>
    </x-blocks.main-block>
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="min"
                                   type="number"
                                   field="form.min"
        >Остаток от
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="max"
                                   type="number"
                                   field="form.max"
        >Остаток до
        </x-inputs.input-with-label>
    </x-blocks.flex-block>
</div>

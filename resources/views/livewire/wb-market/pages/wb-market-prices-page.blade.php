<x-layouts.wb-market-edit-layout :form="$form" :market="$market" :page="$page">
    <x-blocks.main-block>
        <x-layouts.title name="Цены"/>
    </x-blocks.main-block>
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="coefficient"
                                   type="number"
                                   field="form.coefficient"
        >Коэффициент
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="basic_logistics"
                                   type="number"
                                   field="form.basic_logistics"
        >Базовая цена логистики
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="price_one_liter"
                                   type="number"
                                   field="form.price_one_liter"
        >Цена за литр
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="volume"
                                   type="number"
                                   field="form.volume"
        >Объем (л)
        </x-inputs.input-with-label>
    </x-blocks.flex-block>
</x-layouts.wb-market-edit-layout>

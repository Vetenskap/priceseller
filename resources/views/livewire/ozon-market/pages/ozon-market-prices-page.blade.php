<x-layouts.ozon-market-edit-layout :form="$form" :market="$market" :page="$page">
    <x-blocks.flex-block>
        <x-inputs.switcher :checked="$form->seller_price" wire:model="form.seller_price"/>
        <x-layouts.simple-text name="Учитывать цену конкурента"/>
    </x-blocks.flex-block>
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="min_price_percent"
                                   type="number"
                                   field="form.min_price_percent"
                                   tooltip="Итоговая минимальная цена умноженная на этот коэффициент"
        >Процент увел. мин. цены
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="max_price_percent"
                                   type="number"
                                   field="form.max_price_percent"
                                   tooltip="Минимальная цена * %"
        >Цена до скидки, %
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="seller_price_percent"
                                   type="number"
                                   field="form.seller_price_percent"
                                   tooltip="Минимальная цена * %"
        >Цена продажи, %
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="acquiring"
                                   type="number"
                                   field="form.acquiring"
        >Эквайринг
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="last_mile"
                                   type="number"
                                   field="form.last_mile"
                                   tooltip="Считается 5,5 % от цены на сайте"
        >Последняя миля
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="max_mile"
                                   type="number"
                                   field="form.max_mile"
                                   tooltip="Берет комиссию мили не выше этой"
        >Максимальная миля
        </x-inputs.input-with-label>
    </x-blocks.flex-block>
</x-layouts.ozon-market-edit-layout>

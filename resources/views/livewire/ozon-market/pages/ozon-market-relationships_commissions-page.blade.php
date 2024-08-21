<x-layouts.ozon-market-edit-layout :form="$form" :market="$market" :page="$page">
    <x-marketPages.relationships-commissions :market="$market" :items="$items"
                                             :status-filters="$statusFilters" :file="$file">
        <x-inputs.input-with-label name="min_price_percent"
                                   type="number"
                                   field="min_price_percent"
                                   tooltip="Какой процент добавить к цене закупки для получения чистой прибыли"
        >Минимальная цена, %
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="min_price"
                                   type="number"
                                   field="min_price"
                                   tooltip="Ниже этой цены в маркет не выгрузится"
        >Минимальная цена продажи
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="shipping_processing"
                                   type="number"
                                   field="shipping_processing"
        >Обработка отправления
        </x-inputs.input-with-label>
    </x-marketPages.relationships-commissions>
</x-layouts.ozon-market-edit-layout>

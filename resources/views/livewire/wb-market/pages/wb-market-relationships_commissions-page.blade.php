<x-layouts.wb-market-edit-layout :form="$form" :market="$market" :page="$page">
    <x-marketPages.relationships-commissions :market="$market" :items="$items"
                                             :status-filters="$statusFilters" :file="$file">
        <x-inputs.input-with-label name="sales_percent"
                                   type="number"
                                   field="sales_percent"
        >Комиссия
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="min_price"
                                   type="number"
                                   field="min_price"
        >Минимальная цена продажи
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="retail_markup_percent"
                                   type="number"
                                   field="retail_markup_percent"
        >Наценка
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="package"
                                   type="number"
                                   field="package"
        >Упаковка
        </x-inputs.input-with-label>
    </x-marketPages.relationships-commissions>
</x-layouts.wb-market-edit-layout>

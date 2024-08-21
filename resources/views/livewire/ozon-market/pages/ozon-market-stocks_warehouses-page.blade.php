<x-layouts.ozon-market-edit-layout :form="$form" :market="$market" :page="$page">
    <x-marketPages.stocks-warehouses :market="$market" :api-warehouses="$apiWarehouses"/>
    <livewire:ozon-warehouse.ozon-warehouse-index :market="$market" :api-warehouses="$apiWarehouses"/>
</x-layouts.ozon-market-edit-layout>

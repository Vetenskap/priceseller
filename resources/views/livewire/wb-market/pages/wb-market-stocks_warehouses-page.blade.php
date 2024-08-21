<x-layouts.wb-market-edit-layout :form="$form" :market="$market" :page="$page">
    <x-marketPages.stocks-warehouses :market="$market" :api-warehouses="$apiWarehouses"/>
    <livewire:wb-warehouse.wb-warehouse-index :market="$market" :api-warehouses="$apiWarehouses"/>
</x-layouts.wb-market-edit-layout>

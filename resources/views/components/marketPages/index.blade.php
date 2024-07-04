@props(['route', 'market', 'page'])

<x-navigate-pages>
    <x-links.tab-link :href="route($route, ['market' => $market, 'page' => 'main'])" name="Основное" :active="$page === 'main'" wire:navigate.hover/>
    <x-links.tab-link :href="route($route, ['market' => $market, 'page' => 'prices'])" name="Цены" :active="$page === 'prices'" wire:navigate.hover/>
    <x-links.tab-link :href="route($route, ['market' => $market, 'page' => 'stocks_warehouses'])" name="Остатки и склады" :active="$page === 'stocks_warehouses'" wire:navigate.hover/>
    <x-links.tab-link :href="route($route, ['market' => $market, 'page' => 'relationships_commissions'])" name="Связи и комиссии" :active="$page === 'relationships_commissions'" wire:navigate.hover/>
    <x-links.tab-link :href="route($route, ['market' => $market, 'page' => 'export'])" name="Экспорт" :active="$page === 'export'" wire:navigate.hover/>
    <x-links.tab-link :href="route($route, ['market' => $market, 'page' => 'actions'])" name="Действия" :active="$page === 'actions'" wire:navigate.hover/>
</x-navigate-pages>


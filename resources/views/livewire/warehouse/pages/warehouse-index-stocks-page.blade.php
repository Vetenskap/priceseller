<div>
    <x-layouts.header name="Склады"/>
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link :href="route('warehouses.index', ['page' => 'list'])" name="Список"
                              :active="$page === 'list'" wire:navigate.hover/>
            <x-links.tab-link :href="route('warehouses.index', ['page' => 'stocks'])" name="Управление остатками"
                              :active="$page === 'stocks'" wire:navigate.hover/>
        </x-navigate-pages>
        <x-blocks.center-block>
            <x-layouts.title name="Экспорт"/>
        </x-blocks.center-block>
        <x-blocks.center-block>
            <x-secondary-button wire:click="export">Экспортировать</x-secondary-button>
        </x-blocks.center-block>
        <x-blocks.main-block>
            <livewire:warehouses-items-export.warehouses-items-export-index :model="$this->currentUser()"/>
        </x-blocks.main-block>
    </x-layouts.main-container>
    <x-layouts.main-container>
        <x-blocks.center-block>
            <x-layouts.title name="Загрузить новые остатки"/>
        </x-blocks.center-block>
        <x-blocks.flex-block class="justify-center">
            <x-success-button wire:click="downloadTemplate">Скачать шаблон</x-success-button>
        </x-blocks.flex-block>
        <x-file-block action="import" />
        <livewire:warehouses-items-import.warehouses-items-import-index :model="$this->currentUser()"/>
    </x-layouts.main-container>
    <div wire:loading wire:target="export, import">
        <x-loader/>
    </div>
</div>

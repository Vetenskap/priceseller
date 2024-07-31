@props(['market' => null])

<div>
    <x-blocks.main-block>
        <x-layouts.title name="Экспорт"/>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <x-secondary-button wire:click="export">Экспортировать</x-secondary-button>
    </x-blocks.main-block>
    <livewire:items-export-report.items-export-report-index :model="$market"/>
</div>

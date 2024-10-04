@props(['market' => null])

<div>
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <x-blocks.center-block>
                <flux:heading size="xl">Экспорт</flux:heading>
            </x-blocks.center-block>
            <x-blocks.center-block>
                <flux:button wire:click="export">Экспортировать</flux:button>
            </x-blocks.center-block>
            <livewire:items-export-report.items-export-report-index :model="$market"/>
        </flux:card>
    </x-blocks.main-block>
</div>

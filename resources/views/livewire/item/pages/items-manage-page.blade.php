<div>
    <x-layouts.header name="Товары" />
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('items', ['page' => 'list'])}}" :active="$page === 'list'">Список
            </x-links.tab-link>
            <x-links.tab-link href="{{route('items', ['page' => 'manage'])}}" :active="$page === 'manage'">Управление
            </x-links.tab-link>
        </x-navigate-pages>
        <x-blocks.center-block>
            <x-layouts.title name="Экспорт"/>
        </x-blocks.center-block>
        <x-blocks.center-block>
            <x-success-button wire:click="export">Экспортировать</x-success-button>
        </x-blocks.center-block>
        <livewire:items-export-report.items-export-report-index :model="auth()->user()"/>
    </x-layouts.main-container>
    <x-layouts.main-container>
        <x-blocks.center-block>
            <x-layouts.title name="Создайте новые товары или обновите старые"/>
        </x-blocks.center-block>
        <x-blocks.center-block>
            <x-success-button wire:click="downloadTemplate">Скачать шаблон</x-success-button>
        </x-blocks.center-block>
        <x-file-block action="import"/>
        <livewire:items-import-report.items-import-report-index :model="auth()->user()"/>
    </x-layouts.main-container>
</div>

<x-layouts.module-index-layout :modules="$modules">
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('orders.index', ['page' => 'main'])}}" :active="$page === 'main'">Основное
            </x-links.tab-link>
            <x-links.tab-link href="{{route('orders.index', ['page' => 'states'])}}" :active="$page === 'states'">Не
                менять
                статус
            </x-links.tab-link>
        </x-navigate-pages>
        <x-blocks.main-block>
            <x-blocks.center-block>
                <x-layouts.title name="Экспорт"/>
            </x-blocks.center-block>
            <x-blocks.center-block>
                <x-secondary-button wire:click="export">Экспортировать</x-secondary-button>
            </x-blocks.center-block>
            <x-blocks.center-block>
                <x-layouts.title name="Загрузить товары"/>
            </x-blocks.center-block>
            <x-file-block action="import"/>
        </x-blocks.main-block>
    </x-layouts.main-container>
</x-layouts.module-index-layout>

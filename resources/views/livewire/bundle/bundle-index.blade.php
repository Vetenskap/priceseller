<div>
    <x-layouts.header name="Комплекты"/>
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('bundles.index', ['page' => 'list'])}}" :active="$page === 'list'">Список
            </x-links.tab-link>
            <x-links.tab-link href="{{route('bundles.index', ['page' => 'manage'])}}" :active="$page === 'manage'">Управление
            </x-links.tab-link>
            <x-links.tab-link href="{{route('bundles.index', ['page' => 'plural'])}}" :active="$page === 'plural'">Таблица множественности
            </x-links.tab-link>
        </x-navigate-pages>
    </x-layouts.main-container>
</div>

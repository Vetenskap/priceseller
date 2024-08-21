<div>
    <x-layouts.header name="Товары"/>
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('items', ['page' => 'list'])}}" :active="$page === 'list'">Список
            </x-links.tab-link>
            <x-links.tab-link href="{{route('items', ['page' => 'manage'])}}" :active="$page === 'manage'">Управление
            </x-links.tab-link>
        </x-navigate-pages>
    </x-layouts.main-container>
</div>

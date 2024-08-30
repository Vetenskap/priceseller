<div>
    <x-layouts.header name="Комплекты"/>
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('bundles.index', ['page' => 'list'])}}" :active="$page === 'list'">Список
            </x-links.tab-link>
            <x-links.tab-link href="{{route('bundles.index', ['page' => 'manage'])}}" :active="$page === 'manage'">
                Управление
            </x-links.tab-link>
            <x-links.tab-link href="{{route('bundles.index', ['page' => 'plural'])}}" :active="$page === 'plural'">
                Таблица множественности
            </x-links.tab-link>
        </x-navigate-pages>
        <x-blocks.main-block>
            <x-layouts.title name="Список"/>
        </x-blocks.main-block>
        @if($bundles->count() > 0)
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Код"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Наименование"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Последнее обновление"/>
                    </x-table.table-child>
                </x-table.table-header>
                @foreach($bundles->sortByDesc('updated_at') as $bundle)
                    <x-table.table-item :status="session('selected-bundle') === $bundle->getKey() ? 0 : -1">
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$bundle->code"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$bundle->name"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$bundle->updated_at->diffForHumans()"/>
                        </x-table.table-child>
                    </x-table.table-item>
                @endforeach
            </x-table.table-layout>
            <x-blocks.main-block>
                {{ $bundles->withQueryString()->links() }}
            </x-blocks.main-block>
        @else
            <x-blocks.main-block>
                <x-information>Сейчас у вас нет комплектов</x-information>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>

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
        <x-blocks.center-block>
            <x-layouts.title name="Экспорт"/>
        </x-blocks.center-block>
        <x-blocks.center-block>
            <x-success-button wire:click="exportBundles">Экспортировать</x-success-button>
        </x-blocks.center-block>
        @if($bundlesExportReports->count() > 0)
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Статус"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Начало"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Конец"/>
                    </x-table.table-child>
                    <x-table.table-child>

                    </x-table.table-child>
                    <x-table.table-child>

                    </x-table.table-child>
                </x-table.table-header>
                @foreach($bundlesExportReports->sortByDesc('updated_at') as $report)
                    <x-table.table-item wire:key="{{$report->getKey()}}" :status="$report->status">
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$report->message"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$report->created_at"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text
                                :name="$report->status !== 2 ? $report->updated_at->diffForHumans() : ''"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            @if($report->status === 0)
                                <x-secondary-button wire:click="downloadBundleExport({{json_encode($report->id)}})">
                                    Скачать
                                </x-secondary-button>
                            @endif
                        </x-table.table-child>
                        <x-table.table-child>
                            @if($report->status !== 2)
                                <x-danger-button wire:click="destroyBundleExport({{json_encode($report->id)}})">
                                    Удалить
                                </x-danger-button>
                            @endif
                        </x-table.table-child>
                    </x-table.table-item>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-titles.sub-title name="История пуста"/>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
    <x-layouts.main-container>
        <x-blocks.center-block>
            <x-layouts.title name="Создайте новые комплекты или обновите старые"/>
        </x-blocks.center-block>
        <x-blocks.center-block>
            <x-success-button wire:click="downloadBundlesTemplate">Скачать шаблон</x-success-button>
        </x-blocks.center-block>
        <x-file-block action="importBundles"/>
        @if($bundlesImportReports->count() > 0)
            <x-table.table-layout>
                <x-table.table-header>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Статус"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Создано"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Не создано"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Обновлено"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Удалено"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Начало"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text name="Конец"/>
                    </x-table.table-child>
                    <x-table.table-child>

                    </x-table.table-child>
                </x-table.table-header>
                @foreach($bundlesImportReports->sortByDesc('updated_at') as $report)
                    <x-table.table-item wire:key="{{$report->getKey()}}" :status="$report->status">
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$report->message"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$report->correct"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$report->error"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$report->updated"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$report->deleted"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text :name="$report->created_at"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            <x-layouts.simple-text
                                :name="$report->status !== 2 ? $report->updated_at->diffForHumans() : ''"/>
                        </x-table.table-child>
                        <x-table.table-child>
                            @if($report->status !== 2)
                                <x-danger-button wire:click="destroyBundleImport({{json_encode($report->id)}})">
                                    Удалить
                                </x-danger-button>
                            @endif
                        </x-table.table-child>
                    </x-table.table-item>
                @endforeach
            </x-table.table-layout>
        @else
            <x-blocks.main-block>
                <x-titles.sub-title name="История пуста"/>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</div>

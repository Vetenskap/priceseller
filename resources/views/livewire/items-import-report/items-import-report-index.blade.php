<div>
    @if($itemsImportReports->count() > 0)
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
            @foreach($itemsImportReports->sortByDesc('updated_at') as $report)
                <x-table.table-item wire:key="{{$report->getKey()}}" :status="$report->status">
                    <x-table.table-child>
                        <a href="{{route('items-import-report-edit', ['report' => $report->id])}}">
                            <x-layouts.simple-text :name="$report->message"/>
                        </a>
                    </x-table.table-child>
                    <x-table.table-child>
                        <a href="{{route('items-import-report-edit', ['report' => $report->id])}}">
                            <x-layouts.simple-text :name="$report->correct"/>
                        </a>
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
                            <x-danger-button wire:click="deleteImport({{$report}})">Удалить</x-danger-button>
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
</div>

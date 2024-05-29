<div>
    <x-blocks.main-block>
        <x-layouts.title name="Отчёты"/>
    </x-blocks.main-block>
    @if($reports->count() > 0)
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
            </x-table.table-header>
            @foreach($reports->sortByDesc('updated_at') as $report)
                <x-table.table-item :status="$report->status">
                    <x-table.table-child>
                        <a href="{{route('supplier-report-edit', ['supplier' => $supplier->id, 'report' => $report->id])}}">
                            <x-layouts.simple-text :name="$report->message"/>
                        </a>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$report->created_at"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$report->status !== 2 ? $report->updated_at : ''"/>
                    </x-table.table-child>
                </x-table.table-item>
            @endforeach
        </x-table.table-layout>
    @endif
</div>

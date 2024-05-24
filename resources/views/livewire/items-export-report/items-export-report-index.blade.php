<div>
    @if($itemsExportReport->count() > 0)
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
            @foreach($itemsExportReport->sortByDesc('updated_at') as $report)
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
                            <x-secondary-button wire:click="download({{$report}})">Скачать</x-secondary-button>
                        @endif
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-danger-button wire:click="destroy({{$report}})">Удалить</x-danger-button>
                    </x-table.table-child>
                </x-table.table-item>
            @endforeach
        </x-table.table-layout>
    @else
        <x-blocks.main-block>
            <x-titles.sub-title name="История пуста" />
        </x-blocks.main-block>
    @endif
</div>

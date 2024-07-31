<div>
    <x-layouts.header :name="'Отчёт по выгрузке за ' . $report->created_at"/>
    <x-layouts.actions>
        <a href="{{url()->previous()}}"><x-secondary-button>Назад</x-secondary-button></a>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-blocks.flex-block>
            <x-layouts.simple-text name="Статус: {{$report->message}}" />
            <x-layouts.simple-text name="Начало: {{$report->created_at}}" />
            <x-layouts.simple-text name="Конец: {{$report->updated_at}}" />
        </x-blocks.flex-block>
        <x-layouts.title name="Логи" />
        <x-blocks.flex-block-end class="justify-end">
            <x-success-button wire:click="unloadAllLogs">Выгрузить все логи</x-success-button>
        </x-blocks.flex-block-end>
        <x-table.table-layout>
            <x-table.table-header>
                <x-table.table-child>
                    <x-layouts.simple-text name="Дата" />
                </x-table.table-child>
                <x-table.table-child>
                    <x-layouts.simple-text name="Сообщение" />
                </x-table.table-child>
            </x-table.table-header>
            @foreach($report->logs()->where('level', 'info')->get()->sortByDesc('updated_at') as $log)
                <x-table.table-item>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$log->updated_at" />
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$log->message" />
                    </x-table.table-child>
                </x-table.table-item>
            @endforeach
        </x-table.table-layout>
    </x-layouts.main-container>
    <div wire:loading wire:target="unloadAllLogs">
        <x-loader/>
    </div>
</div>

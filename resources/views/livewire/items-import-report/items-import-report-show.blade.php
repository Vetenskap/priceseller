<div>
    <x-layouts.header :name="'Отчёт по импорту №' . $report->id"/>
    <x-layouts.actions>
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-blocks.flex-block>
            <x-layouts.simple-text name="Создано: {{$report->correct}}" />
            <x-layouts.simple-text name="Не создано: {{$report->error}}" />
            <x-layouts.simple-text name="Статус: {{$report->message}}" />
            <x-layouts.simple-text name="Начало: {{$report->created_at}}" />
            <x-layouts.simple-text name="Конец: {{$report->updated_at}}" />
        </x-blocks.flex-block>
        <x-layouts.title name="Не созданные товары" />
        <x-table.table-layout>
            <x-table.table-header>
                <x-table.table-child>
                    <x-layouts.simple-text name="Строка" />
                </x-table.table-child>
                <x-table.table-child>
                    <x-layouts.simple-text name="Поле" />
                </x-table.table-child>
                <x-table.table-child>
                    <x-layouts.simple-text name="Ошибки" />
                </x-table.table-child>
                <x-table.table-child>
                    <x-layouts.simple-text name="Значения" />
                </x-table.table-child>
            </x-table.table-header>
            @foreach($report->badItems as $badItem)
                <x-table.table-item wire:key="{{$badItem->getKey()}}">
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$badItem->row" />
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$badItem->attribute" />
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-json-text :json="$badItem->errors" />
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-json-text :json="$badItem->values" />
                    </x-table.table-child>
                </x-table.table-item>
            @endforeach
        </x-table.table-layout>
    </x-layouts.main-container>
</div>

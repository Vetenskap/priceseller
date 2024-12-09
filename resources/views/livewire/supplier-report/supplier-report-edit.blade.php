<div>
    <x-layouts.header :name="'Отчёт по выгрузке за ' . $report->created_at"/>
    <x-layouts.actions>
        <flux:button
            variant="danger"
            wire:click="destroy"
            wire:confirm="Вы действительно хотите удалить этот отчет?"
        >Удалить</flux:button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:card>
                <div class="flex gap-6">
                    <flux:subheading>Статус: {{$report->message}}</flux:subheading>
                    <flux:subheading>Начало: {{$report->created_at}}</flux:subheading>
                    <flux:subheading>Конец: {{$report->updated_at}}</flux:subheading>
                </div>
            </flux:card>
        </x-blocks.main-block>
        <x-blocks.main-block>
            <flux:card>
                <flux:heading size="xl">Логи</flux:heading>
                <div class="flex justify-end">
                    <flux:button wire:click="unloadAllLogs">Выгрузить все логи</flux:button>
                </div>
                <flux:table :paginate="$this->logs">
                    <flux:columns>
                        <flux:column sortable :sorted="$sortBy === 'level'" :direction="$sortDirection"
                                     wire:click="sort('level')">Статус</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                                             wire:click="sort('updated_at')">Дата</flux:column>
                        <flux:column>Сообщение</flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach($this->logs as $log)
                            <flux:row :key="$log->getKey()">
                                <flux:cell>
                                    <flux:badge size="sm" :color="$log->level == 'info' ? 'sky' : 'red'" inset="top bottom">{{$log->level == 'info' ? 'Инфо' : 'Ошибка'}}</flux:badge>
                                </flux:cell>
                                <flux:cell>
                                    {{$log->updated_at}}
                                </flux:cell>
                                <flux:cell>
                                    {{$log->message}}
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            </flux:card>
        </x-blocks.main-block>
    </x-layouts.main-container>
</div>

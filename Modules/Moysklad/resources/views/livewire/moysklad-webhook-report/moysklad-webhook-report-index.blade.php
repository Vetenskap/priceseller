<div>
    @if($this->reports->count() > 0)
        <flux:table :paginate="$this->reports">
            <flux:columns>
                <flux:column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                             wire:click="sort('status')">Статус
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'payload'" :direction="$sortDirection"
                             wire:click="sort('payload')">Данные
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'exception'" :direction="$sortDirection"
                             wire:click="sort('exception')">Ошибка
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                             wire:click="sort('created_at')">Дата начала обработки
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                             wire:click="sort('updated_at')">Дата конца обработки
                </flux:column>
            </flux:columns>
            <flux:rows>
                @foreach($this->reports as $report)
                    <flux:row :key="$report->getKey()">
                        <flux:cell>
                            <flux:badge size="sm" :color="$report->status ? 'red' : 'lime'">
                                {{$report->status ? 'Не обработано' : 'Обработано'}}
                            </flux:badge>
                        </flux:cell>
                        <flux:cell>{{$report->payload}}</flux:cell>
                        <flux:cell>{{$report->exception}}</flux:cell>
                        <flux:cell>{{$report->created_at}}</flux:cell>
                        <flux:cell>{{$report->updated_at}}</flux:cell>
                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    @endif
</div>

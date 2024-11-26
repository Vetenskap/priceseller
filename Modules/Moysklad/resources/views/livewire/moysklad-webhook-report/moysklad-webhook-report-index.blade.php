<div>
    @if($this->reports->count() > 0)
        <flux:button wire:click="repeatAll">Повторить попытку обработки всех необратанных</flux:button>
        <flux:table :paginate="$this->reports">
            <flux:columns>
                <flux:column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                             wire:click="sort('status')">Статус
                </flux:column>
                <flux:column>Обработано событий
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
                        <flux:cell>{{$report->events()->where('status', true)->count()}}
                            /{{$report->events()->count()}}</flux:cell>
                        <flux:cell>
                            <flux:textarea readonly>{{$report->payload}}</flux:textarea>
                        </flux:cell>
                        <flux:cell>
                            <flux:textarea readonly>{{$report->exception}}</flux:textarea>
                        </flux:cell>
                        <flux:cell>{{$report->created_at}}</flux:cell>
                        <flux:cell>{{$report->updated_at}}</flux:cell>
                        <flux:cell>
                            <flux:tooltip content="Повторить обработку">
                                <flux:button
                                    icon="arrow-up-tray"
                                    wire:click="repeat({{json_encode($report->getKey())}})"
                                    wire:target="repeat({{json_encode($report->getKey())}})"
                                />
                            </flux:tooltip>
                        </flux:cell>
                        <flux:cell>
                            <flux:button icon="eye" size="sm"
                                         :href="route('moysklad.webhooks.reports.show', ['report' => $report])"/>
                        </flux:cell>
                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    @endif
</div>

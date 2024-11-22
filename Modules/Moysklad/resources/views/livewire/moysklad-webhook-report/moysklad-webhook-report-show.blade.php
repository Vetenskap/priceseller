<div>
    <x-layouts.header name="Отчет по Webhook #{{$report->getKey()}}"/>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:heading size="xl">Информация по вебхуку</flux:heading>
                <flux:badge size="sm" :color="$report->status ? 'red' : 'lime'">
                    {{$report->status ? 'Не обработано' : 'Обработано'}}
                </flux:badge>
                <div class="flex gap-6">
                    <flux:cell>
                        <flux:textarea readonly label="Данные">{{$report->payload}}</flux:textarea>
                    </flux:cell>
                    <flux:cell>
                        <flux:textarea readonly label="Ошибка">{{$report->exception}}</flux:textarea>
                    </flux:cell>
                </div>
            </flux:card>
        </x-blocks.main-block>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:heading size="xl">События</flux:heading>
                <flux:table :paginate="$this->events">
                    <flux:columns>
                        <flux:column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                                     wire:click="sort('status')">Статус</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'event'" :direction="$sortDirection"
                                     wire:click="sort('event')">Данные</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'exception'" :direction="$sortDirection"
                                     wire:click="sort('exception')">Ошибка</flux:column>
                        <flux:column>Товар/Комплект</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                                     wire:click="sort('created_at')">Дата начала обработки</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                                     wire:click="sort('updated_at')">Дата конца обработки</flux:column>
                    </flux:columns>
                    <flux:rows>
                        @foreach($this->events as $event)
                            <flux:row :key="$event->getKey()">
                                <flux:cell>
                                    <flux:badge size="sm" :color="$report->status ? 'lime' : 'red'">
                                        {{$event->message}}
                                    </flux:badge>
                                </flux:cell>
                                <flux:cell><flux:textarea readonly>{{$event->event}}</flux:textarea></flux:cell>
                                <flux:cell><flux:textarea readonly>{{$event->exception}}</flux:textarea></flux:cell>
                                <flux:cell>
                                    @if($report->itemable)
                                        <flux:link :href="$report->itemable instanceof \App\Models\Item ? route('item-edit', ['item' => $report->itemable]) : route('bundle-edit', ['bundle' => $report->itemable])">{{$report->itemable->code}}</flux:link>
                                    @endif
                                </flux:cell>
                                <flux:cell>{{$event->created_at}}</flux:cell>
                                <flux:cell>{{$event->updated_at}}</flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            </flux:card>
        </x-blocks.main-block>
    </x-layouts.main-container>
</div>

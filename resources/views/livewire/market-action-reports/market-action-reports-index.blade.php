<x-blocks.main-block>
    <flux:card>
        <flux:table :paginate="$this->reports">
            <flux:columns>
                <flux:column>Статус</flux:column>
                <flux:column>Действие</flux:column>
                <flux:column>Начало</flux:column>
                <flux:column>Конец</flux:column>
            </flux:columns>
            <flux:rows>
                @foreach($this->reports as $report)
                    <flux:row :key="$report->getKey()">
                        <flux:cell>
                            <flux:badge size="sm"
                                        :color="$report->status == 2 ? 'yellow' : ($report->status == 1 ? 'red' : 'lime')"
                                        inset="top bottom">{{ $report->message }}</flux:badge>
                        </flux:cell>
                        <flux:cell>{{$report->action}}</flux:cell>
                        <flux:cell>{{$report->created_at}}</flux:cell>
                        <flux:cell>{{$report->updated_at}}</flux:cell>
                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    </flux:card>
</x-blocks.main-block>

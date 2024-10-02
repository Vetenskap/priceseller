<div>
    <x-blocks.main-block>
        <flux:heading size="xl">Отчёты</flux:heading>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <flux:table :paginate="$this->reports">

            <flux:columns>
                <flux:column>Статус</flux:column>
                <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Начало</flux:column>
                <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection" wire:click="sort('updated_at')">Конец</flux:column>
            </flux:columns>

            <flux:rows>
                @foreach($this->reports as $report)
                    <flux:row :key="$report->getKey()">
                        <flux:cell>
                            <flux:badge size="sm" :color="$report->status == 2 ? 'yellow' : ($report->status == 1 ? 'red' : 'lime')" inset="top bottom">{{ $report->message }}</flux:badge>
                        </flux:cell>
                        <flux:cell variant="strong">{{$report->created_at}}</flux:cell>
                        <flux:cell variant="strong">{{$report->updated_at}}</flux:cell>
                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    </x-blocks.main-block>
</div>

<x-blocks.main-block>
    <flux:card class="space-y-6">
        <flux:heading size="xl">Автоматическая выгрузка остатков по времени</flux:heading>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Добавление нового времени</flux:heading>
            <flux:input.group>
                <flux:input placeholder="Время" type="time" wire:model="time"/>

                <flux:button icon="plus" wire:click="store">Добавить</flux:button>
            </flux:input.group>
        </flux:card>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Список</flux:heading>
            @if($this->times->isNotEmpty())
                <flux:table :paginate="$this->times">
                    <flux:columns>
                        <flux:column>Время</flux:column>
                    </flux:columns>
                    <flux:rows>
                        @foreach($this->times as $time)
                            <flux:row :key="$time->getKey()">
                                <flux:cell>{{$time->time}}</flux:cell>
                                <flux:cell align="right">
                                    <flux:button size="sm" variant="danger" icon="trash"
                                                 wire:click="destroy({{ json_encode($time->getKey()) }})"
                                                 wire:target="destroy({{ json_encode($time->getKey()) }})"
                                                 wire:confirm="Вы действительно хотите удалить это время?"
                                    />
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @endif
        </flux:card>
    </flux:card>
</x-blocks.main-block>

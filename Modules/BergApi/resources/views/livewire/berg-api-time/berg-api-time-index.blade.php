<div>
    @if(!($bergApi->times()->count() >= 3))
        <x-blocks.main-block>
            <flux:card>
                <flux:input.group>
                    <flux:input placeholder="Время" type="time" wire:model="time"/>

                    <flux:button icon="plus" wire:click="store">Добавить</flux:button>
                </flux:input.group>
            </flux:card>
        </x-blocks.main-block>
    @endif
    <x-blocks.main-block>
        <flux:card>
            <flux:table>
                <flux:columns>
                    <flux:column>Время</flux:column>
                </flux:columns>
                <flux:rows>
                    @foreach($bergApi->times as $time)
                        <flux:row :key="$time->getKey()">
                            <flux:cell>{{$time->time}}</flux:cell>
                            <flux:cell align="right">
                                <flux:button size="sm" icon="trash" variant="danger" wire:click="destroy({{$time->getKey()}})" wire:target="destroy({{$time->getKey()}})" />
                            </flux:cell>
                        </flux:row>
                    @endforeach
                </flux:rows>
            </flux:table>
        </flux:card>
    </x-blocks.main-block>
</div>

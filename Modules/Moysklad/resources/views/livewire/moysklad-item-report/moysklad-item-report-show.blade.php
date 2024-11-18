<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:heading size="lg">Фильтры</flux:heading>
                <div class="flex flex-wrap gap-6">
                    <div wire:loading>
                        <flux:input icon-trailing="loading" wire:model.live.debounce.2s="filters.data" label="Данные" disabled/>
                    </div>
                    <div wire:loading.remove>
                        <flux:input wire:model.live.debounce.2s="filters.data" label="Данные" />
                    </div>
                </div>
            </flux:card>
        </x-blocks.main-block>
        <x-blocks.main-block>
            <flux:card>
                <flux:table :paginate="$this->items">
                    <flux:columns>
                        <flux:column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                                     wire:click="sort('status')">Статус</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'exception'" :direction="$sortDirection"
                                     wire:click="sort('exception')">Ошибка</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'data'" :direction="$sortDirection"
                                     wire:click="sort('data')">Данные</flux:column>
                    </flux:columns>
                    <flux:rows>
                        @foreach($this->items as $item)
                            <flux:row :key="$item->getKey()">
                                <flux:cell>
                                    <flux:badge :color="$item->status === 2 ? 'yellow' : ($item->status === 1 ? 'red' : 'lime')">{{$item->message}}</flux:badge>
                                </flux:cell>
                                <flux:cell>
                                    <flux:textarea>{{$item->exception}}</flux:textarea>
                                </flux:cell>
                                <flux:cell>
                                    <flux:textarea>{{$item->data}}</flux:textarea>
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            </flux:card>
        </x-blocks.main-block>
    </x-layouts.main-container>
</div>

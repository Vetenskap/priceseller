<div>
    @if($this->itemsExportReport->count() > 0)
        <flux:table :paginate="$this->itemsExportReport">
            <flux:columns>
                <flux:column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                             wire:click="sort('status')">Статус
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                             wire:click="sort('created_at')">Начало
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                             wire:click="sort('updated_at')">Конец
                </flux:column>
            </flux:columns>

            <flux:rows>
                @foreach ($this->itemsExportReport as $itemExportReport)
                    <flux:row :key="$itemExportReport->id">
                        <flux:cell>
                            <flux:badge size="sm"
                                        :color="$itemExportReport->status == 2 ? 'yellow' : ($itemExportReport->status == 1 ? 'red' : 'lime')"
                                        inset="top bottom">{{ $itemExportReport->message }}</flux:badge>
                        </flux:cell>

                        <flux:cell variant="strong">{{ $itemExportReport->created_at }}</flux:cell>

                        <flux:cell variant="strong">{{ $itemExportReport->updated_at }}</flux:cell>

                        @if($itemExportReport->status != 2)
                            <flux:cell align="right">
                                <flux:icon.arrow-down-tray
                                    wire:click="download({{ json_encode($itemExportReport->getKey()) }})"
                                    wire:loading.remove
                                    wire:target="download({{ json_encode($itemExportReport->getKey()) }})"
                                    class="cursor-pointer hover:text-gray-800"/>
                                <flux:icon.loading wire:loading wire:target="download({{ json_encode($itemExportReport->getKey()) }})"/>
                            </flux:cell>

                            <flux:cell align="right">
                                <flux:icon.trash wire:click="destroy({{ json_encode($itemExportReport->getKey()) }})"
                                                 wire:loading.remove
                                                 wire:target="destroy({{ json_encode($itemExportReport->getKey()) }})"
                                                 wire:confirm="Вы действительно хотите удалить этот отчет?"
                                                 class="cursor-pointer hover:text-red-400"/>
                                <flux:icon.loading wire:loading wire:target="destroy({{ json_encode($itemExportReport->getKey()) }})"/>
                            </flux:cell>
                        @endif

                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    @else
        <flux:subheading>История пуста</flux:subheading>
    @endif
</div>

<div>
    <x-blocks.center-block>
        <flux:heading size="xl">Экспорт</flux:heading>
    </x-blocks.center-block>
    <x-blocks.center-block>
        <flux:button wire:click="export">Экспортировать</flux:button>
    </x-blocks.center-block>
    <x-blocks.main-block>
        @if($this->warehousesItemsExportReports->count() > 0)
            <flux:table :paginate="$this->warehousesItemsExportReports">
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
                    @foreach ($this->warehousesItemsExportReports as $report)
                        <flux:row :key="$report->id">
                            <flux:cell>
                                <flux:badge size="sm"
                                            :color="$report->status == 2 ? 'yellow' : ($report->status == 1 ? 'red' : 'lime')"
                                            inset="top bottom">{{ $report->message }}</flux:badge>
                            </flux:cell>

                            <flux:cell variant="strong">{{ $report->created_at }}</flux:cell>

                            <flux:cell variant="strong">{{ $report->updated_at }}</flux:cell>

                            @if($report->status != 2)
                                <flux:cell align="right">
                                    <flux:icon.arrow-down-tray
                                        wire:click="download({{ json_encode($report->getKey()) }})"
                                        wire:loading.remove
                                        wire:target="download({{ json_encode($report->getKey()) }})"
                                        class="cursor-pointer hover:text-gray-800"/>
                                    <flux:icon.loading wire:loading wire:target="download({{ json_encode($report->getKey()) }})"/>
                                </flux:cell>

                                <flux:cell align="right">
                                    <flux:icon.trash wire:click="destroy({{ json_encode($report->getKey()) }})"
                                                     wire:loading.remove
                                                     wire:target="destroy({{ json_encode($report->getKey()) }})"
                                                     wire:confirm="Вы действительно хотите удалить этот отчет?"
                                                     class="cursor-pointer hover:text-red-400"/>
                                    <flux:icon.loading wire:loading wire:target="destroy({{ json_encode($report->getKey()) }})"/>
                                </flux:cell>
                            @endif

                        </flux:row>
                    @endforeach
                </flux:rows>
            </flux:table>
        @else
            <flux:subheading>История пуста</flux:subheading>
        @endif
    </x-blocks.main-block>
</div>

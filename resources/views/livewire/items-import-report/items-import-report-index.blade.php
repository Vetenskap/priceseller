<div>
    @if($this->itemsImportReports->count() > 0)
        <flux:table :paginate="$this->itemsImportReports">
            <flux:columns>
                <flux:column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                             wire:click="sort('status')">Статус
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'correct'" :direction="$sortDirection"
                             wire:click="sort('correct')">Создано
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'error'" :direction="$sortDirection"
                             wire:click="sort('error')">Не создано
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'updated'" :direction="$sortDirection"
                             wire:click="sort('updated')">Обновлено
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'deleted'" :direction="$sortDirection"
                             wire:click="sort('deleted')">Удалено
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                             wire:click="sort('created_at')">Начало
                </flux:column>
                <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                             wire:click="sort('updated_at')">Конец
                </flux:column>
            </flux:columns>

            <flux:rows>
                @foreach ($this->itemsImportReports as $report)
                    <flux:row :key="$report->id">
                        <flux:cell>
                            <flux:badge size="sm"
                                        :color="$report->status == 2 ? 'yellow' : ($report->status == 1 ? 'red' : 'lime')"
                                        inset="top bottom">{{ $report->message }}</flux:badge>
                        </flux:cell>

                        <flux:cell variant="strong">{{ $report->correct }}</flux:cell>
                        <flux:cell variant="strong">{{ $report->error }}</flux:cell>
                        <flux:cell variant="strong">{{ $report->updated }}</flux:cell>
                        <flux:cell variant="strong">{{ $report->deleted }}</flux:cell>
                        <flux:cell variant="strong">{{ $report->created_at }}</flux:cell>
                        <flux:cell variant="strong">{{ $report->updated_at }}</flux:cell>

                        @if($report->status != 2)
                            <flux:cell align="right">
                                <flux:icon.trash wire:click="destroy({{ json_encode($report->getKey()) }})"
                                                 wire:loading.remove
                                                 wire:target="destroy({{ json_encode($report->getKey()) }})"
                                                 wire:confirm="Вы действительно хотите удалить этот отчет?"
                                                 class="cursor-pointer hover:text-red-400"/>
                                <flux:icon.loading wire:loading
                                                   wire:target="destroy({{ json_encode($report->getKey()) }})"/>
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

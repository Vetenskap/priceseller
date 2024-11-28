<div>
    <x-layouts.header name="Комплекты"/>
    <x-layouts.main-container>
        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="list" icon="list-bullet">Список</flux:tab>
                    <flux:tab name="manage" icon="cog-6-tooth">Управление</flux:tab>
                    <flux:tab name="plural" icon="table-cells">Таблица множественности</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="list">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Фильтры</flux:heading>
                        <div class="flex flex-wrap gap-6">
                            <flux:input wire:model.live.debounce.2s="filters.name" label="Наименование"/>
                            <flux:input wire:model.live.debounce.2s="filters.code" label="Код комлекта"/>
                            <flux:input wire:model.live.debounce.2s="filters.items.code" label="Код товара"/>
                        </div>
                    </flux:card>
                </x-blocks.main-block>
                <x-blocks.main-block>
                    @if($this->bundles->count() > 0)
                        <flux:table :paginate="$this->bundles">
                            <flux:columns>
                                <flux:column>Код</flux:column>
                                <flux:column>Наименование</flux:column>
                                <flux:column>Последнее обновление</flux:column>
                            </flux:columns>

                            <flux:rows>
                                @foreach($this->bundles as $bundle)
                                    <flux:row :key="$bundle->getKey()">
                                        <flux:cell>
                                            {{$bundle->code}}
                                        </flux:cell>
                                        <flux:cell>
                                            {{\Illuminate\Support\Str::limit($bundle->name, 50)}}
                                        </flux:cell>
                                        <flux:cell variant="strong">
                                            {{$bundle->updated_at}}
                                        </flux:cell>
                                        <flux:cell>
                                            <flux:button icon="pencil-square" size="sm"
                                                         :href="route('bundles.edit', ['bundle' => $bundle->getKey()])"/>
                                        </flux:cell>
                                        @if($this->user()->can('delete-bundles'))
                                            <flux:cell>
                                                <flux:button icon="trash" variant="danger" size="sm"
                                                             wire:click="destroy({{json_encode($bundle->getKey())}})"
                                                             wire:target="destroy({{json_encode($bundle->getKey())}})"
                                                             wire:confirm="Вы действительно хотите удалить этот комплект?"
                                                />
                                            </flux:cell>
                                        @endif
                                    </flux:row>
                                @endforeach
                            </flux:rows>
                        </flux:table>
                    @else
                        <flux:subheading>Сейчас у вас нет комплектов</flux:subheading>
                    @endif
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="manage">
                @if($this->user()->can('view-bundles'))
                    <livewire:bundles-export-report.bundles-export-report-index/>
                @endif
                @if($this->user()->can('create-bundles') && $this->user()->can('update-bundles') && $this->user()->can('delete-bundles'))
                    <livewire:bundles-import-report.bundles-import-report-index/>
                @endif
            </flux:tab.panel>
            <flux:tab.panel name="plural">
                @if($this->user()->can('update-bundles'))
                    <livewire:bundle-items-export-report.bundle-items-export-report-index/>
                    <livewire:bundle-items-import-report.bundle-items-import-report-index/>
                @endif
            </flux:tab.panel>
        </flux:tab-group>
    </x-layouts.main-container>
</div>

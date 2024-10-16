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
                                        <flux:cell>
                                            <flux:button icon="trash" variant="danger" size="sm"
                                                         wire:click="destroy({{$bundle->getKey()}})"
                                                         wire:target="destroy({{$bundle->getKey()}})"
                                                         wire:confirm="Вы действительно хотите удалить этот комплект?"
                                            />
                                        </flux:cell>
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
                <livewire:bundles-export-report.bundles-export-report-index/>
                <livewire:bundles-import-report.bundles-import-report-index/>
            </flux:tab.panel>
            <flux:tab.panel name="plural">
                <livewire:bundle-items-export-report.bundle-items-export-report-index/>
                <livewire:bundle-items-import-report.bundle-items-import-report-index/>
            </flux:tab.panel>
        </flux:tab-group>
    </x-layouts.main-container>
</div>

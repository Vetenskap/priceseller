<div>
    <x-layouts.header name="Товары"/>
    <x-layouts.main-container>
        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="list" icon="list-bullet">Список</flux:tab>
                    <flux:tab name="manage" icon="cog-6-tooth">Управление</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="list">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Фильтры</flux:heading>
                        <div class="flex flex-wrap gap-6">
                            <flux:input wire:model.live.debounce.2s="filters.name" label="Наименование"/>
                            <flux:input wire:model.live.debounce.2s="filters.code" label="Код"/>
                            <flux:input wire:model.live.debounce.2s="filters.article" label="Артикул"/>
                            <flux:select variant="listbox" placeholder="Выберите опцию..."
                                         wire:model.live.debounce.2s="filters.updated" label="Товар был обновлён">
                                <flux:option value="1">Да</flux:option>
                                <flux:option value="0">Нет</flux:option>
                            </flux:select>
                            <flux:select variant="listbox" placeholder="Выберите опцию..."
                                         wire:model.live.debounce.2s="filters.unload_wb" label="Выгружать на ВБ">
                                <flux:option value="1">Да</flux:option>
                                <flux:option value="0">Нет</flux:option>
                            </flux:select>
                            <flux:select variant="listbox" placeholder="Выберите опцию..."
                                         wire:model.live.debounce.2s="filters.unload_ozon" label="Выгружать на ОЗОН">
                                <flux:option value="1">Да</flux:option>
                                <flux:option value="0">Нет</flux:option>
                            </flux:select>
                            <flux:select variant="combobox" placeholder="Выберите опцию..." label="Поставщик"
                                         wire:model.live.debounce.2s="filters.supplier_id">

                                @foreach($user->suppliers as $supplier)
                                    <flux:option :value="$supplier->getKey()">{{$supplier->name}}</flux:option>
                                @endforeach
                            </flux:select>
                            @foreach($this->currentUser()->itemAttributes as $attribute)
                                @if($attribute->type === 'boolean')
                                    <flux:select variant="combobox" placeholder="Выберите опцию..." label="{{$attribute->name}}"
                                                 wire:model.live.debounce.2s="filters.attributes.{{$attribute->id}}">

                                        <flux:option value="1">Да</flux:option>
                                        <flux:option value="0">Нет</flux:option>
                                    </flux:select>
                                @else
                                    <flux:input type="{{$attribute->type}}" wire:model.live.debounce.2s="filters.attributes.{{$attribute->id}}" label="{{$attribute->name}}"/>
                                @endif
                            @endforeach
                        </div>
                    </flux:card>
                </x-blocks.main-block>
                <x-blocks.main-block>
                    @if($this->items->count() > 0)
                        <flux:table :paginate="$this->items">
                            <flux:columns>
                                <flux:column sortable :sorted="$sortBy === 'code'" :direction="$sortDirection"
                                             wire:click="sort('code')">Код
                                </flux:column>
                                <flux:column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                                             wire:click="sort('name')">Наименование
                                </flux:column>
                                <flux:column sortable :sorted="$sortBy === 'supplier_id'" :direction="$sortDirection"
                                             wire:click="sort('supplier_id')">Поставщик
                                </flux:column>
                                <flux:column sortable :sorted="$sortBy === 'updated'" :direction="$sortDirection"
                                             wire:click="sort('updated')">Был обновлён
                                </flux:column>
                                <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                                             wire:click="sort('updated_at')">Последнее обновление
                                </flux:column>
                            </flux:columns>

                            <flux:rows>
                                @foreach ($this->items as $item)
                                    <flux:row :key="$item->getKey()"
                                              class="{{session('selected-item') == $item->getKey() ? 'bg-blue-200' : ''}}">
                                        <flux:cell class="flex items-center gap-3">
                                            {{ $item->code }}
                                        </flux:cell>

                                        <flux:cell
                                            class="whitespace-nowrap">{{ \Illuminate\Support\Str::limit($item->name, 50) }}</flux:cell>

                                        <flux:cell class="whitespace-nowrap">{{ $item->supplier->name }}</flux:cell>

                                        <flux:cell>
                                            <flux:badge size="sm" :color="$item->updated ? 'lime' : 'red'"
                                                        inset="top bottom">{{$item->updated ? 'Да' : 'Нет'}}</flux:badge>
                                        </flux:cell>

                                        <flux:cell variant="strong">{{ $item->updated_at }}</flux:cell>

                                        <flux:cell align="right">
                                            <flux:button :href="route('item-edit', ['item' => $item->getKey()])"
                                                         icon="pencil-square" size="sm"/>
                                        </flux:cell>

                                        @if($this->user()->can('delete-items'))
                                            <flux:cell align="right">
                                                <flux:button icon="trash" variant="danger" size="sm"
                                                             wire:click="destroy({{ json_encode($item->getKey()) }})"
                                                             wire:target="destroy({{ json_encode($item->getKey()) }})"
                                                             wire:confirm="Вы действительно хотите удалить этот товар?"
                                                />
                                            </flux:cell>
                                        @endif

                                    </flux:row>
                                @endforeach
                            </flux:rows>
                        </flux:table>
                    @else
                        <flux:subheading>Сейчас у вас нет товаров</flux:subheading>
                    @endif
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="manage">
                @if($this->user()->can('view-items'))
                    <x-blocks.main-block>
                        <flux:card class="space-y-6">
                            <x-blocks.center-block>
                                <flux:heading size="xl">Экспорт</flux:heading>
                            </x-blocks.center-block>
                            <x-blocks.center-block>
                                <flux:button wire:click="export">Экспортировать</flux:button>
                            </x-blocks.center-block>
                            <livewire:items-export-report.items-export-report-index :model="$this->currentUser()"/>
                        </flux:card>
                    </x-blocks.main-block>
                @endif
                @if($this->user()->can('create-items') && $this->user()->can('update-items') && $this->user()->can('delete-items'))
                        <x-blocks.main-block>
                            <flux:card class="space-y-6">
                                <x-blocks.center-block>
                                    <flux:heading size="xl">Создайте новые товары или обновите старые</flux:heading>
                                </x-blocks.center-block>
                                <x-blocks.center-block>
                                    <flux:button wire:click="downloadTemplate">Скачать шаблон</flux:button>
                                </x-blocks.center-block>
                                <x-file-block action="import"/>
                                <livewire:items-import-report.items-import-report-index :model="$this->currentUser()"/>
                            </flux:card>
                        </x-blocks.main-block>
                @endif
            </flux:tab.panel>
        </flux:tab-group>
    </x-layouts.main-container>
</div>

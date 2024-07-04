<div>
    <x-layouts.header name="Склады"/>
    <x-layouts.actions>
        <x-success-button wire:click="add">Добавить</x-success-button>
    </x-layouts.actions>
    @if($showCreateForm)
        <x-layouts.main-container>
            <x-blocks.flex-block-end>
                <x-inputs.input-with-label name="name"
                                           type="text"
                                           field="form.name"
                >Наименование
                </x-inputs.input-with-label>
                <x-success-button wire:click="create">Добавить</x-success-button>
            </x-blocks.flex-block-end>
        </x-layouts.main-container>
    @endif
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link :href="route('warehouses.index', ['page' => 'list'])" name="Список"
                              :active="$page === 'list'" wire:navigate.hover/>
            <x-links.tab-link :href="route('warehouses.index', ['page' => 'stocks'])" name="Управление остатками"
                              :active="$page === 'stocks'" wire:navigate.hover/>
        </x-navigate-pages>
        @switch($page)
            @case('list')
                @if($warehouses->count() > 0)
                    <x-table.table-layout>
                        <x-table.table-header>
                            <x-table.table-child>
                                <x-layouts.simple-text name="Наименование"/>
                            </x-table.table-child>
                            <x-table.table-child>

                            </x-table.table-child>
                        </x-table.table-header>
                        @foreach($warehouses as $warehouse)
                            <x-table.table-item wire:key="{{$warehouse->getKey()}}">
                                <x-table.table-child>
                                    <a href="{{route('warehouses.edit', ['warehouse' => $warehouse->getKey()])}}">
                                        <x-layouts.simple-text :name="$warehouse->name"/>
                                    </a>
                                </x-table.table-child>
                                <x-table.table-child>
                                    <x-danger-button wire:click="destroy({{$warehouse}})">Удалить</x-danger-button>
                                </x-table.table-child>
                            </x-table.table-item>
                        @endforeach
                    </x-table.table-layout>
                @else
                    <x-blocks.main-block>
                        <x-layouts.simple-text name="Сейчас у вас нет складов"/>
                    </x-blocks.main-block>
                @endif
                @break
            @case('stocks')
                <x-layouts.main-container>
                    <x-blocks.main-block>
                        <x-layouts.title name="Экспорт"/>
                    </x-blocks.main-block>
                    <x-blocks.center-block>
                        <x-secondary-button wire:click="export">Экспортировать</x-secondary-button>
                    </x-blocks.center-block>
                    <livewire:warehouses-items-export.warehouses-items-export-index :model="auth()->user()"/>
                </x-layouts.main-container>
                @break
        @endswitch
    </x-layouts.main-container>
    @if($page === 'stocks')
        <x-layouts.main-container>
            <x-blocks.main-block>
                <x-layouts.title name="Загрузить новые остатки"/>
            </x-blocks.main-block>
            <form wire:submit="import">
                <div
                    x-data="{ uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false"
                    x-on:livewire-upload-cancel="uploading = false"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                >
                    <x-blocks.main-block>
                        <x-file-input wire:model="file"/>
                    </x-blocks.main-block>

                    <x-blocks.main-block x-show="uploading">
                        <x-file-progress x-bind:style="{ width: progress + '%' }"/>
                    </x-blocks.main-block>

                    @if($file)
                        <x-blocks.main-block class="text-center">
                            <x-success-button wire:click="import">Загрузить</x-success-button>
                        </x-blocks.main-block>
                    @endif
                </div>
            </form>
            <livewire:warehouses-items-import.warehouses-items-import-index :model="auth()->user()" />
        </x-layouts.main-container>
    @endif
    <div wire:loading wire:target="export, import">
        <x-loader/>
    </div>
</div>

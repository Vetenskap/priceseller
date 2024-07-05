@props(['market' => null, 'items' => null, 'statusFilters' => null])

<div>
    <x-blocks.main-block>
        <x-layouts.title name="Создание/Обновление связей и комиссий"/>
    </x-blocks.main-block>
    <x-blocks.flex-block class="justify-center">
        <x-success-button wire:click="downloadTemplate">Скачать шаблон</x-success-button>
    </x-blocks.flex-block>
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
                <x-file-input wire:model="file" wire:loading.attr="disabled" wire:target="import"/>
            </x-blocks.main-block>

            <x-blocks.main-block x-show="uploading">
                <x-file-progress x-bind:style="{ width: progress + '%' }"/>
            </x-blocks.main-block>

            <x-blocks.center-block>
                @error('file')
                {{ $message }}
                @enderror
            </x-blocks.center-block>

            <x-blocks.main-block class="text-center" wire:loading.remove x-show="$wire.file">
                <x-success-button>Загрузить</x-success-button>
            </x-blocks.main-block>
        </div>
    </form>

    @isset($slot)
        <x-layouts.title name="Комиссии"/>
        <x-titles.sub-title name="Комиссии по умолчанию"/>
        <x-blocks.flex-block>
            {{$slot}}
        </x-blocks.flex-block>
    @endisset
    <x-blocks.main-block>
        <x-secondary-button wire:click="relationshipsAndCommissions">Загрузить связи и комиссии
        </x-secondary-button>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <x-danger-button wire:click="clearRelationships">Очистить связи</x-danger-button>
    </x-blocks.main-block>
    <livewire:items-import-report.items-import-report-index :model="$market"/>
    <x-blocks.main-block>
        <x-layouts.title name="Все связи"/>
    </x-blocks.main-block>
    <x-blocks.main-block>
        <x-titles.sub-title name="Фильтры"/>
    </x-blocks.main-block>
    <x-blocks.flex-block>
        <x-inputs.input-with-label name="external_code"
                                   type="text"
                                   field="filters.external_code"
        >Внешний код
        </x-inputs.input-with-label>
        <x-inputs.input-with-label name="code"
                                   type="text"
                                   field="filters.code"
        >Ваш код
        </x-inputs.input-with-label>
        <x-dropdown-select name="status"
                           field="filters.status"
                           :options="$statusFilters"
                           value="status">
            Статус
        </x-dropdown-select>
    </x-blocks.flex-block>
    @if($items->count() > 0)
        <x-table.table-layout>
            <x-table.table-header>
                <x-table.table-child>
                    <x-layouts.simple-text name="Внешний код"/>
                </x-table.table-child>
                <x-table.table-child>
                    <x-layouts.simple-text name="Ваш код"/>
                </x-table.table-child>
                <x-table.table-child>
                    <x-layouts.simple-text name="Статус"/>
                </x-table.table-child>
                <x-table.table-child>
                    <x-layouts.simple-text name="Последнее изменение"/>
                </x-table.table-child>
            </x-table.table-header>
            @foreach($items as $item)
                <x-table.table-item wire:key="{{$item->getKey()}}">
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$item->external_code"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$item->code"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$item->message"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$item->updated_at->diffForHumans()"/>
                    </x-table.table-child>
                </x-table.table-item>
            @endforeach
        </x-table.table-layout>
    @else
        <x-titles.sub-title name="Нет связей"/>
    @endif
</div>

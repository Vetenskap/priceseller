<div>
    <x-layouts.header :name="$form->name"/>
    <x-layouts.actions>
        <a href="{{route('warehouses.index')}}" wire:navigate.hover>
            <x-primary-button>Назад</x-primary-button>
        </a>
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>
    <x-layouts.main-container>
        <div class="bg-white">
            <nav class="flex flex-col sm:flex-row">
                <x-links.tab-link name="Основное" :active="$selectedTab === 'main'"
                                  wire:click="$set('selectedTab', 'main')"/>
{{--                <x-links.tab-link name="Остатки" :active="$selectedTab === 'stocks'"--}}
{{--                                  wire:click="$set('selectedTab', 'stocks')"/>--}}
{{--                <x-links.tab-link name="Экспорт" :active="$selectedTab === 'export'"--}}
{{--                                  wire:click="$set('selectedTab', 'export')"/>--}}
            </nav>
        </div>
        @switch($selectedTab)
            @case('main')
                <x-blocks.flex-block-end>
                    <x-inputs.input-with-label name="name"
                                               type="text"
                                               field="form.name"
                    >Наименование
                    </x-inputs.input-with-label>
                </x-blocks.flex-block-end>
                @break
            @case('stocks')
{{--                <x-blocks.main-block>--}}
{{--                    <x-layouts.title name="Создание/Обновление остатков"/>--}}
{{--                </x-blocks.main-block>--}}
{{--                <form wire:submit="import">--}}
{{--                    <div--}}
{{--                        x-data="{ uploading: false, progress: 0 }"--}}
{{--                        x-on:livewire-upload-start="uploading = true"--}}
{{--                        x-on:livewire-upload-finish="uploading = false"--}}
{{--                        x-on:livewire-upload-cancel="uploading = false"--}}
{{--                        x-on:livewire-upload-error="uploading = false"--}}
{{--                        x-on:livewire-upload-progress="progress = $event.detail.progress"--}}
{{--                    >--}}
{{--                        <x-blocks.main-block>--}}
{{--                            <x-file-input wire:model="file" wire:loading.attr="disabled" wire:target="import"/>--}}
{{--                        </x-blocks.main-block>--}}

{{--                        <x-blocks.main-block x-show="uploading">--}}
{{--                            <x-file-progress x-bind:style="{ width: progress + '%' }"/>--}}
{{--                        </x-blocks.main-block>--}}

{{--                        <x-blocks.center-block>--}}
{{--                            @error('file')--}}
{{--                            {{ $message }}--}}
{{--                            @enderror--}}
{{--                        </x-blocks.center-block>--}}

{{--                        <x-blocks.main-block class="text-center" wire:loading.remove x-show="$wire.file">--}}
{{--                            <x-success-button>Загрузить</x-success-button>--}}
{{--                        </x-blocks.main-block>--}}
{{--                    </div>--}}
{{--                </form>--}}
                @break
            @case('export')
{{--                <x-blocks.main-block>--}}
{{--                    <x-layouts.title name="Экспорт"/>--}}
{{--                </x-blocks.main-block>--}}
{{--                <x-blocks.center-block>--}}
{{--                    <x-secondary-button wire:click="export">Экспортировать</x-secondary-button>--}}
{{--                </x-blocks.center-block>--}}
{{--                <livewire:items-export-report.items-export-report-index :model="$warehouse"/>--}}
                @break
        @endswitch
    </x-layouts.main-container>
    <div wire:loading
         wire:target="import">
        <x-loader/>
    </div>
</div>

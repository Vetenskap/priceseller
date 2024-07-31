<div>
    <x-layouts.header name="Мой склад"/>
    <x-navigate-pages>
        <x-links.tab-link href="{{route('moysklad.index', ['page' => 'main'])}}" :active="$page === 'main'">Основное
        </x-links.tab-link>
        <x-links.tab-link href="{{route('moysklad.index', ['page' => 'warehouses'])}}" :active="$page === 'warehouses'">Склады
        </x-links.tab-link>
        <x-links.tab-link href="{{route('moysklad.index', ['page' => 'items'])}}" :active="$page === 'items'">Товары
        </x-links.tab-link>
    </x-navigate-pages>
    @if($page === 'main')
        <x-layouts.module-container x-show="$wire.page == 'main'">
            <x-blocks.main-block>
                <x-success-button wire:click="save">Сохранить</x-success-button>
            </x-blocks.main-block>
            <x-blocks.main-block>
                <x-inputs.input-with-label name="api_key" field="form.api_key" type="text">АПИ ключ</x-inputs.input-with-label>
            </x-blocks.main-block>
        </x-layouts.module-container>
    @endif
    @if($page === 'warehouses')
        <livewire:moysklad::moysklad-warehouse.moysklad-warehouse-index :moysklad="$form->moysklad"/>
    @endif
    @if($page === 'items')
        <x-layouts.module-container>
            <x-blocks.main-block>
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
            </x-blocks.main-block>
        </x-layouts.module-container>
    @endif
</div>

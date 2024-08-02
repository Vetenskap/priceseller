<x-layouts.module-container>
    <x-blocks.main-block>
        <x-layouts.title name="Атрибуты"/>
    </x-blocks.main-block>
    <x-blocks.flex-block>
        <x-dropdown-select name="code" field="code" :options="$assortmentAttributes">Код клиента</x-dropdown-select>
        <x-dropdown-select name="article" field="article" :options="$assortmentAttributes">Артикул</x-dropdown-select>
        <x-dropdown-select name="brand" field="brand" :options="$assortmentAttributes">Бренд</x-dropdown-select>
        <x-dropdown-select name="name" field="name" :options="$assortmentAttributes">Наименование</x-dropdown-select>
        <x-dropdown-select name="multiplicity" field="multiplicity" :options="$assortmentAttributes">Кратность отгрузки</x-dropdown-select>
        <x-dropdown-select name="unload_ozon" field="unload_ozon" :options="$assortmentAttributes">Не выгружать на Озон</x-dropdown-select>
        <x-dropdown-select name="unload_wb" field="unload_wb" :options="$assortmentAttributes">Не выгружать на ВБ</x-dropdown-select>
    </x-blocks.flex-block>
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
    <div wire:loading wire:target="import">
        <x-loader/>
    </div>
</x-layouts.module-container>

@props(['action'])
<form wire:submit="{{$action}}">
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

        <x-blocks.center-block>
            @error('file')
            {{ $message }}
            @enderror
        </x-blocks.center-block>

        <x-blocks.center-block x-show="$wire.file">
            <flux:button type="submit">Загрузить</flux:button>
        </x-blocks.center-block>
    </div>
</form>

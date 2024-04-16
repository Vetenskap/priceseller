<div>
    <x-layouts.header name="Товары"/>
    <x-layouts.main-container>
        <x-layouts.title name="Создайте новые товары или обновите старые"/>
        <form wire:submit="save">
            <div
                x-data="{ uploading: false, progress: 0 }"
                x-on:livewire-upload-start="uploading = true"
                x-on:livewire-upload-finish="uploading = false"
                x-on:livewire-upload-cancel="uploading = false"
                x-on:livewire-upload-error="uploading = false"
                x-on:livewire-upload-progress="progress = $event.detail.progress"
            >
                <x-blocks.main-block>
                    <label for="dropzone-file"
                           class="mx-auto cursor-pointer flex w-full max-w-lg flex-col items-center rounded-xl border-2 border-dashed border-blue-400 bg-white p-6 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>

                        <h2 class="mt-4 text-xl font-medium text-gray-700 tracking-wide">Загрузите файл</h2>

                        <p class="mt-2 text-gray-500 tracking-wide">Загрузите или переместите свой файл XLS, XLSX, CSV и TXT. </p>

                        <input id="dropzone-file" type="file" class="hidden" wire:model="table"/>
                    </label>
                </x-blocks.main-block>

                <x-blocks.main-block x-show="uploading">
                    <div class="mx-auto h-4 relative w-96 rounded-full overflow-hidden">
                        <div class=" w-full h-full bg-gray-200 absolute "></div>
                        <div class=" h-full bg-yellow-400 sm:bg-green-500 absolute" x-bind:style="{ width: progress + '%' }"></div>
                    </div>
                </x-blocks.main-block>

                @if($table)
                    <x-blocks.main-block class="text-center">
                        <x-success-button>Загрузить</x-success-button>
                    </x-blocks.main-block>
                @endif
            </div>

        </form>
    </x-layouts.main-container>
    <x-layouts.main-container>
        @empty($items->count())
            <x-blocks.main-block>
                <x-layouts.simple-text name="Сейчас у вас нет товаров"/>
            </x-blocks.main-block>
        @endempty
        @foreach($items as $item)
            <x-table.table-item wire:key="{{$item->getKey()}}">
                <a href="{{route('item-edit', ['item' => $item->getKey()])}}">
                    <x-layouts.simple-text :name="$item->code"/>
                </a>
                <x-layouts.simple-text :name="$item->article_supplier"/>
            </x-table.table-item>
        @endforeach
    </x-layouts.main-container>
</div>

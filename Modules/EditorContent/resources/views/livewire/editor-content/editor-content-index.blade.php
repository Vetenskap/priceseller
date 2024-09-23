<x-layouts.module-index-layout :modules="$modules">
    <x-layouts.main-container>
        <!-- Поле для ввода артикула -->
        <x-blocks.flex-block>
            <x-inputs.input-with-label name="article"
                                       field="article"
                                       type="text"
            >Введите артикул
            </x-inputs.input-with-label>
            <div class="self-center">
                <x-secondary-button wire:click="search">Поиск</x-secondary-button>
            </div>
        </x-blocks.flex-block>

        <div class="rounded-lg shadow-md">
            <!-- Горизонтальная лента карточек -->
            <x-blocks.flex-block>
                @foreach($cards as $card)
                    <div wire:click="selectCard({{ $card['id'] }})"
                         class="cursor-pointer border rounded-lg p-4 shadow-md hover:bg-gray-100 transition-transform transform hover:scale-105 {{$selectedCard ? ($card['id'] == $selectedCard['id'] ? 'bg-blue-300' : 'bg-white') : 'bg-white'}}">
                        <img src="{{ $card['image'] }}" alt="image" class="w-12 h-12 object-cover mx-auto rounded-md">
                        <div class="mt-4 text-center">
                            <p class="text-sm font-medium text-gray-700">{{ $card['account'] }}</p>
                            <p class="text-xs text-gray-500">{{ $card['article'] }}</p>
                        </div>
                    </div>
                @endforeach
            </x-blocks.flex-block>

            <!-- Детальная информация по выбранной карточке -->
            @if($selectedCard)
                <div class="mt-8 bg-white p-6">
                    <x-blocks.flex-block>
                        <img src="{{ $selectedCard['image'] }}" alt="image" class="w-48 h-48 object-cover rounded-md">
                        <div class="ml-8 space-y-4">
                            <x-inputs.input-with-label name="account"
                                                       field="selectedCard.account"
                                                       type="text"
                                                       disabled
                            >Маркетплейс
                            </x-inputs.input-with-label>
                            <x-inputs.input-with-label name="article"
                                                       field="selectedCard.article"
                                                       type="text"
                                                       disabled
                            >Артикул
                            </x-inputs.input-with-label>
                        </div>
                    </x-blocks.flex-block>
                </div>
            @endif
        </div>

        @if($cards)
            <div class="mt-12">
                <x-blocks.main-block>
                    <x-layouts.title name="Редактирование карточек"/>
                </x-blocks.main-block>

                <!-- Блок с выбором фото -->
                <x-blocks.flex-block>
                    <div class="grid grid-cols-2 p-6 gap-4">
                        <div id="image-picker"
                             class="relative w-24 h-24 border-2 border-dashed rounded-lg bg-gray-50 flex items-center justify-center cursor-pointer hover:bg-gray-100 transition"
                             onclick="document.getElementById('file-input').click()">
                            <input type="file" id="file-input" accept="image/*"
                                   class="absolute inset-0 opacity-0 cursor-pointer" multiple wire:model="images">
                            <div class="text-center" id="image-placeholder">
                                <span class="text-2xl font-bold text-gray-400">+</span>
                                <p class="text-sm text-gray-500 mt-1">Фото</p>
                            </div>
                        </div>

                        @foreach($images as $image)
                            <div class="relative w-24 h-24 border rounded-lg bg-gray-50 flex items-center justify-center">
                                <img src="{{ $image->temporaryUrl() }}" class="object-cover rounded-lg w-full h-full" />
                                <button type="button" wire:click="$set('images', array_diff($images, [$image]))"
                                        class="absolute top-0 right-0 m-1 text-white bg-red-500 rounded-full w-4 h-4 flex items-center justify-center">
                                    &times;
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <!-- Ошибки загрузки файлов -->
                    @error('images.*')
                    <div class="text-red-600 mt-2 text-sm">
                        {{ $message }} <!-- Сообщение об ошибке для каждого файла -->
                    </div>
                    @enderror

                    <!-- Поля для редактирования информации карточки -->
                    <x-blocks.main-block>
                        <x-inputs.input-with-label name="offer_id"
                                                   field="offer_id"
                                                   type="text"
                        >offer_id
                        </x-inputs.input-with-label>
                        <x-inputs.input-with-label name="name"
                                                   field="name"
                                                   type="text"
                        >Наименование
                        </x-inputs.input-with-label>
                        <x-inputs.input-with-label name="depth"
                                                   field="depth"
                                                   type="number"
                        >Глубина
                        </x-inputs.input-with-label>
                        <x-inputs.input-with-label name="width"
                                                   field="width"
                                                   type="number"
                        >Ширина
                        </x-inputs.input-with-label>
                        <x-inputs.input-with-label name="height"
                                                   field="height"
                                                   type="number"
                        >Высота
                        </x-inputs.input-with-label>
                        <x-inputs.input-with-label name="dimension_unit"
                                                   field="dimension_unit"
                                                   type="text"
                        >Единица измерения размеров
                        </x-inputs.input-with-label>
                        <x-inputs.input-with-label name="weight"
                                                   field="weight"
                                                   type="number"
                        >Вес
                        </x-inputs.input-with-label>
                        <x-textarea name="Описание" value="description"/>
                    </x-blocks.main-block>
                </x-blocks.flex-block>

                <x-blocks.main-block>
                    <x-success-button>Отправить изменения</x-success-button>
                </x-blocks.main-block>
            </div>
        @endif
    </x-layouts.main-container>
</x-layouts.module-index-layout>

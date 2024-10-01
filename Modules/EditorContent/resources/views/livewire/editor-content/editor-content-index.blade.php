<x-layouts.module-index-layout :modules="$modules">
    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:card>
                <div class="flex gap-4 items-end">
                    <flux:input wire:model="article" label="Введите артикул" icon="magnifying-glass"/>
                    <flux:button wire:click="search">Поиск</flux:button>
                </div>
            </flux:card>
        </x-blocks.main-block>
        <div class="rounded-lg shadow-md">
            <x-blocks.flex-block>
                @foreach($cards as $card)
                    <flux:card
                        class="cursor-pointer max-w-sm hover:bg-gray-100 transition-transform transform hover:scale-105 {{$selectedCard ? ($card['id'] == $selectedCard['id'] ? '!bg-blue-300' : '') : ''}}"
                        wire:click="selectCard({{ $card['id'] }})">
                        <img src="{{ $card['images'][0]['file_name'] }}" alt="image"
                             class="w-12 h-12 object-cover mx-auto rounded-md">
                        <div class="mt-4 text-center text-wrap">
                            <p class="text-sm font-medium text-gray-700">{{ $card['offer_id'] }}</p>
                        </div>
                    </flux:card>
                @endforeach
            </x-blocks.flex-block>
            <!-- Детальная информация по выбранной карточке -->
            @if($selectedCard)
                <x-blocks.main-block>
                    <flux:card>
                        <x-blocks.flex-block>
                            <img src="{{ $selectedCard['images'][0]['file_name'] }}" alt="image"
                                 class="w-48 h-48 object-cover rounded-md">
                            <flux:card class="ml-8 space-y-4">
                                <flux:input wire:model="selectedCard.offer_id" label="Код клиента" readonly/>
                                <flux:input wire:model="selectedCard.name" label="Наименование" readonly/>
                                <flux:textarea wire:model="selectedCard.description" label="Описание"/>
                            </flux:card>
                        </x-blocks.flex-block>
                    </flux:card>
                </x-blocks.main-block>
            @endif
        </div>

        @if($cards)
            <x-blocks.main-block class="mt-12">
                <flux:card class="space-y-6">
                    <flux:heading size="xl">Редактирование карточек</flux:heading>

                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Категории</flux:heading>
                        <div class="flex gap-6 flex-wrap">
                            @foreach($selectedCategories as $index => $selectedCategory)
                                <flux:select variant="listbox" class="max-w-64" placeholder="Выберите категорию..."
                                             wire:model.live.debounce.1s="selectedCategories.{{ $index }}" searchable>
                                    <x-slot name="search">
                                        <flux:select.search placeholder="Поиск..."/>
                                    </x-slot>

                                    @foreach($currentAvailableCategories[$index] as $category)
                                        <flux:option
                                            value="{{ $category['description_category_id'] ?? $category['type_id'] }}">{{ $category['category_name'] ?? $category['type_name'] }}</flux:option>
                                    @endforeach
                                </flux:select>
                            @endforeach
                        </div>
                    </flux:card>

                    <flux:card class="space-y-6">
                        <div class="flex gap-6">
                            <div class="flex flex-wrap gap-6 max-w-sm h-fit">
                                <div id="image-picker"
                                     class="relative w-24 h-24 border-2 border-dashed rounded-lg bg-gray-50 flex items-center justify-center cursor-pointer hover:bg-gray-100 transition"
                                     onclick="document.getElementById('file-input').click()">
                                    <input type="file" id="file-input" accept="image/*"
                                           class="absolute inset-0 opacity-0 cursor-pointer" multiple
                                           wire:model="images">
                                    <div class="text-center" id="image-placeholder">
                                        <span class="text-2xl font-bold text-gray-400">+</span>
                                        <p class="text-sm text-gray-500 mt-1">Фото</p>
                                    </div>
                                </div>

                                @foreach($images as $image)
                                    <div
                                        class="relative w-24 h-24 border rounded-lg bg-gray-50 flex items-center justify-center">
                                        <img src="{{ $image->temporaryUrl() }}"
                                             class="object-cover rounded-lg w-full h-full"/>
                                        <button type="button" wire:click="$set('images', array_diff($images, [$image]))"
                                                class="absolute top-0 right-0 m-1 text-white bg-red-500 rounded-full w-4 h-4 flex items-center justify-center">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Поля для редактирования информации карточки -->
                            <div class="space-y-6">
                                <flux:heading size="lg">Основные атрибуты</flux:heading>
                                <flux:input wire:model="offer_id" label="Код клиента"/>
                                <flux:input wire:model="name" label="Наименование"/>
                                <flux:input wire:model="depth" label="Глубина"/>
                                <flux:input wire:model="width" label="Ширина"/>
                                <flux:input wire:model="height" label="Высота"/>
                                <flux:input wire:model="dimension_unit" label="Единица измерения размеров"/>
                                <flux:input wire:model="weight" label="Вес"/>
                                <flux:textarea wire:model="description" label="Описание"/>
                            </div>

                            <div class="space-y-6">
                                <flux:heading size="lg">Атрибуты категории</flux:heading>
                                @foreach($categoryAttributes as $attribute)
                                    @if($attribute['dictionary_id'])
                                        <flux:card class="space-y-6">
                                            @if($attribute['is_required'])
                                                <flux:field>
                                                    <flux:label badge="Обязательное">{{$attribute['name']}}</flux:label>

                                                    <flux:input type="{{$attribute['type']}}" wire:model.live.debounce.5s="categoryAttributesDictionarySearch.{{$attribute['id']}}" required />

                                                    <flux:error name="categoryAttributesDictionarySearch.{{$attribute['id']}}" />
                                                </flux:field>
                                            @else
                                                <div class="flex gap-4 items-end">
                                                    <flux:input wire:model="categoryAttributesValues.{{$attribute['id']}}" type="{{$attribute['type']}}" label="{{$attribute['name']}}" />
                                                </div>
                                            @endif
                                            <flux:select variant="listbox" class="max-w-64" placeholder="Выберите {{$attribute['name']}}..."
                                                         wire:model="categoryAttributesValuesSelected.{{$attribute['id']}}" searchable>
                                                <x-slot name="search">
                                                    <flux:select.search placeholder="Поиск..."/>
                                                </x-slot>

                                                @if(isset($categoryAttributesDictionary[$attribute['id']]))
                                                    @foreach($categoryAttributesDictionary[$attribute['id']] as $value)
                                                        <flux:option
                                                            value="{{ $value['id'] }}">{{ $value['value'] }}</flux:option>
                                                    @endforeach
                                                @endif
                                            </flux:select>
                                        </flux:card>
                                    @else
                                        @if($attribute['type'] === 'textarea')
                                            <flux:textarea wire:model="categoryAttributesValues.{{$attribute['id']}}" label="{{$attribute['name']}}" rows="1"/>
                                        @else
                                            @if($attribute['is_required'])
                                                <flux:field>
                                                    <flux:label badge="Обязательное">{{$attribute['name']}}</flux:label>

                                                    <flux:input type="{{$attribute['type']}}" wire:model="categoryAttributesValues.{{$attribute['id']}}" required />

                                                    <flux:error name="categoryAttributesValues.{{$attribute['id']}}" />
                                                </flux:field>
                                            @else
                                                <flux:input wire:model="categoryAttributesValues.{{$attribute['id']}}" type="{{$attribute['type']}}" label="{{$attribute['name']}}" />
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <flux:button wire:click="save">Отправить изменения</flux:button>
                    </flux:card>
                </flux:card>
            </x-blocks.main-block>
        @endif
    </x-layouts.main-container>
</x-layouts.module-index-layout>

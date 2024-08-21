<div>
    <x-blocks.main-block>
        <button wire:click="openDialog" class="bg-blue-500 text-white px-4 py-2 rounded">Добавить новое поле</button>
    </x-blocks.main-block>

    @if ($showDialog)
        <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 z-50">
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Ввести данные</h3>
                    <div class="mt-2">
                        <form wire:submit.prevent="submit">
                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Наименование')" />
                                <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <x-dropdown-select name="type"
                                                   field="type"
                                                   :options="config('app.item_attribute_types')"
                                                   value="name"
                                                   option-name="label"
                                                   required
                                >
                                    Тип поля
                                </x-dropdown-select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>
                            <div class="flex justify-end">
                                <button type="button" wire:click="closeDialog" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Закрыть</button>
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Подтвердить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

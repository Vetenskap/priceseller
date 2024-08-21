@props(['items', 'currentId', 'name', 'optionValue' => 'id', 'optionName' => 'name', 'field', 'currentItems' => collect(), 'currentItemsOptionValue' => 'id'])

@php
    if (is_array($items)) {
        array_unshift($items, [$optionName => 'Нет', $optionValue => null]);
    } else {
        $items->prepend([$optionName => 'Нет', $optionValue => null]);
    }
@endphp

<div class="w-[250px]">
    <div>
        <x-input-label for="{{$name}}" :value="$slot"/>
    </div>
    <div class="flex">
        <div class="relative group dropdown-container">
            <button class="text-nowrap overflow-hidden dropdown-button w-[250px] inline-flex h-[42px] items-center px-4 justify-between font-medium text-gray-700 rounded-md shadow-sm focus:outline-none border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 {{$errors->has($field) ? ' border-red-500' : '' }}">
                <span class="mr-2" title="{{collect($items)->where($optionValue, $currentId)->first() ? collect($items)->where($optionValue, $currentId)->first()[$optionName] : "Выберите опцию"}}">{{\Illuminate\Support\Str::limit(collect($items)->where($optionValue, $currentId)->first() ? collect($items)->where($optionValue, $currentId)->first()[$optionName] : "Выберите опцию", 18)}}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2 -mr-1" viewBox="0 0 20 20"
                     fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                          d="M6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                          clip-rule="evenodd"/>
                </svg>
            </button>
            <div
                class="dropdown-menu w-[250px] hidden max-h-52 absolute mt-2 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 p-1 space-y-1 overflow-y-scroll dark:bg-gray-900">
                <input
                    class="search-input block w-full px-4 py-2 text-gray-800 border rounded-md  border-gray-300 focus:outline-none"
                    type="text" placeholder="Поиск" autocomplete="off">
                @foreach(collect($items)->whereNotIn($optionValue, $currentItems->pluck($currentItemsOptionValue)->add($currentId)->values())->toArray() as $item)
                    <div
                        class=" block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 active:bg-blue-100 cursor-pointer rounded-md"
                        wire:click="$set({{json_encode($field)}}, {{json_encode($item[$optionValue])}})">{{$item[$optionName]}}</div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="h-[20px]">
        <x-input-error :messages="collect($errors->get($field))->first()"/>
    </div>
</div>

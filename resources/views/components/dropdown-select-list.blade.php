@props(['name', 'field', 'options'])

<div>
    <div class="flex items-center">
        <x-input-label for="{{$name}}" class="mr-3">{{$slot}}</x-input-label>
    </div>
    <select {{ $attributes->merge([
    "class" => "w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm",
    "name" => $name,
    "id" => $name,
]) }}
            wire:model.live.debounce.1s="{{$field}}">
        <option wire:key="{{null}}" value="{{ null }}">Выберите опцию</option>
        @foreach($options as $timezone => $name)
            <option wire:key="{{$timezone}}"
                    value="{{ $timezone }}">{{ $name }}</option>
        @endforeach
    </select>
</div>

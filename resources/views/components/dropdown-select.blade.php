@props(['name', 'field', 'options', 'value' => 'id', 'optionName' => 'name'])

<div>
    <div class="flex items-center">
        <x-input-label for="{{$name}}" class="mr-3">{{$slot}}</x-input-label>
    </div>
    <select {{ $attributes->merge([
    "class" => "w-[208px] border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm",
    "name" => $name,
    "id" => $name,
]) }}
    wire:model.live.debounce.1s="{{$field}}">
        @foreach($options as $option)
            <option wire:key="{{$option[$value]}}"
                    value="{{ $option[$value] }}">{{ $option[$optionName] }}</option>
        @endforeach
    </select>
</div>

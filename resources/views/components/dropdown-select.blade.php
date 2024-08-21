@props(['name', 'field', 'options', 'value' => 'id', 'optionName' => 'name'])

<div class="w-[250px]">
    <div>
        <x-input-label for="{{$name}}" class="mr-3">{{$slot}}</x-input-label>
    </div>

    <select {{ $attributes->merge([
    "class" => "w-[250px] border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" . ($errors->has($field) ? ' border-red-500' : '' ),
    "name" => $name,
    "id" => $name,
]) }}
    wire:model.live.debounce.1s="{{$field}}">
        <option wire:key="{{null}}" value="{{ null }}">Выберите опцию</option>
        @foreach($options as $option)
            <option wire:key="{{$option[$value]}}"
                    value="{{ $option[$value] }}">{{ $option[$optionName] }}</option>
        @endforeach
    </select>

    <div class="h-[20px]">
        <x-input-error :messages="collect($errors->get($field))->first()"/>
    </div>
</div>

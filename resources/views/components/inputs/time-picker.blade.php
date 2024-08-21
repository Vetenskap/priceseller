@props(['name', 'field'])
<div class="w-[250px]">
    <div class="flex">
        <x-input-label for="{{$name}}" :value="$slot"/>
    </div>

    <input {!! $attributes->merge([
    'class' => 'w-[250px] block border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm' . ($errors->has($field) ? ' border-red-500' : '' ),
    'name' => $name,
    'id' => $name,
    'type' => 'time'
    ]) !!} wire:model.live.debounce.1s="{{$field}}" />

    <div class="mb-5">
        <x-input-error :messages="collect($errors->get($field))->first()" class="mt-2"/>
    </div>
</div>

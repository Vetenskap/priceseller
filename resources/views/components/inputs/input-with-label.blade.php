@props(['name', 'field', 'tooltip' => null])
<div class="mt-4">
    <div class="flex items-center">
        <x-input-label for="{{$name}}" :value="$slot"/>
        @if($tooltip)
            <x-tooltip :text="$tooltip"/>
        @endif
    </div>

    <input {!! $attributes->merge([
    'class' => 'block border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm' . ($errors->has($field) ? ' border-red-500' : '' ),
    'name' => $name,
    'id' => $name,
    ]) !!} wire:model.live.debounce.1s="{{$field}}" />

    <x-input-error :messages="$errors->get($field)" class="mt-2"/>
</div>

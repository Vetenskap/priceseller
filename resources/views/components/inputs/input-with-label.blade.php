@props(['name', 'field', 'tooltip' => null, 'required' => false])
<div class="w-[250px]">
    <div class="flex">
        @if($tooltip)
            <div class="mr-6">
                <x-tooltip :text="$tooltip"/>
            </div>
        @endif
        <x-input-label for="{{$name}}" :value="$slot"/>
    </div>

    <div class="relative">
        <input {!! $attributes->merge([
            'class' => 'w-[250px] block border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm' . ($errors->has($field) ? ' border-red-500' : '' ),
            'name' => $name,
            'id' => $name,
            ]) !!} wire:model.live.debounce.3s="{{$field}}"
        />
        @if($required)
            <span class="absolute inset-y-0 right-3 flex items-center text-red-500 text-2xl pointer-events-none">*</span>
        @endif
    </div>

    <div class="mb-5">
        <x-input-error :messages="collect($errors->get($field))->first()" class="mt-2"/>
    </div>
</div>

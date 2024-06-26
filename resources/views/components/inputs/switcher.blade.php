@props(['checked', 'disabled' => false])
<div>
    <label class="relative inline-flex cursor-pointer items-center">
        <input {!! $attributes->merge(["id"=>"switch", "type"=>"checkbox", "class"=>"peer sr-only"]) !!} {{$checked ? 'checked' : ''}} {{$disabled ? 'disabled' : ''}} wire:model="checked"/>
        <label for="switch" class="hidden"></label>
        <div class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-green-300"></div>
    </label>
</div>

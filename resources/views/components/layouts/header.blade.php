@props(['name'])
<x-slot name="header">
    <div class="flex gap-6">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __($name) }}
        </h2>
    </div>
</x-slot>

@props(['name'])
<div {{ $attributes->merge(['class' => "text-gray-900 dark:text-gray-100"]) }}>
    {{$name}}
</div>
